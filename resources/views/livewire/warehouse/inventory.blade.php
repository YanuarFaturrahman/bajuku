<div>
    <x-page-title title="Manajemen Stok" subtitle="Pantau stok fisik, peringatan stok menipis, dan stock opname." />

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <input type="search" wire:model.live.debounce.400ms="search" class="input max-w-xs" placeholder="Cari SKU / produk">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model.live="lowOnly" class="rounded accent-pink-500"> Hanya stok menipis (≤ {{ config('shop.low_stock') }})</label>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Produk</th><th>Varian</th><th>SKU</th><th>Rak</th><th>Stok</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @foreach ($variants as $v)
                <tr wire:key="v-{{ $v->id }}">
                    <td class="font-medium">{{ $v->product->name }}</td>
                    <td>{{ $v->label }}</td>
                    <td class="text-xs text-gray-400">{{ $v->sku }}</td>
                    <td>{{ $v->rack_location ?? '—' }}</td>
                    <td>
                        <span class="{{ $v->stock === 0 ? 'badge-red' : ($v->is_low_stock ? 'badge-amber' : 'badge-green') }}">{{ $v->stock }}</span>
                        @if ($v->is_low_stock)<span class="ml-1 text-xs text-amber-600">menipis</span>@endif
                    </td>
                    <td class="text-right"><button wire:click="openOpname({{ $v->id }})" class="btn-soft btn-sm">Stock Opname</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $variants->links() }}</div>

    <h2 class="mb-3 mt-10 font-display text-lg">Riwayat Pergerakan Stok Terakhir</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Varian</th><th>Jenis</th><th>Jumlah</th><th>Stok Akhir</th><th>Referensi</th><th>Oleh</th></tr></thead>
            <tbody>
            @foreach ($movements as $m)
                <tr><td class="text-gray-400">{{ $m->created_at->format('d/m H:i') }}</td><td>{{ $m->variant?->product->name }} ({{ $m->variant?->label }})</td>
                    <td>{{ $m->type }}</td><td class="{{ $m->quantity < 0 ? 'text-red-500' : 'text-emerald-600' }}">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</td>
                    <td>{{ $m->stock_after }}</td><td class="text-xs">{{ $m->reference ?? '—' }}</td><td class="text-xs">{{ $m->user?->name ?? 'Sistem' }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if ($opname)
        <div class="modal-backdrop" wire:click.self="closeOpname">
            <form wire:submit="saveOpname" class="modal !max-w-md space-y-4">
                <h3 class="font-display text-xl">Stock Opname</h3>
                <p class="text-sm text-gray-500">{{ $opname->product->name }} ({{ $opname->label }}) · stok sistem <strong>{{ $opname->stock }}</strong></p>
                <div><label class="label">Jumlah Fisik Hasil Hitung</label><input type="number" min="0" wire:model="physical" class="input">@error('physical')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Catatan</label><input wire:model="note" class="input" placeholder="Contoh: barang rusak / salah hitung"></div>
                <div class="flex gap-2"><button class="btn-primary">Simpan Penyesuaian</button><button type="button" wire:click="closeOpname" class="btn-soft">Batal</button></div>
            </form>
        </div>
    @endif
</div>
