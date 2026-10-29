<div>
    <x-account-shell title="Ulasan Saya">
        <div class="space-y-4">
            @forelse ($reviews as $r)
                <div class="card !p-5">
                    <a href="{{ route('product.show', $r->product->slug) }}" class="font-medium text-gray-800 hover:text-brand-600">{{ $r->product->name }}</a>
                    <p class="text-brand-500">{{ str_repeat('★', $r->rating) }}<span class="text-gray-300">{{ str_repeat('★', 5 - $r->rating) }}</span></p>
                    <p class="mt-1 text-sm text-gray-600">{{ $r->comment }}</p>
                    <p class="mt-2 text-xs text-gray-400">{{ $r->created_at->format('d M Y') }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-400">Kamu belum menulis ulasan. Ulasan bisa dibuat setelah pesanan selesai.</p>
            @endforelse
        </div>
    </x-account-shell>
</div>
