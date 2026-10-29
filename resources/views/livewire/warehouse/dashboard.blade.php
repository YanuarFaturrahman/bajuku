<div>
    <x-page-title title="Dashboard Gudang" subtitle="Antrean pengiriman, stok, dan barang masuk." />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Siap Diproses" :value="$toProcess" />
        <x-stat-card label="Sedang Dikemas" :value="$packing" />
        <x-stat-card label="Stok Menipis" :value="$lowStock" hint="≤ {{ config('shop.low_stock') }} pcs" />
        <x-stat-card label="PO Dalam Perjalanan" :value="$incoming" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card">
            <h2 class="mb-4 font-display text-lg">Fast Moving (30 hari)</h2>
            @forelse ($fast as $f)
                <div class="flex justify-between border-b border-brand-50 py-2 text-sm">
                    <span>{{ $f->variant?->product->name }} <span class="text-gray-400">({{ $f->variant?->label }})</span></span>
                    <span class="font-medium text-brand-600">{{ $f->qty }} terjual</span>
                </div>
            @empty <p class="text-sm text-gray-400">Belum ada data penjualan.</p> @endforelse
        </div>
        <div class="card">
            <h2 class="mb-4 font-display text-lg">Slow Moving (stok mengendap)</h2>
            @forelse ($slow as $s)
                <div class="flex justify-between border-b border-brand-50 py-2 text-sm">
                    <span>{{ $s->product->name }} <span class="text-gray-400">({{ $s->label }})</span></span>
                    <span class="text-gray-500">stok {{ $s->stock }}</span>
                </div>
            @empty <p class="text-sm text-gray-400">Tidak ada stok mengendap.</p> @endforelse
        </div>
    </div>
</div>
