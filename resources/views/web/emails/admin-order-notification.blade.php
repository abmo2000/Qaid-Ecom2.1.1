<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2196F3;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .info-section {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        .info-section h3 {
            margin-top: 0;
            color: #2196F3;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .info-value {
            flex: 1;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
         .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
         .btn:hover {
            background-color: #1976D2;
        }
        .action-section {
            text-align: center;
            padding: 20px 0;
        }
        
    </style>
</head>
<body>
    <div class="header">
        <h1>New Order Received</h1>
    </div>
    
    <div class="content">
        <div class="alert">
            <strong>Action Required:</strong> A new order has been placed and requires your attention.
        </div>
        
        <div class="info-section">
            <h3>Order Information</h3>
            <div class="info-row">
                <span class="info-label">Order Number:</span>
                <span class="info-value">#{{ $order->order_id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value">{{ $order->created_at->format('F d, Y h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Amount:</span>
                <span class="info-value">EGP{{ number_format($totalAmount) }}</span>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $order->customer->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $order->customer->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $order->customer->phone ?? 'N/A' }}</span>
            </div>
            @if(isset($order->customer_address))
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $order->customer_address }}</span>
            </div>
            @endif
        </div>

        <div class="action-section">
            <a href="{{ url('admin/orders/' . $order->id) }}" class="btn">
                View Order Details
            </a>
        </div>
     
    </div>
</body>
</html>