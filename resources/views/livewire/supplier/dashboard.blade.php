<div>
    <x-page-title :title="'Halo, ' . $supplier->company_name" subtitle="Ringkasan Purchase Order dan tagihan Anda." />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="PO Baru (Perlu Aksi)" :value="$newPo" />
        <x-stat-card label="Diproses" :value="$process" />
        <x-stat-card label="Dalam Pengiriman" :value="$shipping" />
        <x-stat-card label="Selesai" :value="$done" />
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <x-stat-card label="Tagihan Belum Dibayar" :value="rupiah($unpaid)" hint="PO sudah diterima gudang, menunggu pembayaran" />
        <x-stat-card label="Total Sudah Dibayar" :value="rupiah($paid)" />
    </div>

    <h2 class="mb-3 mt-10 font-display text-lg">PO Terbaru</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. PO</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Pembayaran</th></tr></thead>
            <tbody>
            @forelse ($recent as $po)
                <tr><td class="font-medium">{{ $po->po_number }}</td><td>{{ $po->created_at->format('d M Y') }}</td><td>{{ rupiah($po->total) }}</td>
                    <td><span class="{{ $po->status_badge }}">{{ $po->status_label }}</span></td>
                    <td><span class="{{ $po->payment_status === 'paid' ? 'badge-green' : 'badge-amber' }}">{{ $po->payment_status === 'paid' ? 'Lunas' : 'Belum dibayar' }}</span></td></tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada PO.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
