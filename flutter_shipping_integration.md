# Flutter Shipping Integration Guide

This guide explains how to integrate the shipping functionality into your Flutter app, including checkout, order tracking, and displaying shipping information.

## 1. API Endpoints

The Laravel backend provides the following API endpoints for shipping:

- **POST /api/checkout**: Place an order and determine shipping method
- **GET /api/orders/{orderId}/shipment**: Get shipment details for an order (authenticated)
- **POST /api/track-shipment**: Track a shipment by tracking number (public)

## 2. Models

Create the following models in your Flutter app:

### Order Model

```dart
class Order {
  final int id;
  final String orderNumber;
  final double total;
  final String status;
  final String paymentStatus;
  final String shippingMethod;
  final String shippingStatus;
  final double shippingCost;
  final String? trackingNumber;
  final List<OrderItem> items;

  Order({
    required this.id,
    required this.orderNumber,
    required this.total,
    required this.status,
    required this.paymentStatus,
    required this.shippingMethod,
    required this.shippingStatus,
    required this.shippingCost,
    this.trackingNumber,
    required this.items,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'],
      orderNumber: json['order_number'],
      total: json['total'].toDouble(),
      status: json['status'],
      paymentStatus: json['payment_status'],
      shippingMethod: json['shipping_method'],
      shippingStatus: json['shipping_status'],
      shippingCost: json['shipping_cost'].toDouble(),
      trackingNumber: json['tracking_number'],
      items: (json['items'] as List?)
          ?.map((item) => OrderItem.fromJson(item))
          .toList() ?? [],
    );
  }
}
```

### Shipment Model

```dart
class Shipment {
  final int id;
  final int orderId;
  final String? awbNumber;
  final String status;
  final DateTime? shippedAt;
  final DateTime? deliveredAt;
  final List<TrackingEvent> trackingHistory;

  Shipment({
    required this.id,
    required this.orderId,
    this.awbNumber,
    required this.status,
    this.shippedAt,
    this.deliveredAt,
    required this.trackingHistory,
  });

  factory Shipment.fromJson(Map<String, dynamic> json) {
    return Shipment(
      id: json['id'],
      orderId: json['order_id'],
      awbNumber: json['awb_number'],
      status: json['status'],
      shippedAt: json['shipped_at'] != null
          ? DateTime.parse(json['shipped_at'])
          : null,
      deliveredAt: json['delivered_at'] != null
          ? DateTime.parse(json['delivered_at'])
          : null,
      trackingHistory: (json['tracking_history'] as List?)
          ?.map((event) => TrackingEvent.fromJson(event))
          .toList() ?? [],
    );
  }
}

class TrackingEvent {
  final DateTime timestamp;
  final String status;
  final String location;

  TrackingEvent({
    required this.timestamp,
    required this.status,
    required this.location,
  });

  factory TrackingEvent.fromJson(Map<String, dynamic> json) {
    return TrackingEvent(
      timestamp: DateTime.parse(json['timestamp']),
      status: json['status'],
      location: json['location'],
    );
  }
}
```

## 3. API Services

Create a service to handle API calls:

```dart
class ShippingService {
  final dio = Dio();
  final String baseUrl = 'http://10.0.2.2:8000/api'; // For Android emulator

  // Place an order
  Future<Order> placeOrder(Map<String, dynamic> orderData) async {
    try {
      final response = await dio.post(
        '$baseUrl/checkout',
        data: orderData,
        options: Options(
          headers: {
            'Authorization': 'Bearer $token',
            'Content-Type': 'application/json',
          },
        ),
      );

      if (response.statusCode == 200) {
        return Order.fromJson(response.data['order']);
      } else {
        throw Exception('Failed to place order: ${response.statusMessage}');
      }
    } catch (e) {
      throw Exception('Failed to place order: $e');
    }
  }

  // Get shipment details
  Future<Shipment> getShipmentDetails(int orderId) async {
    try {
      final response = await dio.get(
        '$baseUrl/orders/$orderId/shipment',
        options: Options(
          headers: {
            'Authorization': 'Bearer $token',
            'Content-Type': 'application/json',
          },
        ),
      );

      if (response.statusCode == 200) {
        return Shipment.fromJson(response.data['shipment']);
      } else {
        throw Exception('Failed to get shipment details: ${response.statusMessage}');
      }
    } catch (e) {
      throw Exception('Failed to get shipment details: $e');
    }
  }

  // Track a shipment
  Future<Map<String, dynamic>> trackShipment(String trackingNumber) async {
    try {
      final response = await dio.post(
        '$baseUrl/track-shipment',
        data: {'tracking_number': trackingNumber},
        options: Options(
          headers: {
            'Content-Type': 'application/json',
          },
        ),
      );

      if (response.statusCode == 200) {
        return response.data;
      } else {
        throw Exception('Failed to track shipment: ${response.statusMessage}');
      }
    } catch (e) {
      throw Exception('Failed to track shipment: $e');
    }
  }
}
```

