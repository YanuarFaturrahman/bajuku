<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6" x-data="{ sizeChart: false }">
    <nav class="mb-6 text-xs text-gray-400">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a> /
        <a href="{{ route('catalog', ['category' => $product->category->slug]) }}" class="hover:text-brand-600">{{ $product->category->name }}</a> /
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-2">
        {{-- GALERI --}}
        <div>
            <div class="aspect-[4/5] overflow-hidden rounded-3xl bg-brand-50">
                <x-product-image :product="$product" />
            </div>
        </div>

        {{-- INFO --}}
        <div>
            <p class="text-xs uppercase tracking-[0.25em] text-brand-600">{{ $product->category->name }}</p>
            <h1 class="mt-2 font-display text-4xl">{{ $product->name }}</h1>
            <p class="mt-2 text-sm text-gray-500">★ {{ $reviews->count() ? $average : '-' }} · {{ $reviews->count() }} ulasan · {{ $product->sold_count }} terjual</p>

            <div class="mt-5 flex items-baseline gap-3">
                <span class="font-display text-3xl text-brand-600">{{ rupiah($product->final_price) }}</span>
                @if ($product->has_discount)
                    <span class="text-gray-400 line-through">{{ rupiah($product->price) }}</span>
                    <span class="badge-pink">-{{ $product->discount_percent }}%</span>
                @endif
            </div>

            {{-- Warna --}}
            <div class="mt-8">
                <p class="label">Warna: <span class="normal-case text-gray-800">{{ $color }}</span></p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->availableColors as $c)
                        <button wire:click="selectColor('{{ $c }}')"
                                class="rounded-full border px-4 py-1.5 text-sm transition {{ $color === $c ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-gray-200 hover:border-brand-300' }}">
                            {{ $c }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Ukuran --}}
            <div class="mt-6">
                <div class="mb-1.5 flex items-center justify-between">
                    <p class="label !mb-0">Ukuran: <span class="normal-case text-gray-800">{{ $size }}</span></p>
                    <button type="button" @click="sizeChart = true" class="text-xs text-brand-600 underline">Panduan Ukuran</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->sizesForColor as $s => $stock)
                        <button wire:click="selectSize('{{ $s }}')" @disabled($stock < 1)
                                class="flex h-11 w-14 items-center justify-center rounded-xl border text-sm transition
                                {{ $size === $s ? 'border-brand-500 bg-brand-500 text-white' : 'border-gray-200 hover:border-brand-300' }}
                                {{ $stock < 1 ? 'cursor-not-allowed text-gray-300 line-through' : '' }}">{{ $s }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Stok & qty --}}
            @php($variant = $this->variant)
            <div class="mt-6 flex flex-wrap items-center gap-5">
                <div class="flex items-center rounded-full border border-gray-200">
                    <button wire:click="decrement" class="h-10 w-10 text-lg hover:text-brand-600">−</button>
                    <span class="w-10 text-center text-sm font-medium">{{ $qty }}</span>
                    <button wire:click="increment" class="h-10 w-10 text-lg hover:text-brand-600">+</button>
                </div>
                @if ($variant)
                    @if ($variant->stock < 1)
                        <span class="badge-red">Stok habis</span>
                    @elseif ($variant->stock <= 5)
                        <span class="text-sm font-medium text-brand-600">Stok tinggal {{ $variant->stock }} lagi!</span>
                    @else
                        <span class="text-sm text-gray-500">Stok tersedia: {{ $variant->stock }}</span>
                    @endif
                @endif
            </div>

            <div class="mt-8 flex gap-3">
                <button wire:click="addToCart(false)" class="btn-outline flex-1" @disabled(! $variant || $variant->stock < 1)>+ Keranjang</button>
                <button wire:click="addToCart(true)" class="btn-primary flex-1" @disabled(! $variant || $variant->stock < 1)>Beli Sekarang</button>
                <button wire:click="toggleWishlist({{ $product->id }})" class="flex h-11 w-11 items-center justify-center rounded-full border border-brand-200 text-lg {{ $wished ? 'text-brand-500' : 'text-gray-400' }}">{{ $wished ? '♥' : '♡' }}</button>
            </div>

            <p class="mt-4 text-xs text-gray-400">SKU: {{ $variant?->sku ?? '-' }} · Berat {{ $product->weight }} gram</p>
        </div>
    </div>

    {{-- TAB DESKRIPSI --}}
    <div class="mt-16">
        <div class="flex gap-6 border-b border-brand-100 text-sm">
            @foreach (['deskripsi' => 'Deskripsi', 'bahan' => 'Spesifikasi Bahan', 'perawatan' => 'Panduan Perawatan', 'ulasan' => 'Ulasan Pembeli (' . $reviews->count() . ')'] as $key => $label)
                <button wire:click="$set('tab','{{ $key }}')" class="-mb-px border-b-2 pb-3 {{ $tab === $key ? 'border-brand-500 font-medium text-brand-600' : 'border-transparent text-gray-500' }}">{{ $label }}</button>
            @endforeach
        </div>
        <div class="max-w-3xl py-8 text-sm leading-7 text-gray-600">
            @if ($tab === 'deskripsi') {{ $product->description ?: 'Belum ada deskripsi.' }}
            @elseif ($tab === 'bahan') {{ $product->material ?: 'Belum ada informasi bahan.' }}
            @elseif ($tab === 'perawatan') {{ $product->care ?: 'Cuci dengan air dingin, jangan diperas, setrika suhu rendah.' }}
            @else
                @forelse ($reviews as $r)
                    <div class="border-b border-brand-50 py-4">
                        <p class="font-medium text-gray-800">{{ $r->user->name }} <span class="ml-2 text-brand-500">{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}</span></p>
                        <p class="mt-1">{{ $r->comment }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $r->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p>Belum ada ulasan untuk produk ini.</p>
                @endforelse
            @endif
        </div>
    </div>

    {{-- MODAL PANDUAN UKURAN --}}
    <div x-show="sizeChart" x-cloak class="modal-backdrop" @click.self="sizeChart = false">
        <div class="modal !max-w-lg">
            <h3 class="font-display text-xl">Panduan Ukuran</h3>
            <table class="mt-4 w-full text-center text-sm">
                <thead class="bg-brand-50 text-xs uppercase text-brand-800"><tr><th class="py-2">Ukuran</th><th>Lingkar Dada</th><th>Panjang Baju</th></tr></thead>
                <tbody>
                    <tr class="border-t border-brand-50"><td class="py-2">S</td><td>92 cm</td><td>66 cm</td></tr>
                    <tr class="border-t border-brand-50"><td class="py-2">M</td><td>98 cm</td><td>68 cm</td></tr>
                    <tr class="border-t border-brand-50"><td class="py-2">L</td><td>104 cm</td><td>70 cm</td></tr>
                    <tr class="border-t border-brand-50"><td class="py-2">XL</td><td>110 cm</td><td>72 cm</td></tr>
                </tbody>
            </table>
            <button @click="sizeChart = false" class="btn-primary mt-6 w-full">Tutup</button>
        </div>
    </div>
</div>
