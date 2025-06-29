# Shipping System Documentation

This document provides an overview of the shipping system implementation for the marketplace application, including both the Laravel backend and Flutter app integration.

## Overview

The shipping system allows for two delivery methods:

1. **Vendor Delivery**: Vendors handle their own deliveries
2. **Aramex Courier**: Aramex handles deliveries for vendors who cannot deliver

The system automatically determines the appropriate delivery method based on vendor capabilities and creates shipments accordingly.

## Database Schema

The following tables and fields have been added to support the shipping system:

### Companies Table

- `can_deliver` (boolean): Indicates whether a vendor can handle their own deliveries

### Orders Table

- `shipping_method` (string): The assigned shipping method ('vendor' or 'aramex')
- `shipping_status` (string): The current delivery status (e.g., 'pending', 'shipped', 'delivered')
- `shipping_cost` (decimal): The cost of shipping
- `tracking_number` (string): The tracking number for the shipment
- `customer_name` (string): The name of the customer
- `customer_phone` (string): The phone number of the customer
- `shipping_city` (string): The city for shipping
- `shipping_country` (string): The country for shipping

### Shipments Table

- `order_id` (foreign key): The order this shipment belongs to
- `awb_number` (string): The Aramex airwaybill / tracking number
- `status` (string): The shipment status (e.g., 'pending', 'in_transit', 'delivered')
- `shipment_details` (json): Additional shipment details from Aramex
- `tracking_history` (json): Tracking history from Aramex
- `shipped_at` (timestamp): When the shipment was shipped
- `delivered_at` (timestamp): When the shipment was delivered

### Order Items Table

- `vendor_id` (foreign key): The vendor (company) responsible for this item

## Backend Components

### Models

1. **Company Model**
   - Added `can_deliver` boolean field
   - Added appropriate casts and fillable properties

2. **Order Model**
   - Added shipping-related fields
   - Added relationship to Shipment model
   - Added methods to check vendor delivery capabilities

3. **Shipment Model**
   - Created new model for tracking shipments
   - Added relationship to Order model
   - Added methods for updating shipment status

4. **OrderItem Model**
   - Added vendor relationship
   - Updated fillable properties

### Services

1. **ShippingService**
   - `assignDelivery(Order $order)`: Determines and assigns the appropriate delivery method
   - `updateShippingStatus(Order $order, string $status)`: Updates the shipping status
   - `notifyVendors(Order $order)`: Notifies vendors about new orders

2. **AramexService**
   - `createShipment(Order $order)`: Creates a shipment via Aramex API
   - Handles all communication with the Aramex API

### Controllers

1. **CheckoutController**
   - `placeOrder(Request $request)`: Processes checkout and assigns delivery

2. **ShipmentController**
   - `getShipmentDetails($orderId)`: Gets shipment details for an order
   - `trackShipment(Request $request)`: Tracks a shipment by tracking number

### Background Jobs

1. **CreateAramexShipmentJob**
   - Handles asynchronous creation of Aramex shipments
   - Includes retry logic for failed API calls

## API Endpoints

1. **POST /api/checkout**
   - Places an order and determines shipping method
   - Requires authentication

2. **GET /api/orders/{orderId}/shipment**
   - Gets shipment details for an order
   - Requires authentication

3. **POST /api/track-shipment**
   - Tracks a shipment by tracking number
   - Public endpoint (no authentication required)

## Configuration

### Environment Variables

The following environment variables are used for Aramex integration:

```
ARAMEX_ACCOUNT_NUMBER="test_account"
ARAMEX_USERNAME="test_user"
ARAMEX_PASSWORD="test_password"
ARAMEX_ACCOUNT_PIN="test_pin"
ARAMEX_ENTITY="AE"
ARAMEX_COUNTRY_CODE="AE"
ARAMEX_SHIPPER_NAME="Store Manager"
ARAMEX_SHIPPER_COMPANY="My Store"
ARAMEX_SHIPPER_PHONE="+971501234567"
ARAMEX_SHIPPER_EMAIL="info@mystore.com"
ARAMEX_SHIPPER_ADDRESS_LINE1="123 Market St"
ARAMEX_SHIPPER_CITY="Dubai"
ARAMEX_SHIPPER_COUNTRY_CODE="AE"
```

