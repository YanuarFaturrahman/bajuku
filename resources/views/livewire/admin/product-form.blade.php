<div>
    <x-page-title :title="$product ? 'Edit Produk' : 'Pajang Produk Baru'" subtitle="Isi informasi produk, foto, varian, dan stok tampil.">
        <a href="{{ route('admin.products') }}" class="btn-soft">← Kembali</a>
    </x-page-title>

    <form wire:submit="save" class="space-y-6">

        {{-- CARD 1: INFORMASI DASAR --}}
        <div class="card space-y-4">
            <h2 class="font-display text-lg">1. Informasi Dasar</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2"><label class="label">Judul Produk</label><input wire:model="name" class="input">@error('name')<p class="error">{{ $message }}</p>@enderror</div>
                <div>
                    <label class="label">Kategori</label>
                    <select wire:model="category_id" class="input">
                        <option value="">Pilih kategori…</option>
                        @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->full_name }}</option>@endforeach
                    </select>
                    @error('category_id')<p class="error">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-end gap-2 pb-2 text-sm"><input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded accent-pink-500"> Tampilkan di toko (aktif)</label>
                <div class="sm:col-span-2"><label class="label">Deskripsi</label><textarea wire:model="description" rows="4" class="input"></textarea></div>
                <div><label class="label">Spesifikasi Bahan</label><textarea wire:model="material" rows="2" class="input" placeholder="Contoh: Katun combed 30s"></textarea></div>
                <div><label class="label">Panduan Perawatan</label><textarea wire:model="care" rows="2" class="input" placeholder="Contoh: Cuci air dingin, jangan dikucek"></textarea></div>
            </div>
        </div>

        {{-- CARD 2: MEDIA --}}
        <div class="card space-y-4">
            <h2 class="font-display text-lg">2. Foto Produk</h2>
            <div class="flex flex-wrap items-center gap-5">
                <div class="h-40 w-32 overflow-hidden rounded-2xl bg-brand-50">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover">
                    @elseif ($currentImage)
                        <img src="{{ asset('storage/' . $currentImage) }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-xs text-gray-400">Belum ada foto</div>
                    @endif
                </div>
                <div>
                    <input type="file" wire:model="photo" accept="image/*" class="text-sm">
                    <div wire:loading wire:target="photo" class="mt-2 text-xs text-brand-600">Mengunggah…</div>
                    @error('photo')<p class="error">{{ $message }}</p>@enderror
                    <p class="mt-2 text-xs text-gray-400">JPG/PNG/WebP, maksimal 2 MB. Rasio 4:5 disarankan.</p>
                </div>
            </div>
        </div>

        {{-- CARD 3: HARGA --}}
        <div class="card space-y-4">
            <h2 class="font-display text-lg">3. Harga, Berat & Pengiriman</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div><label class="label">Harga Jual (Rp)</label><input type="number" wire:model="price" class="input">@error('price')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Harga Diskon (Rp)</label><input type="number" wire:model="discount_price" class="input" placeholder="Kosongkan jika tidak diskon">@error('discount_price')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Harga Modal (Rp)</label><input type="number" wire:model="cost_price" class="input">@error('cost_price')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Berat (gram)</label><input type="number" wire:model="weight" class="input">@error('weight')<p class="error">{{ $message }}</p>@enderror</div>
            </div>
        </div>

        {{-- CARD 4: VARIAN --}}
        <div class="card space-y-5">
            <h2 class="font-display text-lg">4. Varian & Stok Tampil</h2>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <p class="label">Pilih Warna</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (config('shop.colors') as $name => $hex)
                            <label class="flex cursor-pointer items-center gap-2 rounded-full border border-gray-200 px-3 py-1.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                <input type="checkbox" wire:model.live="colors" value="{{ $name }}" class="sr-only">
                                <span class="h-4 w-4 rounded-full border border-gray-200" style="background: {{ $hex }}"></span>{{ $name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="label">Pilih Ukuran</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (config('shop.sizes') as $s)
                            <label class="cursor-pointer rounded-xl border border-gray-200 px-4 py-1.5 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500 has-[:checked]:text-white">
                                <input type="checkbox" wire:model.live="sizes" value="{{ $s }}" class="sr-only">{{ $s }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @error('rows')<p class="error">{{ $message }}</p>@enderror

            @if (count($rows))
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Varian</th><th>SKU (opsional)</th><th>Stok</th><th>Lokasi Rak</th></tr></thead>
                        <tbody>
                        @foreach ($rows as $i => $row)
                            <tr wire:key="row-{{ $row['color'] }}-{{ $row['size'] }}">
                                <td class="font-medium">{{ $row['color'] }} / {{ $row['size'] }}</td>
                                <td><input wire:model="rows.{{ $i }}.sku" class="input !py-1.5" placeholder="otomatis">@error("rows.$i.sku")<p class="error">{{ $message }}</p>@enderror</td>
                                <td><input type="number" min="0" wire:model="rows.{{ $i }}.stock" class="input !w-24 !py-1.5">@error("rows.$i.stock")<p class="error">{{ $message }}</p>@enderror</td>
                                <td><input wire:model="rows.{{ $i }}.rack" class="input !w-28 !py-1.5" placeholder="A-12"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="sticky bottom-4 flex justify-end gap-3 rounded-2xl border border-brand-100 bg-white/90 p-4 backdrop-blur">
            <a href="{{ route('admin.products') }}" class="btn-soft">Batal</a>
            <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="save">Simpan Produk</button>
        </div>
    </form>
</div>