## 4. UI Implementation

### Checkout Screen

```dart
class CheckoutScreen extends StatefulWidget {
  final List<CartItem> cartItems;

  const CheckoutScreen({Key? key, required this.cartItems}) : super(key: key);

  @override
  _CheckoutScreenState createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _formKey = GlobalKey<FormState>();
  final _shippingService = ShippingService();
  bool _isLoading = false;
  
  // Form controllers
  final _nameController = TextEditingController();
  final _addressController = TextEditingController();
  final _cityController = TextEditingController();
  final _countryController = TextEditingController();
  final _phoneController = TextEditingController();
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Checkout')),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Shipping Information', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    SizedBox(height: 16),
                    TextFormField(
                      controller: _nameController,
                      decoration: InputDecoration(labelText: 'Full Name'),
                      validator: (value) => value!.isEmpty ? 'Please enter your name' : null,
                    ),
                    TextFormField(
                      controller: _addressController,
                      decoration: InputDecoration(labelText: 'Address'),
                      validator: (value) => value!.isEmpty ? 'Please enter your address' : null,
                    ),
                    TextFormField(
                      controller: _cityController,
                      decoration: InputDecoration(labelText: 'City'),
                      validator: (value) => value!.isEmpty ? 'Please enter your city' : null,
                    ),
                    TextFormField(
                      controller: _countryController,
                      decoration: InputDecoration(labelText: 'Country'),
                      validator: (value) => value!.isEmpty ? 'Please enter your country' : null,
                    ),
                    TextFormField(
                      controller: _phoneController,
                      decoration: InputDecoration(labelText: 'Phone'),
                      validator: (value) => value!.isEmpty ? 'Please enter your phone' : null,
                    ),
                    SizedBox(height: 24),
                    Text('Order Summary', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    SizedBox(height: 16),
                    // Display cart items here
                    ...widget.cartItems.map((item) => ListTile(
                      leading: Image.network(item.product.image),
                      title: Text(item.product.name),
                      subtitle: Text('Quantity: ${item.quantity}'),
                      trailing: Text('\$${(item.product.price * item.quantity).toStringAsFixed(2)}'),
                    )),
                    Divider(),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Subtotal:'),
                        Text('\$${_calculateSubtotal().toStringAsFixed(2)}'),
                      ],
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Shipping:'),
                        Text('\$10.00'), // Fixed shipping cost
                      ],
                    ),
                    Divider(),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Total:', style: TextStyle(fontWeight: FontWeight.bold)),
                        Text('\$${(_calculateSubtotal() + 10.0).toStringAsFixed(2)}', 
                             style: TextStyle(fontWeight: FontWeight.bold)),
                      ],
                    ),
                    SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _placeOrder,
                        child: Text('Place Order'),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  double _calculateSubtotal() {
    return widget.cartItems.fold(0, (sum, item) => sum + (item.product.price * item.quantity));
  }

  void _placeOrder() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      
      try {
        // Prepare order data
        final orderData = {
          'items': widget.cartItems.map((item) => {
            'product_id': item.product.id,
            'quantity': item.quantity,
          }).toList(),
          'branch_id': widget.cartItems.first.product.branchId, // Assuming all items from same branch
          'shipping_address': {
            'name': _nameController.text,
            'address': _addressController.text,
            'city': _cityController.text,
            'country': _countryController.text,
            'phone': _phoneController.text,
          },
          'payment_method': 'credit_card', // Simplified for this example
          'notes': '',
        };
        
        // Place the order
        final order = await _shippingService.placeOrder(orderData);
        
        setState(() => _isLoading = false);
        
        // Navigate to order confirmation screen
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => OrderConfirmationScreen(order: order),
          ),
        );
      } catch (e) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    }
  }
}
```

### Order Confirmation Screen

