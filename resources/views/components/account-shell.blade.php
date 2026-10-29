@props(['title'])
{{-- Kerangka halaman "Akun Saya" untuk Konsumen: sidebar kiri + konten kanan --}}
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
        <aside class="h-fit rounded-2xl border border-brand-100 bg-brand-50/50 p-3">
            <p class="px-4 pb-2 pt-3 text-xs font-semibold uppercase tracking-widest text-brand-700">Akun Saya</p>
            @foreach ([
                ['Profil', 'account.profile'], ['Pesanan Saya', 'account.orders'], ['Buku Alamat', 'account.addresses'],
                ['Wishlist', 'account.wishlist'], ['Ulasan Saya', 'account.reviews'], ['Voucher Saya', 'account.vouchers'],
            ] as [$label, $route])
                <a href="{{ route($route) }}" wire:navigate
                   class="nav-link {{ request()->routeIs($route) ? 'nav-link-active' : '' }}">{{ $label }}</a>
            @endforeach
        </aside>
        <section>
            <h1 class="mb-6 font-display text-2xl">{{ $title }}</h1>
            {{ $slot }}
        </section>
    </div>
</div>
