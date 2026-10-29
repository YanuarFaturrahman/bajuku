<div>
    <x-account-shell title="Wishlist / Favorit">
        <div class="grid grid-cols-2 gap-5 md:grid-cols-3">
            @forelse ($items as $w)
                <div wire:key="w-{{ $w->id }}" class="group">
                    <a href="{{ route('product.show', $w->product->slug) }}" class="block aspect-[3/4] overflow-hidden rounded-2xl bg-brand-50">
                        <x-product-image :product="$w->product" />
                    </a>
                    <p class="mt-3 truncate text-sm font-medium">{{ $w->product->name }}</p>
                    <p class="text-sm font-semibold text-brand-600">{{ rupiah($w->product->final_price) }}</p>
                    <button wire:click="remove({{ $w->id }})" class="mt-1 text-xs text-red-500 hover:underline">Hapus</button>
                </div>
            @empty
                <p class="col-span-full text-sm text-gray-400">Wishlist masih kosong. Tekan ikon hati pada produk untuk menyimpannya.</p>
            @endforelse
        </div>
    </x-account-shell>
</div>
