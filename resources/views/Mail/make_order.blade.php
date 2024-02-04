<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
        }
        p {
            color: #666666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            color: #ffffff;
            background-color: #3498db;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Order Confirmation</h1>
    <p>{{$order['name']}}</p>
    <p>Thank you for your order. We are pleased to inform you that your order has been received and is currently being processed.</p>
    <p>Order Details:</p>
    <ul>
        @foreach($order['order_details'] as $order_detail)
            <li>Product:{{$order_detail['product_name']}}, Quantity: {{$order_detail['amount']}}</li>
        @endforeach
    </ul>
    <p>If you have any questions or concerns, feel free to contact our customer support.</p>
    <p>Thank you for choosing our service!</p>
</div>
</body>
</html>
