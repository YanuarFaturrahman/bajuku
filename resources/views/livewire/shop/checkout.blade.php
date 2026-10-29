<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <h1 class="mb-8 font-display text-3xl">Checkout</h1>

    <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
        <div class="space-y-6">

            {{-- 1. ALAMAT --}}
            <div class="card">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-display text-xl">1. Alamat Pengiriman</h2>
                    <button wire:click="$toggle('showAddressForm')" class="text-sm text-brand-600 hover:underline">{{ $showAddressForm ? 'Batal' : '+ Alamat baru' }}</button>
                </div>

                @if ($this->addresses->count())
                    <div class="space-y-3">
                        @foreach ($this->addresses as $a)
                            <label wire:key="addr-{{ $a->id }}" class="flex cursor-pointer gap-3 rounded-xl border p-4 {{ $addressId === $a->id ? 'border-brand-400 bg-brand-50' : 'border-gray-200' }}">
                                <input type="radio" wire:model.live="addressId" value="{{ $a->id }}" class="mt-1 accent-pink-500">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-800">{{ $a->label }} · {{ $a->recipient_name }} <span class="font-normal text-gray-400">({{ $a->phone }})</span></p>
                                    <p class="text-gray-500">{{ $a->full_address }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                @if ($showAddressForm)
                    <form wire:submit="saveAddress" class="mt-4 grid gap-4 rounded-xl bg-brand-50/50 p-4 sm:grid-cols-2">
                        <div><label class="label">Label</label><input wire:model="label" class="input">@error('label')<p class="error">{{ $message }}</p>@enderror</div>
                        <div><label class="label">Nama Penerima</label><input wire:model="recipient_name" class="input">@error('recipient_name')<p class="error">{{ $message }}</p>@enderror</div>
                        <div><label class="label">No. HP</label><input wire:model="phone" class="input">@error('phone')<p class="error">{{ $message }}</p>@enderror</div>
                        <div><label class="label">Kota</label><input wire:model="city" class="input">@error('city')<p class="error">{{ $message }}</p>@enderror</div>
                        <div class="sm:col-span-2"><label class="label">Alamat Lengkap</label><textarea wire:model="address" rows="2" class="input"></textarea>@error('address')<p class="error">{{ $message }}</p>@enderror</div>
                        <div><label class="label">Kode Pos</label><input wire:model="postal_code" class="input">@error('postal_code')<p class="error">{{ $message }}</p>@enderror</div>
                        <div class="flex items-end"><button class="btn-primary w-full">Simpan Alamat</button></div>
                    </form>
                @endif
            </div>

            {{-- 2. PENGIRIMAN --}}
            <div class="card">
                <h2 class="mb-4 font-display text-xl">2. Opsi Pengiriman</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Kurir</label>
                        <select wire:model.live="courier" class="input">
                            @foreach (config('shop.couriers') as $name => $services)<option value="{{ $name }}">{{ $name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Layanan</label>
                        <select wire:model.live="service" class="input">
                            @foreach (config("shop.couriers.{$courier}") as $name => $info)
                                <option value="{{ $name }}">{{ $name }} — est. {{ $info['eta'] }} ({{ rupiah($info['cost']) }}/kg)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. VOUCHER --}}
            <div class="card">
                <h2 class="mb-4 font-display text-xl">3. Voucher</h2>
                @if ($this->voucher)
                    <div class="flex items-center justify-between rounded-xl bg-brand-50 px-4 py-3 text-sm">
                        <span><strong class="text-brand-700">{{ $this->voucher->code }}</strong> — hemat {{ rupiah($this->discount) }}</span>
                        <button wire:click="removeVoucher" class="text-red-500">Hapus</button>
                    </div>
                @else
                    <div class="flex gap-2">
                        <input wire:model="voucherCode" class="input uppercase" placeholder="Masukkan kode promo">
                        <button wire:click="applyVoucher" class="btn-dark">Pakai</button>
                    </div>
                @endif
            </div>

            {{-- 4. PEMBAYARAN --}}
            <div class="card">
                <h2 class="mb-4 font-display text-xl">4. Metode Pembayaran</h2>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach (config('shop.payment_methods') as $m)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 text-sm {{ $payment === $m ? 'border-brand-400 bg-brand-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model.live="payment" value="{{ $m }}" class="accent-pink-500"> {{ $m }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RINGKASAN --}}
        <aside class="h-fit rounded-2xl border border-brand-100 bg-brand-50/50 p-6 lg:sticky lg:top-28">
            <h2 class="font-display text-xl">Ringkasan Pesanan</h2>
            <ul class="mt-4 space-y-3 border-b border-brand-100 pb-4 text-sm">
                @foreach ($this->items as $i)
                    <li class="flex justify-between gap-3">
                        <span class="text-gray-600">{{ $i->variant->product->name }} <span class="text-gray-400">({{ $i->variant->label }}) ×{{ $i->quantity }}</span></span>
                        <span>{{ rupiah($i->line_total) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Subtotal produk</dt><dd>{{ rupiah($this->subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Ongkos kirim</dt><dd>{{ $this->shippingCost === 0 ? 'GRATIS' : rupiah($this->shippingCost) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Diskon voucher</dt><dd class="text-brand-600">-{{ rupiah($this->discount) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Biaya layanan</dt><dd>{{ rupiah(config('shop.service_fee')) }}</dd></div>
            </dl>
            <div class="mt-4 flex items-baseline justify-between border-t border-brand-100 pt-4">
                <span class="font-medium">Total Pembayaran</span>
                <span class="font-display text-2xl text-brand-600">{{ rupiah($this->total) }}</span>
            </div>
            <button wire:click="placeOrder" wire:loading.attr="disabled" class="btn-primary mt-6 w-full">
                <span wire:loading.remove wire:target="placeOrder">Bayar Sekarang</span>
                <span wire:loading wire:target="placeOrder">Memproses…</span>
            </button>
        </aside>
    </div>
</div>
