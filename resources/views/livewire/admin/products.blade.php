<div>
    <x-page-title title="Data Produk" subtitle="Kelola barang yang dipajang di toko.">
        <a href="{{ route('admin.products.create') }}" class="btn-primary">+ Pajang Produk Baru</a>
    </x-page-title>

    <div class="mb-4 flex flex-wrap gap-3">
        <input type="search" wire:model.live.debounce.400ms="search" class="input max-w-xs" placeholder="Cari produk…">
        <select wire:model.live="categoryId" class="input max-w-[200px]">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->full_name }}</option>@endforeach
        </select>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @forelse ($products as $p)
                <tr wire:key="p-{{ $p->id }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="h-14 w-11 shrink-0 overflow-hidden rounded-lg"><x-product-image :product="$p" /></div>
                            <div><p class="font-medium text-gray-800">{{ $p->name }}</p><p class="text-xs text-gray-400">{{ $p->variants->count() }} varian</p></div>
                        </div>
                    </td>
                    <td>{{ $p->category->full_name }}</td>
                    <td>{{ rupiah($p->final_price) }} @if ($p->has_discount)<span class="block text-xs text-gray-400 line-through">{{ rupiah($p->price) }}</span>@endif</td>
                    <td>{{ $p->total_stock }}</td>
                    <td><button wire:click="toggleActive({{ $p->id }})" class="{{ $p->is_active ? 'badge-green' : 'badge-gray' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</button></td>
                    <td class="text-right">
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn-soft btn-sm">Edit</a>
                        <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus produk {{ $p->name }}?" class="btn-danger btn-sm">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-gray-400">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
