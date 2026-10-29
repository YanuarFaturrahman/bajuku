<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Jalan {{ $po->po_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #374151; padding: 32px; }
        .wrap { max-width: 760px; margin: auto; }
        h1 { font-family: Georgia, serif; color: #d93573; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th { background: #ffe8f0; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #fde1ec; font-size: 14px; }
        .muted { color: #9ca3af; font-size: 13px; }
        .btn { background: #f0508a; color: #fff; border: 0; padding: 10px 22px; border-radius: 99px; cursor: pointer; margin-top: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Surat Jalan</h1>
    <p class="muted">Pengiriman barang untuk {{ $po->po_number }}</p>
    <p><strong>Dari:</strong> {{ $po->supplier->company_name }}<br>
       <strong>Kepada:</strong> Gudang Bajuku<br>
       <strong>Armada / Kurir:</strong> {{ $po->courier ?? '-' }} · <strong>Plat / Resi:</strong> {{ $po->vehicle_plate ?? '-' }}<br>
       <strong>Tanggal kirim:</strong> {{ $po->shipped_at?->format('d M Y') ?? '-' }}</p>
    <table>
        <thead><tr><th>SKU</th><th>Barang</th><th>Qty Dikirim</th></tr></thead>
        <tbody>
        @foreach ($po->items as $item)
            <tr>
                <td>{{ $item->variant->sku }}</td>
                <td>{{ $item->variant->product->name }} ({{ $item->variant->label }})</td>
                <td>{{ $item->qty_ordered }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <button class="btn no-print" onclick="window.print()">Cetak Surat Jalan</button>
</div>
</body>
</html>
