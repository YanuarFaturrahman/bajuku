<div>
    <x-page-title title="Laporan & Analisis" subtitle="Pilih periode lalu telusuri penjualan, keuangan, produk, dan logistik.">
        <button onclick="window.print()" class="btn-soft">Cetak</button>
    </x-page-title>

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <input type="date" wire:model.live="from" class="input !w-auto">
        <span class="text-gray-400">s/d</span>
        <input type="date" wire:model.live="to" class="input !w-auto">
    </div>

    <div class="mb-5 flex flex-wrap gap-2">
        @foreach ($tabs as $key => $label)
            <button wire:click="$set('tab','{{ $key }}')" class="rounded-full px-4 py-1.5 text-sm {{ $tab === $key ? 'bg-brand-500 text-white' : 'bg-white text-brand-700 ring-1 ring-brand-100 hover:bg-brand-50' }}">{{ $label }}</button>
        @endforeach
    </div>

    @if ($tab === 'penjualan')
        <div class="table-wrap"><table>
            <thead><tr><th>Tanggal</th><th>Jumlah Pesanan</th><th>GMV</th><th>Ongkir</th></tr></thead>
            <tbody>
            @forelse ($sales as $s)
                <tr><td>{{ \Carbon\Carbon::parse($s->day)->format('d M Y') }}</td><td>{{ $s->orders }}</td><td class="font-medium">{{ rupiah($s->gmv) }}</td><td>{{ rupiah($s->shipping) }}</td></tr>
            @empty <tr><td colspan="4" class="py-10 text-center text-gray-400">Tidak ada penjualan pada periode ini.</td></tr> @endforelse
            </tbody>
            @if ($sales->count())
            <tfoot><tr class="bg-brand-50 font-semibold"><td class="px-4 py-3">Total</td><td class="px-4 py-3">{{ $sales->sum('orders') }}</td><td class="px-4 py-3 text-brand-600">{{ rupiah($sales->sum('gmv')) }}</td><td class="px-4 py-3">{{ rupiah($sales->sum('shipping')) }}</td></tr></tfoot>
            @endif
        </table></div>
    @endif

    @if ($tab === 'keuangan')
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="card">
                <h2 class="mb-4 font-display text-lg">Laba Rugi Sederhana</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt>Pendapatan penjualan produk</dt><dd>{{ rupiah($pl['revenue']) }}</dd></div>
                    <div class="flex justify-between"><dt>Harga pokok penjualan (modal)</dt><dd>-{{ rupiah($pl['cogs']) }}</dd></div>
                    <div class="flex justify-between"><dt>Diskon voucher</dt><dd>-{{ rupiah($pl['discount']) }}</dd></div>
                    <div class="flex justify-between border-t border-brand-100 pt-2 text-base font-semibold"><dt>Laba Kotor</dt><dd class="text-brand-600">{{ rupiah($pl['gross']) }}</dd></div>
                    <div class="flex justify-between text-gray-400"><dt>Biaya layanan terkumpul</dt><dd>{{ rupiah($pl['fee']) }}</dd></div>
                </dl>
            </div>
            <div class="card">
                <h2 class="mb-4 font-display text-lg">Pengadaan (PO)</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt>Sudah dibayar ke supplier</dt><dd>{{ rupiah($poPaid) }}</dd></div>
                    <div class="flex justify-between"><dt>Hutang PO belum dibayar</dt><dd class="text-amber-600">{{ rupiah($poUnpaid) }}</dd></div>
                </dl>
            </div>
        </div>
        <h2 class="mb-3 mt-8 font-display text-lg">Margin per Kategori</h2>
        <div class="table-wrap"><table>
            <thead><tr><th>Kategori</th><th>Pendapatan</th><th>Laba</th><th>Margin</th></tr></thead>
            <tbody>
            @forelse ($margins as $m)
                <tr><td class="font-medium">{{ $m['name'] }}</td><td>{{ rupiah($m['revenue']) }}</td><td>{{ rupiah($m['profit']) }}</td><td><span class="badge-pink">{{ $m['margin'] }}%</span></td></tr>
            @empty <tr><td colspan="4" class="py-8 text-center text-gray-400">Tidak ada data.</td></tr> @endforelse
            </tbody>
        </table></div>
    @endif

    @if ($tab === 'produk')
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="card">
                <h2 class="mb-4 font-display text-lg">Best Seller</h2>
                @forelse ($best as $i => $b)
                    <div class="flex justify-between border-b border-brand-50 py-2 text-sm"><span>{{ $i + 1 }}. {{ $b->product_name }}</span><span class="text-gray-500">{{ $b->qty }} pcs · {{ rupiah($b->revenue) }}</span></div>
                @empty <p class="text-sm text-gray-400">Tidak ada data.</p> @endforelse
            </div>
            <div class="card">
                <h2 class="mb-4 font-display text-lg">Dead Stock (tidak laku di periode ini)</h2>
                @forelse ($dead as $d)
                    <div class="flex justify-between border-b border-brand-50 py-2 text-sm"><span>{{ $d->product->name }} ({{ $d->label }})</span><span class="text-amber-600">stok {{ $d->stock }}</span></div>
                @empty <p class="text-sm text-gray-400">Tidak ada dead stock.</p> @endforelse
            </div>
        </div>
    @endif

    @if ($tab === 'logistik')
        <div class="table-wrap"><table>
            <thead><tr><th>Kurir</th><th>Jumlah Pengiriman</th><th>Total Ongkir</th></tr></thead>
            <tbody>
            @forelse ($couriers as $c)<tr><td class="font-medium">{{ $c->courier }}</td><td>{{ $c->orders }}</td><td>{{ rupiah($c->cost) }}</td></tr>
            @empty <tr><td colspan="3" class="py-8 text-center text-gray-400">Tidak ada data.</td></tr> @endforelse
            </tbody>
        </table></div>
    @endif
</div>
