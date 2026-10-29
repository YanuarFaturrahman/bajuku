@props(['product', 'wished' => false])
<div {{ $attributes->merge(['class' => 'group relative']) }}>
    <a href="{{ route('product.show', $product->slug) }}" class="block">
        <div class="relative aspect-[3/4] overflow-hidden rounded-2xl bg-brand-50">
            <x-product-image :product="$product" class="transition duration-500 group-hover:scale-105" />
            @if ($product->has_discount)
                <span class="absolute left-3 top-3 rounded-full bg-brand-500 px-2.5 py-1 text-[11px] font-medium text-white">-{{ $product->discount_percent }}%</span>
            @elseif ($product->sold_count >= 50)
                <span class="absolute left-3 top-3 rounded-full bg-gray-900 px-2.5 py-1 text-[11px] font-medium text-white">Best Seller</span>
            @endif
        </div>
        <div class="mt-3 space-y-1 px-1">
            <p class="text-[11px] uppercase tracking-widest text-gray-400">{{ $product->category->name }}</p>
            <h3 class="truncate font-sans text-sm font-medium text-gray-800">{{ $product->name }}</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-sm font-semibold text-brand-600">{{ rupiah($product->final_price) }}</span>
                @if ($product->has_discount)
                    <span class="text-xs text-gray-400 line-through">{{ rupiah($product->price) }}</span>
                @endif
            </div>
            <p class="text-xs text-gray-400">★ {{ $product->reviews->count() ? number_format($product->reviews->avg('rating'), 1) : '-' }} · {{ $product->sold_count }} terjual</p>
        </div>
    </a>
    <button type="button" wire:click="toggleWishlist({{ $product->id }})"
            class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow-sm transition hover:scale-110"
            title="Wishlist">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $wished ? 'fill-brand-500 text-brand-500' : 'fill-none text-gray-500' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
    </button>
</div>
