<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="mb-8">
        <h1 class="font-display text-3xl">Katalog Produk</h1>
        <p class="mt-1 text-sm text-gray-500">Temukan gaya favoritmu dengan filter di samping.</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-[260px_1fr]">
        {{-- FILTER PANEL --}}
        <aside class="h-fit space-y-7 rounded-2xl border border-brand-100 bg-white p-6">
            <div>
                <label class="label">Cari</label>
                <input type="search" wire:model.live.debounce.400ms="search" class="input" placeholder="Nama produk…">
            </div>

            <div>
                <p class="label">Kategori</p>
                <div class="space-y-1 text-sm">
                    <button wire:click="$set('category','')" class="block w-full rounded-lg px-3 py-1.5 text-left {{ $category === '' ? 'bg-brand-100 text-brand-700' : 'hover:bg-brand-50' }}">Semua</button>
                    @foreach ($categories as $cat)
                        <button wire:click="$set('category','{{ $cat->slug }}')" class="block w-full rounded-lg px-3 py-1.5 text-left {{ $category === $cat->slug ? 'bg-brand-100 text-brand-700' : 'hover:bg-brand-50' }}">{{ $cat->name }}</button>
                        @foreach ($cat->children as $child)
                            <button wire:click="$set('category','{{ $child->slug }}')" class="block w-full rounded-lg py-1 pl-7 pr-3 text-left text-xs {{ $category === $child->slug ? 'bg-brand-100 text-brand-700' : 'text-gray-500 hover:bg-brand-50' }}">{{ $child->name }}</button>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div>
                <p class="label">Rentang Harga</p>
                <div class="flex items-center gap-2">
                    <input type="number" wire:model.live.debounce.600ms="minPrice" class="input !px-3" placeholder="Min">
                    <span class="text-gray-300">–</span>
                    <input type="number" wire:model.live.debounce.600ms="maxPrice" class="input !px-3" placeholder="Maks">
                </div>
            </div>

            <div>
                <p class="label">Ukuran</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (config('shop.sizes') as $s)
                        <label class="cursor-pointer">
                            <input type="checkbox" value="{{ $s }}" wire:model.live="sizes" class="peer sr-only">
                            <span class="flex h-9 w-11 items-center justify-center rounded-lg border border-gray-200 text-sm peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:text-white">{{ $s }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="label">Warna</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (config('shop.colors') as $name => $hex)
                        <label class="cursor-pointer" title="{{ $name }}">
                            <input type="checkbox" value="{{ $name }}" wire:model.live="colors" class="peer sr-only">
                            <span class="block h-8 w-8 rounded-full border border-gray-200 ring-offset-2 peer-checked:ring-2 peer-checked:ring-brand-500" style="background: {{ $hex }}"></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button wire:click="resetFilters" class="btn-soft w-full">Reset Filter</button>
        </aside>

        {{-- GRID PRODUK --}}
        <section>
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-500">Menampilkan <strong class="text-gray-800">{{ $products->total() }}</strong> produk</p>
                <div class="flex items-center gap-2">
                    <select wire:model.live="sort" class="input !w-auto !rounded-full !py-2">
                        <option value="terbaru">Terbaru</option>
                        <option value="populer">Terpopuler</option>
                        <option value="termurah">Harga Terendah</option>
                        <option value="termahal">Harga Tertinggi</option>
                    </select>
                    <div class="flex overflow-hidden rounded-full border border-gray-200 text-xs">
                        <button wire:click="$set('layout','grid')" class="px-3 py-2 {{ $layout === 'grid' ? 'bg-brand-500 text-white' : '' }}">Grid</button>
                        <button wire:click="$set('layout','list')" class="px-3 py-2 {{ $layout === 'list' ? 'bg-brand-500 text-white' : '' }}">List</button>
                    </div>
                </div>
            </div>

            <div wire:loading.class="opacity-50" class="transition">
                @if ($products->count())
                    <div class="{{ $layout === 'grid' ? 'grid grid-cols-2 gap-5 md:grid-cols-3' : 'space-y-4' }}">
                        @foreach ($products as $p)
                            @if ($layout === 'grid')
                                <x-product-card :product="$p" :wished="in_array($p->id, $wishlist)" wire:key="p-{{ $p->id }}" />
                            @else
                                <a href="{{ route('product.show', $p->slug) }}" wire:key="p-{{ $p->id }}"
                                   class="flex gap-5 rounded-2xl border border-brand-100 p-4 transition hover:shadow-md">
                                    <div class="h-28 w-24 shrink-0 overflow-hidden rounded-xl"><x-product-image :product="$p" /></div>
                                    <div class="flex-1">
                                        <p class="text-[11px] uppercase tracking-widest text-gray-400">{{ $p->category->name }}</p>
                                        <h3 class="font-medium text-gray-800">{{ $p->name }}</h3>
                                        <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $p->description }}</p>
                                        <p class="mt-2 font-semibold text-brand-600">{{ rupiah($p->final_price) }}</p>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $products->links() }}</div>
                @else
                    <div class="rounded-2xl border border-dashed border-brand-200 py-20 text-center">
                        <p class="font-display text-xl text-gray-700">Produk tidak ditemukan</p>
                        <p class="mt-1 text-sm text-gray-400">Coba ubah atau reset filter pencarian.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
