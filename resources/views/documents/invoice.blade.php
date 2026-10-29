<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #374151; margin: 0; padding: 32px; }
        .wrap { max-width: 760px; margin: auto; }
        h1 { font-family: Georgia, serif; color: #d93573; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #ffe8f0; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #fde1ec; font-size: 14px; }
        .right { text-align: right; }
        .muted { color: #9ca3af; font-size: 13px; }
        .total td { font-weight: bold; font-size: 16px; border-bottom: 0; }
        .btn { background: #f0508a; color: #fff; border: 0; padding: 10px 22px; border-radius: 99px; cursor: pointer; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
<div class="wrap">
    <div style="display:flex;justify-content:space-between;align-items:flex-start">
        <div><h1>Bajuku</h1><p class="muted">Invoice Pembelian</p></div>
        <div class="right">
            <strong>{{ $order->order_number }}</strong><br>
            <span class="muted">{{ $order->created_at->format('d M Y H:i') }}</span><br>
            <span class="muted">Status: {{ $order->status_label }}</span>
        </div>
    </div>

    <p style="margin-top:24px"><strong>Dikirim kepada</strong><br>
        {{ $order->recipient_name }} ({{ $order->recipient_phone }})<br>
        {{ $order->shipping_address }}</p>

    <table>
        <thead><tr><th>Produk</th><th class="right">Harga</th><th class="right">Qty</th><th class="right">Subtotal</th></tr></thead>
        <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}<br><span class="muted">{{ $item->variant_label }}</span></td>
                <td class="right">{{ rupiah($item->price) }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ rupiah($item->line_total) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="right">Subtotal</td><td class="right">{{ rupiah($order->subtotal) }}</td></tr>
            <tr><td colspan="3" class="right">Ongkir ({{ $order->courier }} {{ $order->courier_service }})</td><td class="right">{{ rupiah($order->shipping_cost) }}</td></tr>
            <tr><td colspan="3" class="right">Diskon {{ $order->voucher_code ? '('.$order->voucher_code.')' : '' }}</td><td class="right">-{{ rupiah($order->discount) }}</td></tr>
            <tr><td colspan="3" class="right">Biaya layanan</td><td class="right">{{ rupiah($order->service_fee) }}</td></tr>
            <tr class="total"><td colspan="3" class="right">Total Pembayaran</td><td class="right">{{ rupiah($order->total) }}</td></tr>
        </tfoot>
    </table>
    <p class="muted">Metode pembayaran: {{ $order->payment_method }} · Status bayar: {{ $order->payment_status }}</p>
    <button class="btn no-print" onclick="window.print()">Cetak Invoice</button>
</div>
</body>
</html>
