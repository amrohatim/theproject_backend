In a multi-vendor Flutter+Laravel e-commerce app, the checkout process must decide whether vendors ship orders themselves or if a carrier (Aramex) handles delivery. We update the database, implement controller logic, and use Laravel services and jobs to automate this decision. If all vendors in the cart have can_deliver = true, each vendor will fulfill their part; if any vendor cannot deliver, the entire order is sent via Aramex automatically. The Aramex Shipping API (SOAP-based) can then be called to create the shipment and obtain tracking information
aramex.com
. The following guide details each step, with code examples, a flow diagram, and frontend notes.
Database Schema Updates
We first extend the database to record each vendor’s delivery ability, the chosen shipping method for each order, and to log Aramex shipments. Below are example Laravel migrations (using the Schema builder):
php
Copy
Edit
// 1. Add a `can_deliver` flag to vendors
Schema::table('vendors', function (Blueprint $table) {
    $table->boolean('can_deliver')->default(true)
          ->comment('Whether vendor can handle own deliveries');
});

// 2. Add shipping info to orders
Schema::table('orders', function (Blueprint $table) {
    $table->string('shipping_method')->default('vendor')
          ->comment("Assigned shipping method: 'vendor' or 'aramex'");
    $table->string('shipping_status')->default('pending')
          ->comment('Delivery status (e.g. pending, shipped, delivered)');
});

// 3. Create a shipments table for Aramex shipments
Schema::create('shipments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->string('awb_number')->nullable()
          ->comment('Aramex airwaybill / tracking number');
    $table->string('status')->default('pending')
          ->comment('Shipment status (e.g. pending, in_transit, delivered)');
    $table->timestamps();
});
vendors.can_deliver – Boolean flag set per vendor account.
orders.shipping_method – String ('vendor' or 'aramex') chosen during checkout.
orders.shipping_status – Current delivery status.
shipments table – Logs one Aramex shipment per order (for orders sent via Aramex), storing the AWB/tracking number and status.
With these changes, the application can mark orders and shipments appropriately.
Checkout Controller Logic
In the checkout flow (e.g. CheckoutController@placeOrder), after creating the order and its items, evaluate vendor delivery capability. A service class (e.g. ShippingService) encapsulates this logic for clarity:
php
Copy
Edit
class ShippingService
{
    public function assignDelivery(Order $order)
    {
        // Check each vendor in the order items
        $canAllDeliver = $order->items->every(function($item) {
            return $item->vendor->can_deliver;
        });

        if ($canAllDeliver) {
            // All vendors can deliver: mark as vendor shipment
            $order->shipping_method = 'vendor';
            $order->save();
            // (Optional) Notify vendors to fulfill their items here
        } else {
            // At least one vendor cannot deliver: use Aramex for the whole order
            $order->shipping_method = 'aramex';
            $order->save();
            // Dispatch an async job to create an Aramex shipment
            CreateAramexShipmentJob::dispatch($order);
        }
    }
}
In the checkout controller, use this service after order creation:
php
Copy
Edit
public function placeOrder(Request $request)
{
    // ...validate input, create Order and OrderItem models...
    
    // Determine shipping method and handle delivery
    app(ShippingService::class)->assignDelivery($order);

    // Return order confirmation (e.g. JSON response or redirect)
    return response()->json(['order_id' => $order->id]);
}
This logic ensures the shipping method is chosen automatically. If vendor delivery is chosen, the app can simply proceed to notify vendors or update order status as “awaiting shipment.” If Aramex is chosen, it immediately triggers a background job to create the shipment through Aramex’s API.
Aramex Shipment Service
Implement a service class (e.g. AramexService) to encapsulate the API call to Aramex. Aramex offers a SOAP-based Shipping API
aramex.com
. For simplicity, we show a JSON-like structure for the request; in actual code you may use PHP’s SoapClient or a Laravel package. Example payload for CreateShipments:
php
Copy
Edit
class AramexService
{
    protected $client;

    public function __construct()
    {
        // Initialize SOAP client with WSDL (test or live endpoint)
        $wsdl = config('services.aramex.wsdl_create_shipments'); 
        $this->client = new \SoapClient($wsdl);
    }

