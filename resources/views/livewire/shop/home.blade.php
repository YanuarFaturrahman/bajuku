<div>
    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-100 via-brand-50 to-white">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:py-28">
            <div>
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">New Collection</p>
                <h1 class="font-display text-5xl leading-tight text-gray-900 sm:text-6xl">Tampil elegan,<br>sederhana &amp; <span class="italic text-brand-500">berkarakter.</span></h1>
                <p class="mt-6 max-w-md text-gray-500">Koleksi baju, jaket, celana, dan aksesoris pilihan dengan potongan modern yang nyaman dipakai setiap hari.</p>
                <div class="mt-8 flex gap-3">
                    <a href="{{ route('catalog') }}" class="btn-primary px-8">Belanja Sekarang</a>
                    <a href="{{ route('catalog', ['sort' => 'populer']) }}" class="btn-outline px-8">Terpopuler</a>
                </div>
            </div>
            <div class="relative mx-auto hidden aspect-[4/5] w-full max-w-sm lg:block">
                <div class="absolute inset-0 rotate-3 rounded-[2rem] bg-brand-200/60"></div>
                <div class="absolute inset-0 -rotate-2 rounded-[2rem] bg-white shadow-xl"></div>
                <div class="absolute inset-4 flex items-center justify-center rounded-[1.5rem] bg-gradient-to-br from-brand-200 to-brand-50">
                    <span class="font-display text-7xl italic text-brand-500">Bajuku</span>
                </div>
            </div>
        </div>
    </section>

    {{-- KATEGORI --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <h2 class="mb-8 text-center font-display text-3xl">Belanja per Kategori</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            @foreach ($categories as $cat)
                <a href="{{ route('catalog', ['category' => $cat->slug]) }}"
                   class="group rounded-2xl border border-brand-100 bg-white p-6 text-center transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-md">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 font-display text-xl text-brand-500 group-hover:bg-brand-100">{{ substr($cat->name, 0, 1) }}</div>
                    <p class="text-sm font-medium text-gray-800">{{ $cat->name }}</p>
                    <p class="text-xs text-gray-400">{{ $cat->products_count }} produk</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- FLASH SALE --}}
    @if ($flashSale->count())
        <section class="bg-brand-50/70 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-brand-600">Penawaran Terbatas</p>
                        <h2 class="font-display text-3xl">Flash Sale</h2>
                    </div>
                    <a href="{{ route('catalog') }}" class="text-sm text-brand-600 hover:underline">Lihat semua →</a>
                </div>
                <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
                    @foreach ($flashSale as $p)
                        <x-product-card :product="$p" :wished="in_array($p->id, $wishlist)" wire:key="fs-{{ $p->id }}" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- TRENDING --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <h2 class="mb-8 font-display text-3xl">Produk Terlaris</h2>
        <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
            @foreach ($trending as $p)
                <x-product-card :product="$p" :wished="in_array($p->id, $wishlist)" wire:key="tr-{{ $p->id }}" />
            @endforeach
        </div>
    </section>

    {{-- TERBARU --}}
    <section class="mx-auto max-w-7xl px-4 pb-8 sm:px-6">
        <h2 class="mb-8 font-display text-3xl">Baru Datang</h2>
        <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
            @foreach ($newest as $p)
                <x-product-card :product="$p" :wished="in_array($p->id, $wishlist)" wire:key="nw-{{ $p->id }}" />
            @endforeach
        </div>
    </section>
</div>
