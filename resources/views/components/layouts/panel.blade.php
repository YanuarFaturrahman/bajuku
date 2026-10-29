<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — Bajuku {{ auth()->user()->role_label }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-brand-50/40" x-data="{ side: false }">
    <x-toast />

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-brand-100 bg-white p-5 transition lg:translate-x-0"
           :class="side ? '!translate-x-0' : ''">
        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="block font-display text-2xl font-semibold text-gray-900">
            Baju<span class="text-brand-500">ku</span>
        </a>
        <span class="badge-pink mt-2">{{ auth()->user()->role_label }}</span>

        <nav class="mt-8 space-y-1">
            @foreach (\App\Support\Menu::for(auth()->user()->role) as [$label, $route, $pattern])
                <a href="{{ route($route) }}" wire:navigate
                   class="nav-link {{ request()->routeIs($pattern) ? 'nav-link-active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="absolute inset-x-5 bottom-5">
            @csrf
            <button class="btn-soft w-full">Keluar</button>
        </form>
    </aside>
    <div x-show="side" @click="side = false" class="fixed inset-0 z-30 bg-gray-900/30 lg:hidden" x-cloak></div>

    {{-- Konten --}}
    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-brand-100 bg-white/90 px-5 py-3 backdrop-blur">
            <button class="rounded-xl border border-brand-200 px-3 py-1.5 text-sm lg:hidden" @click="side = true">Menu</button>
            <p class="hidden text-sm text-gray-400 lg:block">{{ now()->translatedFormat('l, d F Y') }}</p>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                </div>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-500 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
        </header>

        <main class="p-5 sm:p-8">{{ $slot }}</main>
    </div>

    @livewireScripts
</body>
</html>
