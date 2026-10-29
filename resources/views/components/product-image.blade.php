@props(['product'])
{{-- Gambar produk; jika belum ada foto tampilkan placeholder pink dengan huruf awal --}}
@if ($product->image)
    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy"
         {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 via-brand-50 to-white']) }}>
        <span class="font-display text-5xl text-brand-300">{{ \Illuminate\Support\Str::substr($product->name, 0, 1) }}</span>
    </div>
@endif
