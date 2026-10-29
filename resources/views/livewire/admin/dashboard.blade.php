<div>
    <x-page-title title="Dashboard Admin" subtitle="Ringkasan katalog dan pesanan hari ini." />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-card label="Menunggu Bayar" :value="$unpaid" />
        <x-stat-card label="Perlu Diproses" :value="$toProcess" />
        <x-stat-card label="Pengajuan Retur" :value="$returns" />
        <x-stat-card label="Total Produk" :value="$products" />
        <x-stat-card label="Varian Stok Menipis" :value="$lowStock" />
    </div>

    <h2 class="mb-3 mt-10 font-display text-lg">Pesanan Terbaru</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Pesanan</th><th>Konsumen</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
            @forelse ($recent as $o)
                <tr><td class="font-medium">{{ $o->order_number }}</td><td>{{ $o->user->name }}</td><td>{{ rupiah($o->total) }}</td>
                    <td><span class="{{ $o->status_badge }}">{{ $o->status_label }}</span></td><td class="text-gray-400">{{ $o->created_at->format('d M H:i') }}</td></tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