```dart
class OrderConfirmationScreen extends StatelessWidget {
  final Order order;

  const OrderConfirmationScreen({Key? key, required this.order}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Order Confirmation')),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(Icons.check_circle, color: Colors.green, size: 64),
            SizedBox(height: 16),
            Text('Thank You!', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            Text('Your order has been placed successfully.'),
            SizedBox(height: 24),
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Order Details', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    SizedBox(height: 8),
                    _buildInfoRow('Order Number:', order.orderNumber),
                    _buildInfoRow('Total Amount:', '\$${order.total.toStringAsFixed(2)}'),
                    _buildInfoRow('Status:', order.status),
                    _buildInfoRow('Payment Status:', order.paymentStatus),
                    Divider(),
                    Text('Shipping Information', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    SizedBox(height: 8),
                    _buildInfoRow('Shipping Method:', _formatShippingMethod(order.shippingMethod)),
                    _buildInfoRow('Shipping Status:', order.shippingStatus),
                    if (order.trackingNumber != null)
                      _buildInfoRow('Tracking Number:', order.trackingNumber!),
                  ],
                ),
              ),
            ),
            SizedBox(height: 24),
            if (order.trackingNumber != null)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => TrackingScreen(trackingNumber: order.trackingNumber!),
                      ),
                    );
                  },
                  child: Text('Track Shipment'),
                ),
              ),
            SizedBox(height: 16),
            SizedBox(
              width: double.infinity,
              child: OutlinedButton(
                onPressed: () {
                  Navigator.popUntil(context, (route) => route.isFirst);
                },
                child: Text('Continue Shopping'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(width: 120, child: Text(label, style: TextStyle(fontWeight: FontWeight.w500))),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }

  String _formatShippingMethod(String method) {
    switch (method) {
      case 'vendor':
        return 'Vendor Delivery';
      case 'aramex':
        return 'Aramex Courier';
      default:
        return method;
    }
  }
}
```

### Tracking Screen

```dart
class TrackingScreen extends StatefulWidget {
  final String trackingNumber;

  const TrackingScreen({Key? key, required this.trackingNumber}) : super(key: key);

  @override
  _TrackingScreenState createState() => _TrackingScreenState();
}

class _TrackingScreenState extends State<TrackingScreen> {
  final _shippingService = ShippingService();
  bool _isLoading = true;
  Map<String, dynamic>? _trackingData;
  String? _error;

  @override
  void initState() {
    super.initState();
    _trackShipment();
  }

  Future<void> _trackShipment() async {
    try {
      final data = await _shippingService.trackShipment(widget.trackingNumber);
      setState(() {
        _trackingData = data;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Track Shipment')),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Text('Error: $_error'))
              : SingleChildScrollView(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Card(
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Shipment Information', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                              SizedBox(height: 8),
                              _buildInfoRow('Tracking Number:', widget.trackingNumber),
                              _buildInfoRow('Status:', _trackingData!['status']),
                              if (_trackingData!['shipped_at'] != null)
                                _buildInfoRow('Shipped Date:', _formatDate(_trackingData!['shipped_at'])),
                              if (_trackingData!['delivered_at'] != null)
                                _buildInfoRow('Delivered Date:', _formatDate(_trackingData!['delivered_at'])),
                            ],
                          ),
                        ),
                      ),
                      SizedBox(height: 24),
                      Text('Tracking History', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                      SizedBox(height: 16),
                      ..._buildTrackingTimeline(),
                    ],
                  ),
                ),
    );
  }

  List<Widget> _buildTrackingTimeline() {
    final trackingHistory = _trackingData!['tracking_history'] as List;
    
    return trackingHistory.map((event) {
      return Card(
        margin: EdgeInsets.only(bottom: 16),
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Column(
                children: [
                  Container(
                    width: 20,
                    height: 20,
                    decoration: BoxDecoration(
                      color: Colors.blue,
                      shape: BoxShape.circle,
                    ),
                  ),
                  if (trackingHistory.last != event)
                    Container(
                      width: 2,
                      height: 40,
                      color: Colors.grey[300],
                    ),
                ],
              ),
              SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(event['status'], style: TextStyle(fontWeight: FontWeight.bold)),
                    SizedBox(height: 4),
                    Text(_formatDate(event['timestamp'])),
                    SizedBox(height: 4),
                    Text(event['location']),
                  ],
                ),
              ),
            ],
          ),
        ),
      );
    }).toList();
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(width: 120, child: Text(label, style: TextStyle(fontWeight: FontWeight.w500))),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }

  String _formatDate(String dateString) {
    final date = DateTime.parse(dateString);
    return '${date.day}/${date.month}/${date.year} ${date.hour}:${date.minute.toString().padLeft(2, '0')}';
  }
}
```

## 5. Integration with Existing App

1. Add the new models to your app's model directory
2. Add the shipping service to your services directory
3. Integrate the checkout flow with your existing cart functionality
4. Add the order confirmation and tracking screens to your app
5. Update your order history screen to show shipping information and tracking links

## 6. Testing

Test the following scenarios:

1. Place an order with a vendor that can deliver
2. Place an order with a vendor that cannot deliver (should use Aramex)
3. Track a shipment using the tracking number
4. View order details with shipping information

## 7. Error Handling

Implement proper error handling for:

1. Network errors during checkout
2. Failed shipment creation
3. Invalid tracking numbers
4. Missing shipping information

## 8. Additional Features

Consider implementing these additional features:

1. Push notifications for shipping status updates
2. Estimated delivery dates
3. Multiple shipping options (standard, express, etc.)
4. Address book for saved shipping addresses
