# Shipping System Integration Guide

This guide provides instructions for testing and using the shipping system integration with the Flutter app.

## Overview

The shipping system allows for two delivery methods:

1. **Vendor Delivery**: Vendors handle their own deliveries
2. **Aramex Courier**: Aramex handles deliveries for vendors who cannot deliver

## Testing the Integration

### Backend Testing

1. **Run Migrations**:
   ```
   php artisan migrate
   ```

2. **Test the Shipping Service**:
   ```
   php test_shipping.php
   ```

3. **Test API Endpoints**:
   - Use Postman or a similar tool to test the API endpoints:
     - `POST /api/checkout`: Place an order
     - `GET /api/orders/{orderId}/shipment`: Get shipment details
     - `POST /api/track-shipment`: Track a shipment

### Flutter App Testing

1. **Update Flutter App Configuration**:
   - Open your Flutter app's API service configuration
   - Make sure the base URL is set correctly:
     ```dart
     // For Android emulator
     final baseUrl = 'http://10.0.2.2:8000/api';
     
     // For physical devices, use your computer's IP address
     // final baseUrl = 'http://192.168.1.x:8000/api';
     ```

2. **Copy Flutter Implementation Files**:
   - Copy the files from the `flutter_implementation` directory to your Flutter app
   - Update imports and dependencies as needed

3. **Test Checkout Flow**:
   - Add products to cart
   - Proceed to checkout
   - Fill in shipping information
   - Place order
   - Verify order confirmation screen shows shipping information
   - Test tracking functionality if a tracking number is provided

## Vendor Dashboard

Vendors can manage their delivery capabilities through the vendor dashboard:

1. **Access Shipping Settings**:
   - Log in as a vendor
   - Navigate to `/vendor/shipping/settings`
   - Enable or disable delivery capability

2. **Manage Orders**:
   - Navigate to `/vendor/shipping/orders`
   - View orders that need to be shipped
   - Update shipping status (pending, processing, shipped, delivered)

## Admin Dashboard

Admins can manage the shipping system through the admin dashboard:

1. **Configure Aramex Integration**:
   - Log in as an admin
   - Navigate to `/admin/shipping/settings`
   - Configure Aramex API credentials and settings

2. **Manage Orders and Shipments**:
   - Navigate to `/admin/shipping/orders` to view all orders
   - Navigate to `/admin/shipping/shipments` to view all shipments
   - Update shipping methods and statuses
   - Track shipments

3. **Manage Vendor Shipping Capabilities**:
   - Navigate to `/admin/shipping/vendors`
   - Enable or disable delivery capability for vendors

## Aramex Integration

The system is integrated with Aramex for courier services. To use the real Aramex API:

1. **Update .env File**:
   ```
   # Aramex API Configuration
   ARAMEX_ACCOUNT_NUMBER="your_account_number"
   ARAMEX_USERNAME="your_username"
   ARAMEX_PASSWORD="your_password"
   ARAMEX_ACCOUNT_PIN="your_account_pin"
   ARAMEX_ENTITY="your_entity"
   ARAMEX_COUNTRY_CODE="your_country_code"
   
   # Aramex API Endpoints
   ARAMEX_API_MODE="Production" # Use "Test" for testing
   ARAMEX_WSDL_CREATE_SHIPMENTS="https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl"
   ARAMEX_WSDL_TRACK_SHIPMENTS="https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc?wsdl"
   
   # Shipper Information
   ARAMEX_SHIPPER_NAME="Your Name"
   ARAMEX_SHIPPER_COMPANY="Your Company"
   ARAMEX_SHIPPER_PHONE="Your Phone"
   ARAMEX_SHIPPER_EMAIL="your.email@example.com"
   ARAMEX_SHIPPER_ADDRESS_LINE1="Your Address"
   ARAMEX_SHIPPER_CITY="Your City"
   ARAMEX_SHIPPER_COUNTRY_CODE="Your Country Code"
   ```

2. **Test Aramex Integration**:
   - Create an order with a vendor that cannot deliver
   - Verify that an Aramex shipment is created
   - Track the shipment using the tracking number

## Troubleshooting

### Common Issues

1. **Images Not Showing in Flutter App**:
   - Make sure the APP_URL in .env is accessible from your device
   - For Android emulator, use `http://10.0.2.2:8000`
   - For physical devices, use your computer's IP address

2. **Aramex API Errors**:
   - Check Aramex API credentials
   - Verify that the SOAP extension is enabled in PHP
   - Check Laravel logs for detailed error messages

3. **Shipping Status Not Updating**:
   - Check database connection
   - Verify that the order exists
   - Check permissions for the user making the update

### Logs

Check the Laravel logs for detailed error messages:

```
tail -f storage/logs/laravel.log
```

## Additional Resources

- [Aramex API Documentation](https://www.aramex.com/developers/api)
- [Flutter Network Image Documentation](https://api.flutter.dev/flutter/widgets/Image/Image.network.html)
- [Laravel Queue Documentation](https://laravel.com/docs/10.x/queues) (for background jobs)

## Next Steps

1. **Implement Real-time Tracking**:
   - Add WebSocket support for real-time tracking updates
   - Implement push notifications for shipping status changes

2. **Add Multiple Courier Options**:
   - Integrate with additional courier services
   - Implement shipping cost comparison

3. **Enhance User Experience**:
   - Add estimated delivery dates
   - Implement delivery time slots
   - Add package insurance options
