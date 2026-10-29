<div>
    <x-page-title title="Executive Dashboard" subtitle="Performa bisnis Bajuku secara keseluruhan.">
        <select wire:model.live="days" class="input !w-auto !rounded-full !py-2">
            <option value="7">7 hari</option>
            <option value="14">14 hari</option>
            <option value="30">30 hari</option>
        </select>
    </x-page-title>

    {{-- KPI --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-card label="Total Omset (GMV)" :value="rupiah($gmv)" />
        <x-stat-card label="Estimasi Laba Bersih" :value="rupiah($netProfit)" hint="setelah modal & diskon" />
        <x-stat-card label="Pesanan Selesai" :value="$ordersDone" :hint="'dari ' . $allOrders . ' pesanan'" />
        <x-stat-card label="Rata-rata Nilai Order" :value="rupiah($aov)" />
        <x-stat-card
            label="Konversi Pembeli"
            :value="($customers ? round($buyers / $customers * 100) : 0) . '%'"
            :hint="$buyers . ' dari ' . $customers . ' pelanggan'" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[2fr_1fr]">
        {{-- GRAFIK BATANG: penjualan harian --}}
        <div class="card">
            <h2 class="mb-4 font-display text-lg">Penjualan Harian</h2>
            <div class="flex h-56 items-end gap-1.5">
                @foreach ($daily as $d)
                    <div class="group flex h-full flex-1 flex-col items-center justify-end"
                         title="{{ $d['label'] }}: {{ rupiah($d['total']) }}">
                        <div class="w-full rounded-t-lg bg-gradient-to-t from-brand-400 to-brand-200 transition group-hover:from-brand-500"
                             style="height: {{ max(2, $d['total'] / $dailyMax * 100) }}%"></div>
                        <span class="mt-2 text-[10px] text-gray-400">{{ $d['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PIE: distribusi kategori --}}
        <div class="card">
            <h2 class="mb-4 font-display text-lg">Penjualan per Kategori</h2>
            <div class="mx-auto h-40 w-40 rounded-full"
                 style="background: conic-gradient({{ $pieGradient }})"></div>
            <ul class="mt-5 space-y-1.5 text-sm">
                @foreach ($perCategory as $i => $c)
                    <li class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full"
                                  style="background: {{ $palette[$i % 6] }}"></span>
                            {{ $c['name'] }}
                        </span>
                        <span class="text-gray-500">{{ round($c['revenue'] / $pieTotal * 100) }}%</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card">
            <h2 class="mb-4 font-display text-lg">Top 5 Produk</h2>
            @forelse ($top as $i => $t)
                <div class="flex items-center gap-3 border-b border-brand-50 py-2.5 text-sm">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">{{ $i + 1 }}</span>
                    <span class="flex-1">{{ $t->product_name }}</span>
                    <span class="text-gray-400">{{ $t->qty }} pcs</span>
                    <span class="font-medium text-brand-600">{{ rupiah($t->revenue) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada data.</p>
            @endforelse
        </div>

        <div class="card">
            <h2 class="mb-4 font-display text-lg">Keandalan Supplier</h2>
            @forelse ($suppliers as $s)
                <div class="mb-4">
                    <div class="flex justify-between text-sm">
                        <span>{{ $s['name'] }}</span>
                        <span class="text-gray-400">{{ $s['done'] }}/{{ $s['total'] }} PO selesai</span>
                    </div>
                    <div class="mt-1.5 h-2 rounded-full bg-brand-50">
                        <div class="h-2 rounded-full bg-brand-500"
                             style="width: {{ $s['onTime'] ?? 0 }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">
                        Ketepatan waktu:
                        {{ $s['onTime'] !== null ? $s['onTime'] . '%' : 'belum ada data' }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada supplier.</p>
            @endforelse
        </div>
    </div>
</div>