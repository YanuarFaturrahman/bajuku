<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Beranda' }} — Bajuku</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col">
    <x-toast />

    {{-- Top bar pengumuman promo --}}
    <div class="bg-brand-500 py-2 text-center text-xs tracking-wide text-white">
        Gratis ongkir untuk belanja min. {{ rupiah(config('shop.free_shipping_min')) }} ✦ Pembayaran aman &amp; mudah
    </div>

    @php($menuCategories = \App\Models\Category::whereNull('parent_id')->with('children')->orderBy('name')->get())

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-brand-100 bg-white/90 backdrop-blur" x-data="{ mobile: false }">
        <div class="mx-auto flex max-w-7xl items-center gap-6 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" wire:navigate class="font-display text-2xl font-semibold tracking-tight text-gray-900">
                Baju<span class="text-brand-500">ku</span>
            </a>

            {{-- Mega menu kategori --}}
            <nav class="hidden items-center gap-1 lg:flex">
                <a href="{{ route('catalog') }}" wire:navigate class="rounded-full px-4 py-2 text-sm text-gray-600 hover:bg-brand-50 hover:text-brand-700">Semua</a>
                @foreach ($menuCategories as $cat)
                    <div class="group relative">
                        <a href="{{ route('catalog', ['category' => $cat->slug]) }}"
                           class="rounded-full px-4 py-2 text-sm text-gray-600 hover:bg-brand-50 hover:text-brand-700">{{ $cat->name }}</a>
                        @if ($cat->children->count())
                            <div class="invisible absolute left-0 top-full z-50 w-48 rounded-2xl border border-brand-100 bg-white p-2 opacity-0 shadow-lg transition group-hover:visible group-hover:opacity-100">
                                @foreach ($cat->children as $child)
                                    <a href="{{ route('catalog', ['category' => $child->slug]) }}"
                                       class="block rounded-xl px-3 py-2 text-sm text-gray-600 hover:bg-brand-50 hover:text-brand-700">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>

            {{-- Pencarian --}}
            <form action="{{ route('catalog') }}" method="GET" class="ml-auto hidden max-w-xs flex-1 md:block">
                <input type="search" name="search" placeholder="Cari baju, jaket, celana…" class="input !rounded-full !bg-brand-50/60 !py-2">
            </form>

            <div class="ml-auto flex items-center gap-2 md:ml-0">
                @auth
                    @if (auth()->user()->role === 'konsumen')
                        <a href="{{ route('account.wishlist') }}" class="flex h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-brand-50" title="Wishlist">♡</a>
                    @endif
                @endauth
                <livewire:shop.cart-counter />

                @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-full border border-brand-200 py-1.5 pl-1.5 pr-4 text-sm hover:bg-brand-50">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-500 text-xs font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="hidden max-w-[110px] truncate sm:block">{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-56 rounded-2xl border border-brand-100 bg-white p-2 shadow-lg">
                            <p class="px-3 py-2 text-xs text-gray-400">{{ auth()->user()->role_label }}</p>
                            @if (auth()->user()->role === 'konsumen')
                                <a href="{{ route('account.profile') }}" class="nav-link">Akun Saya</a>
                                <a href="{{ route('account.orders') }}" class="nav-link">Pesanan Saya</a>
                            @else
                                <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="nav-link">Buka Dashboard</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="nav-link w-full text-left !text-red-500">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-outline btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-sm">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">{{ $slot }}</main>

    {{-- Footer --}}
    <footer class="mt-20 border-t border-brand-100 bg-brand-50/60">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-4">
            <div>
                <p class="font-display text-2xl text-gray-900">Baju<span class="text-brand-500">ku</span></p>
                <p class="mt-3 text-sm text-gray-500">Fashion modern yang minimalis dan elegan untuk keseharianmu.</p>
            </div>
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-brand-700">Bantuan</p>
                <ul class="space-y-2 text-sm text-gray-500"><li>Customer Service</li><li>Kebijakan Retur</li><li>Panduan Ukuran</li></ul>
            </div>
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-brand-700">Pembayaran</p>
                <ul class="space-y-2 text-sm text-gray-500"><li>Virtual Account</li><li>QRIS</li><li>E-Wallet</li><li>Kartu Kredit</li></ul>
            </div>
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-brand-700">Pengiriman</p>
                <ul class="space-y-2 text-sm text-gray-500">@foreach (array_keys(config('shop.couriers')) as $c)<li>{{ $c }}</li>@endforeach</ul>
            </div>
        </div>
        <p class="border-t border-brand-100 py-5 text-center text-xs text-gray-400">© {{ date('Y') }} Bajuku. Seluruh hak cipta dilindungi.</p>
    </footer>

    @livewireScripts
</body>
</html>
