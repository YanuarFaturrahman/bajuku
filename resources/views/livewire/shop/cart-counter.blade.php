<a href="{{ auth()->check() ? route('cart') : route('login') }}"
   class="relative flex h-10 w-10 items-center justify-center rounded-full text-gray-600 hover:bg-brand-50" title="Keranjang">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
    </svg>
    @if ($count > 0)
        <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-brand-500 px-1 text-[10px] font-semibold text-white">{{ $count }}</span>
    @endif
</a>
