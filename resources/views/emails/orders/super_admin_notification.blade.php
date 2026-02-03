<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Order Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">

<div style="max-width:650px; margin:auto; background:#ffffff; padding:20px; border-radius:6px;">

    <h2 style="color:#2c3e50; margin-bottom:10px;">
        🛒 New Order Placed
    </h2>

    <p>Hello <strong>Super Admin</strong>,</p>

    <p>
        A new order has been successfully placed on the system.
        Below are the order details:
    </p>

    <hr>

    <h3>📦 Order Summary</h3>

    <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;">
        <tr>
            <th align="left">Order Number</th>
            <td>{{ $order->order_number }}</td>
        </tr>
        <tr>
            <th align="left">Customer Name</th>
            <td>{{ $order->user->name }}</td>
        </tr>
        <tr>
            <th align="left">Customer Email</th>
            <td>{{ $order->user->email }}</td>
        </tr>
        <tr>
            <th align="left">Payment Method</th>
            <td>{{ ucfirst($order->payment_method) }}</td>
        </tr>
        <tr>
            <th align="left">Order Status</th>
            <td>{{ ucfirst($order->status) }}</td>
        </tr>
    </table>

    <br>

    <h3>🧾 Ordered Items</h3>

    <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;">
        <thead>
        <tr style="background:#f0f0f0;">
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td align="center">{{ $item->quantity }}</td>
                <td align="right">₹ {{ number_format($item->price, 2) }}</td>
                <td align="right">₹ {{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <br>

    <h3>💰 Price Details</h3>
    <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;">
        <tr>
            <th align="left">Subtotal</th>
            <td align="right">₹ {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <th align="left">Extra Charges</th>
            <td align="right">₹ {{ number_format($order->extra_charge, 2) }}</td>
        </tr>
        <tr>
            <th align="left">GST (18%)</th>
            <td align="right">₹ {{ number_format($order->gst, 2) }}</td>
        </tr>
        <tr style="background:#eafaf1;">
            <th align="left">Grand Total</th>
            <td align="right"><strong>₹ {{ number_format($order->total_amount, 2) }}</strong></td>
        </tr>
    </table>

    <br>

    <p style="color:#555;">
        Please review the order if any action is required.
    </p>

    <p>
        Regards,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

</div>

</body>
</html>
