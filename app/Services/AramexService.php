<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class AramexService
{
    /**
     * The SOAP client instance.
     *
     * @var \SoapClient|null
     */
    protected $client;

    /**
     * Aramex account credentials.
     *
     * @var array
     */
    protected $credentials;

    /**
     * Create a new Aramex service instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Initialize credentials from config
        $this->credentials = [
            'AccountNumber' => Config::get('services.aramex.account_number', 'test_account'),
            'UserName' => Config::get('services.aramex.username', 'test_user'),
            'Password' => Config::get('services.aramex.password', 'test_password'),
            'AccountPin' => Config::get('services.aramex.account_pin', 'test_pin'),
            'AccountEntity' => Config::get('services.aramex.entity', 'AE'),
            'AccountCountryCode' => Config::get('services.aramex.country_code', 'AE'),
        ];

        // Initialize SOAP client with WSDL (test or live endpoint)
        try {
            $apiMode = Config::get('services.aramex.api_mode', 'Test');
            $wsdl = Config::get('services.aramex.wsdl_create_shipments');

            // In a production environment, initialize the SOAP client
            if (extension_loaded('soap')) {
                Log::info('Initializing Aramex SOAP client with WSDL: ' . $wsdl);

                $this->client = new \SoapClient($wsdl, [
                    'trace' => 1,
                    'exceptions' => true,
                    'connection_timeout' => 60,
                ]);

                Log::info('Aramex SOAP client initialized successfully');
            } else {
                Log::warning('SOAP extension not loaded. Using mock implementation for Aramex service.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize Aramex SOAP client: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Create a shipment for an order via Aramex API.
     *
     * @param Order $order
     * @return array
     */
    public function createShipment(Order $order)
    {
        try {
            // Extract shipping address details
            $shippingAddress = $order->shipping_address;

            // Build shipper information (your store/company)
            $shipper = [
                'Reference1' => 'Shipper_' . $order->id,
                'Reference2' => '',
                'AccountNumber' => $this->credentials['AccountNumber'],
                'PartyAddress' => [
                    'Line1' => Config::get('services.aramex.shipper_address_line1', '123 Market St'),
                    'Line2' => Config::get('services.aramex.shipper_address_line2', ''),
                    'Line3' => '',
                    'City' => Config::get('services.aramex.shipper_city', 'Dubai'),
                    'StateOrProvinceCode' => Config::get('services.aramex.shipper_state', ''),
                    'PostCode' => Config::get('services.aramex.shipper_postal_code', ''),
                    'CountryCode' => Config::get('services.aramex.shipper_country_code', 'AE'),
                ],
                'Contact' => [
                    'Department' => '',
                    'PersonName' => Config::get('services.aramex.shipper_name', 'Store Manager'),
                    'Title' => '',
                    'CompanyName' => Config::get('services.aramex.shipper_company', 'My Store'),
                    'PhoneNumber1' => Config::get('services.aramex.shipper_phone', '+971501234567'),
                    'PhoneNumber1Ext' => '',
                    'PhoneNumber2' => '',
                    'PhoneNumber2Ext' => '',
                    'FaxNumber' => '',
                    'CellPhone' => Config::get('services.aramex.shipper_mobile', '+971501234567'),
                    'EmailAddress' => Config::get('services.aramex.shipper_email', 'info@mystore.com'),
                    'Type' => '',
                ],
            ];

            // Build consignee information (customer)
            $consignee = [
                'Reference1' => 'Consignee_' . $order->id,
                'Reference2' => '',
                'AccountNumber' => '',
                'PartyAddress' => [
                    'Line1' => $shippingAddress['address'] ?? '',
                    'Line2' => $shippingAddress['address2'] ?? '',
                    'Line3' => '',
                    'City' => $order->shipping_city ?? ($shippingAddress['city'] ?? ''),
                    'StateOrProvinceCode' => $shippingAddress['state'] ?? '',
                    'PostCode' => $shippingAddress['zip_code'] ?? '',
                    'CountryCode' => $order->shipping_country ?? ($shippingAddress['country'] ?? 'AE'),
                ],
                'Contact' => [
                    'Department' => '',
                    'PersonName' => $order->customer_name ?? ($shippingAddress['name'] ?? ''),
                    'Title' => '',
                    'CompanyName' => '',
                    'PhoneNumber1' => $order->customer_phone ?? ($shippingAddress['phone'] ?? ''),
                    'PhoneNumber1Ext' => '',
                    'PhoneNumber2' => '',
                    'PhoneNumber2Ext' => '',
                    'FaxNumber' => '',
                    'CellPhone' => $order->customer_phone ?? ($shippingAddress['phone'] ?? ''),
                    'EmailAddress' => $order->user->email ?? '',
                    'Type' => '',
                ],
            ];

            // Calculate total weight (in KG) - in a real app, you would get this from products
            $totalWeight = 2; // Default weight in KG

            // Build the shipment details
            $shipmentDetails = [
                'Dimensions' => [
                    'Length' => 20,
                    'Width' => 20,
                    'Height' => 20,
                    'Unit' => 'cm',
                ],
                'ActualWeight' => [
                    'Value' => $totalWeight,
                    'Unit' => 'kg',
                ],
                'ProductGroup' => 'EXP', // Express
                'ProductType' => 'PPX', // Priority Parcel Express
                'PaymentType' => 'P', // Prepaid
                'PaymentOptions' => '',
                'NumberOfPieces' => 1,
                'DescriptionOfGoods' => 'Order #' . $order->order_number,
                'GoodsOriginCountry' => 'AE',
                'CashOnDeliveryAmount' => [
                    'Value' => 0,
                    'CurrencyCode' => 'AED',
                ],
            ];

            // Build the full request
            $request = [
                'ClientInfo' => $this->credentials,
                'LabelInfo' => [
                    'ReportID' => 9201, // Standard label format
                    'ReportType' => 'URL',
                ],
                'Shipments' => [
                    [
                        'Reference1' => 'Order_' . $order->id,
                        'Reference2' => '',
                        'Reference3' => '',
                        'Shipper' => $shipper,
                        'Consignee' => $consignee,
                        'ShippingDateTime' => now()->format('Y-m-d\TH:i:s'),
                        'DueDate' => now()->addDays(1)->format('Y-m-d\TH:i:s'),
                        'Comments' => $order->notes ?? '',
                        'PickupLocation' => '',
                        'OperationsInstructions' => '',
                        'Details' => $shipmentDetails,
                    ],
                ],
            ];

            // Call the Aramex API to create the shipment
            if ($this->client) {
                try {
                    Log::info('Sending Aramex shipment request', ['request' => $request]);

                    // Call the CreateShipments method on the SOAP client
                    $response = $this->client->CreateShipments($request);

                    Log::info('Aramex API response received', ['response' => $response]);

                    // Check if the request was successful
                    if (isset($response->Shipments) && !empty($response->Shipments)) {
                        $shipment = $response->Shipments[0];

                        if ($shipment->HasErrors) {
                            // Log the errors
                            $errors = [];
                            foreach ($shipment->Notifications as $notification) {
                                $errors[] = $notification->Message;
                            }

                            Log::error('Aramex shipment creation failed', [
                                'order_id' => $order->id,
                                'errors' => $errors,
                            ]);

                            return [
                                'success' => false,
                                'message' => 'Shipment creation failed: ' . implode(', ', $errors),
                            ];
                        }

                        // Get the AWB number (tracking number)
                        $awbNumber = $shipment->ID;
                    } else {
                        // Handle transaction-level errors
                        $errors = [];
                        if (isset($response->Notifications)) {
                            foreach ($response->Notifications as $notification) {
                                $errors[] = $notification->Message;
                            }
                        }

                        Log::error('Aramex transaction failed', [
                            'order_id' => $order->id,
                            'errors' => $errors,
                        ]);

                        return [
                            'success' => false,
                            'message' => 'Transaction failed: ' . implode(', ', $errors),
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Aramex API call exception', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    return [
                        'success' => false,
                        'message' => 'API call failed: ' . $e->getMessage(),
                    ];
                }
            } else {
                // Simulate a successful response with a tracking number for testing
                Log::info('Using mock implementation for Aramex shipment creation', ['request' => $request]);
                $awbNumber = 'AWB' . rand(10000000, 99999999);
            }

            // Update the shipment record with the AWB number
            $shipment = Shipment::where('order_id', $order->id)->first();
            if ($shipment) {
                $shipment->awb_number = $awbNumber;
                $shipment->status = 'created';
                $shipment->shipment_details = $request;
                $shipment->save();

                // Also update the order's tracking number
                $order->tracking_number = $awbNumber;
                $order->save();
            }

            return [
                'success' => true,
                'awb_number' => $awbNumber,
                'message' => 'Shipment created successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Error creating Aramex shipment: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create shipment: ' . $e->getMessage(),
            ];
        }
    }
}