### Services Configuration

The Aramex configuration is defined in `config/services.php`:

```php
'aramex' => [
    // API credentials
    'account_number' => env('ARAMEX_ACCOUNT_NUMBER', 'test_account'),
    'username' => env('ARAMEX_USERNAME', 'test_user'),
    'password' => env('ARAMEX_PASSWORD', 'test_password'),
    'account_pin' => env('ARAMEX_ACCOUNT_PIN', 'test_pin'),
    'entity' => env('ARAMEX_ENTITY', 'AE'),
    'country_code' => env('ARAMEX_COUNTRY_CODE', 'AE'),
    
    // API endpoints
    'wsdl_create_shipments' => env('ARAMEX_WSDL_CREATE_SHIPMENTS', 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl'),
    'wsdl_track_shipments' => env('ARAMEX_WSDL_TRACK_SHIPMENTS', 'https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc?wsdl'),
    
    // Shipper information
    'shipper_name' => env('ARAMEX_SHIPPER_NAME', 'Store Manager'),
    'shipper_company' => env('ARAMEX_SHIPPER_COMPANY', 'My Store'),
    'shipper_phone' => env('ARAMEX_SHIPPER_PHONE', '+971501234567'),
    'shipper_email' => env('ARAMEX_SHIPPER_EMAIL', 'info@mystore.com'),
    'shipper_address_line1' => env('ARAMEX_SHIPPER_ADDRESS_LINE1', '123 Market St'),
    'shipper_address_line2' => env('ARAMEX_SHIPPER_ADDRESS_LINE2', ''),
    'shipper_city' => env('ARAMEX_SHIPPER_CITY', 'Dubai'),
    'shipper_state' => env('ARAMEX_SHIPPER_STATE', ''),
    'shipper_postal_code' => env('ARAMEX_SHIPPER_POSTAL_CODE', ''),
    'shipper_country_code' => env('ARAMEX_SHIPPER_COUNTRY_CODE', 'AE'),
],
```

## Flutter Integration

For Flutter integration details, please refer to the `flutter_shipping_integration.md` file, which includes:

1. Models for Order and Shipment
2. API services for checkout and tracking
3. UI implementation for checkout, order confirmation, and tracking
4. Error handling and testing guidelines

## Vendor Dashboard Integration

Vendors can manage their delivery capabilities through the vendor dashboard:

1. Enable/disable delivery capability in their company settings
2. View and manage orders assigned to them for delivery
3. Update shipping status for orders they are delivering

## Admin Dashboard Integration

Admins can manage the shipping system through the admin dashboard:

1. View all shipments and their statuses
2. Manually assign or change shipping methods for orders
3. Track shipments and view delivery history
4. Configure Aramex integration settings

## Testing

The shipping system includes tests to verify functionality:

1. `ShippingTest`: Tests vendor delivery capability and shipping assignment
2. `test_shipping.php`: A simple script to verify the implementation

## Troubleshooting

Common issues and solutions:

1. **Shipment creation fails**: Check Aramex API credentials and connection
2. **Tracking information not updating**: Verify the tracking number format
3. **Vendor delivery not working**: Ensure the vendor has the `can_deliver` flag set to true
4. **Flutter app not showing shipping information**: Check API responses and model mapping

## Future Enhancements

Potential future enhancements to the shipping system:

1. Support for multiple courier services
2. Real-time tracking updates via webhooks
3. Shipping cost calculation based on weight, dimensions, and distance
4. Customer delivery preferences and scheduling
5. Integration with mapping services for route optimization