    public function createShipment(Order $order)
    {
        // Build the shipment request payload
        $shipper = [
            'Name'      => 'My Store Name',
            'Address1'  => '123 Market St',
            'City'      => 'Dubai',
            'CountryCode'=> 'AE',
            'Contact'   => [
                'PersonName'   => 'Store Manager',
                'PhoneNumber1' => '+971501234567',
            ],
        ];
        $consignee = [
            'Name'      => $order->customer_name,
            'Address1'  => $order->shipping_address,
            'City'      => $order->shipping_city,
            'CountryCode'=> $order->shipping_country,
            'Contact'   => [
                'PersonName'   => $order->customer_name,
                'PhoneNumber1' => $order->customer_phone,
            ],
        ];

        // Example request parameters
        $params = [
            'ClientInfo' => [
                'AccountNumber' => config('services.aramex.account_number'),
                'UserName'      => config('services.aramex.username'),
                'Password'      => config('services.aramex.password'),
                'AccountPin'    => config('services.aramex.account_pin'),
                'Entity'        => config('services.aramex.entity'),    // e.g. 'AE'
                'CountryCode'   => 'AE',
            ],
            'LabelInfo' => [
                'ReportID' => 9201, // Label type (Aramex defines IDs for different label formats)
            ],
            'Shipments' => [
                [
                    'Shipper'         => $shipper,
                    'Consignee'       => $consignee,
                    'Reference1'      => "Order #{$order->id}",
                    'Weight'          => ['Value' => 2, 'Unit' => 'KG'],
                    'NumberOfPieces'  => 1,
                    'Description'     => 'E-commerce Order Shipment',
                    'PaymentType'     => 'P', // 'P' = Prepaid by shipper
                    // (Optional: ServiceType, Items, etc., as needed)
                ]
            ]
        ];

        // Call the SOAP method
        $response = $this->client->CreateShipments($params);
        
        // Check response and extract AWB
        if (!empty($response->Shipments->ProcessedShipment->ShipmentNumber)) {
            $awb = $response->Shipments->ProcessedShipment->ShipmentNumber;
            // Store AWB in shipments table
            Shipment::create([
                'order_id'   => $order->id,
                'awb_number' => $awb,
                'status'     => 'created',
            ]);
        }
    }
}
This createShipment() method illustrates how to format the request. Key points: include ClientInfo (account credentials and Aramex account PIN) and at least one shipment with shipper/consignee addresses, weight, and description. The Aramex guide notes that all API calls use HTTPS and require authentication by account number and PIN
aramex.com
.
Queueable Job for Asynchronous Shipment Creation
Since the Aramex API call can take time, offload it to a queued job. Laravel’s queue system makes this easy
laravel.com
. For example:
php
Copy
Edit
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateAramexShipmentJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(AramexService $aramexService)
    {
        // Call the service to create a shipment via Aramex API
        $aramexService->createShipment($this->order);
        
        // (Optional) Update order or notify customer that shipping is arranged
        $this->order->update(['shipping_status' => 'shipped']);
    }
}
In the shipping service (see above), we dispatched this job with CreateAramexShipmentJob::dispatch($order). Laravel’s workers will then process this job in the background, creating the shipment without blocking the user’s request
laravel.com
. This improves responsiveness and reliability.
Routes and Controller Snippets
Define the checkout route (for example, in routes/web.php or routes/api.php):
php
Copy
Edit
use App\Http\Controllers\CheckoutController;

Route::post('/checkout', [CheckoutController::class, 'placeOrder']);
The CheckoutController@placeOrder method contains the order creation logic and calls the ShippingService as shown above. No additional routes are strictly needed for shipping, as the Aramex job runs automatically. However, you might add routes for viewing order/shipment status, e.g.:
php
Copy
Edit
Route::get('/orders/{order}', [OrderController::class, 'show']);
where the order details API returns shipping_method, shipping_status, and awb_number (if any).
Delivery Decision Flow Diagram


Figure: Simplified shipping process flowchart. In this customized checkout workflow, the key decision is whether all vendors can deliver. If yes, each vendor fulfills their items; if no, the order is routed to Aramex (a carrier) for delivery. Other steps (item packing, labeling, customer notifications) follow accordingly. The diagram above illustrates a typical shipping flow with a decision diamond. In our case, the decision checks the can_deliver flags. If all vendors deliver, the flow proceeds with vendor fulfillment; if any vendor cannot, we branch to using Aramex automatically.
Flutter Frontend Guidance
On the Flutter side, the checkout and order-confirmation screens should reflect the chosen delivery method:
During checkout: Display shipping information as usual. Since delivery pricing is fixed, simply show the flat shipping cost (or individual vendor shipping fees) without calculating distance-based rates. If the backend determines Aramex will be used, you might display an “Aramex Delivery” logo or note on the confirmation screen.
After order placement: When fetching order details from the API, include the shipping_method and (if available) the Aramex tracking number (awb_number). For example, show a message:
“Your order will be shipped by [Vendor Name(s)].” if shipping_method = 'vendor'.
“Your order will be shipped via Aramex.” if shipping_method = 'aramex'.
Once the job completes, you could also show the tracking number or link to Aramex tracking if provided.
Notifications/updates: Consider pushing a notification or updating the UI when the shipping_status changes (e.g. to “shipped” with a tracking number). The frontend can poll or subscribe to WebSocket events to update the order status display.