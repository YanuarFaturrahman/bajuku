<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Label {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; color: #111; }
        .label { width: 380px; border: 2px dashed #f0508a; padding: 18px; margin: 0 auto 24px; }
        h2 { margin: 0 0 8px; font-family: Georgia, serif; color: #d93573; }
        .code { font-family: monospace; font-size: 22px; letter-spacing: 2px; border: 1px solid #111; padding: 8px; text-align: center; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        td, th { border-bottom: 1px solid #ddd; padding: 6px 4px; text-align: left; }
        .btn { background: #f0508a; color: #fff; border: 0; padding: 10px 22px; border-radius: 99px; cursor: pointer; display: block; margin: 0 auto; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="label">
        <h2>Bajuku — Label Pengiriman</h2>
        <strong>{{ $order->courier }} {{ $order->courier_service }}</strong>
        <div class="code">{{ $order->tracking_number ?: $order->order_number }}</div>
        <p><strong>Penerima:</strong> {{ $order->recipient_name }} ({{ $order->recipient_phone }})<br>{{ $order->shipping_address }}</p>
        <p><strong>No. Pesanan:</strong> {{ $order->order_number }}</p>
    </div>

    <div class="label" style="border-style: solid;">
        <h2>Packing Slip / Pick List</h2>
        <table>
            <tr><th>Rak</th><th>Produk</th><th>Varian</th><th>Qty</th></tr>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->variant?->rack_location ?? '-' }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->variant_label }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <button class="btn no-print" onclick="window.print()">Cetak Label</button>
</body>
</html>
