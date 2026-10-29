<div>
    <x-account-shell title="Detail Pesanan">
        <div class="space-y-6">
            <div class="card">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-display text-xl">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">Dibuat {{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <span class="{{ $order->status_badge }}">{{ $order->status_label }}</span>
                </div>

                {{-- TIMELINE STEPPER --}}
                @if (! in_array($order->status, ['cancelled', 'refunded']))
                    <ol class="mt-8 grid grid-cols-5 gap-2">
                        @foreach ($steps as $label => $time)
                            <li class="text-center">
                                <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full text-sm {{ $time ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-400' }}">{{ $loop->iteration }}</div>
                                <p class="mt-2 text-[11px] leading-tight {{ $time ? 'text-gray-800' : 'text-gray-400' }}">{{ $label }}</p>
                                <p class="text-[10px] text-gray-400">{{ $time?->format('d/m H:i') }}</p>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-500">Pesanan ini {{ strtolower($order->status_label) }}. {{ $order->cancel_reason }}</p>
                @endif
            </div>

            {{-- AKSI --}}
            <div class="flex flex-wrap gap-3">
                @if ($order->status === 'pending_payment')
                    <button wire:click="simulatePayment" class="btn-primary">Bayar Sekarang (Simulasi {{ $order->payment_method }})</button>
                    <button wire:click="cancel" wire:confirm="Batalkan pesanan ini?" class="btn-danger">Batalkan Pesanan</button>
                @endif
                @if ($order->status === 'shipped')
                    <button wire:click="confirmReceived" wire:confirm="Pesanan sudah diterima dengan baik?" class="btn-primary">Pesanan Diterima</button>
                @endif
                @if ($order->status === 'completed')
                    <button wire:click="$toggle('showReturn')" class="btn-outline">Ajukan Retur</button>
                @endif
                <a href="{{ route('invoice.show', $order) }}" target="_blank" class="btn-soft">Cetak Invoice</a>
            </div>

            @if ($showReturn)
                <form wire:submit="requestReturn" class="card space-y-3">
                    <label class="label">Alasan Retur</label>
                    <textarea wire:model="returnReason" rows="3" class="input" placeholder="Jelaskan kondisi barang (min. 10 karakter)"></textarea>
                    @error('returnReason')<p class="error">{{ $message }}</p>@enderror
                    <button class="btn-primary">Kirim Pengajuan</button>
                </form>
            @endif

            {{-- RESI --}}
            <div class="card text-sm">
                <h2 class="mb-3 font-display text-lg">Pengiriman</h2>
                <p><span class="text-gray-400">Kurir:</span> {{ $order->courier }} {{ $order->courier_service }}</p>
                <p class="mt-1 flex items-center gap-2"><span class="text-gray-400">No. Resi:</span>
                    @if ($order->tracking_number)
                        <code class="rounded bg-brand-50 px-2 py-0.5 text-brand-700">{{ $order->tracking_number }}</code>
                        <button type="button" x-data @click="navigator.clipboard.writeText('{{ $order->tracking_number }}'); $dispatch('toast', { message: 'Resi disalin' })" class="text-xs text-brand-600 underline">Salin</button>
                    @else <span class="text-gray-400">Belum tersedia</span> @endif
                </p>
                <p class="mt-1"><span class="text-gray-400">Penerima:</span> {{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                <p class="mt-1 text-gray-500">{{ $order->shipping_address }}</p>
            </div>

            {{-- BARANG --}}
            <div class="card">
                <h2 class="mb-4 font-display text-lg">Barang yang Dibeli</h2>
                <div class="divide-y divide-brand-50">
                    @foreach ($order->items as $item)
                        <div class="py-4" wire:key="it-{{ $item->id }}">
                            <div class="flex justify-between gap-4 text-sm">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->variant_label }} · {{ rupiah($item->price) }} × {{ $item->quantity }}</p>
                                </div>
                                <p class="font-medium">{{ rupiah($item->line_total) }}</p>
                            </div>

                            {{-- Form ulasan setelah pesanan selesai --}}
                            @if ($order->status === 'completed' && $item->product_id)
                                @if (isset($reviewed[$item->product_id]))
                                    <p class="mt-2 text-xs text-brand-600">Ulasan Anda: {{ str_repeat('★', $reviewed[$item->product_id]->rating) }} — {{ $reviewed[$item->product_id]->comment }}</p>
                                @else
                                    <div class="mt-3 rounded-xl bg-brand-50/60 p-3">
                                        <div class="flex gap-1">
                                            @for ($s = 1; $s <= 5; $s++)
                                                <button type="button" wire:click="$set('ratings.{{ $item->product_id }}', {{ $s }})"
                                                        class="text-2xl {{ ($ratings[$item->product_id] ?? 0) >= $s ? 'text-brand-500' : 'text-gray-300' }}">★</button>
                                            @endfor
                                        </div>
                                        <textarea wire:model="comments.{{ $item->product_id }}" rows="2" class="input mt-2" placeholder="Ceritakan pengalamanmu…"></textarea>
                                        <button wire:click="submitReview({{ $item->product_id }})" class="btn-primary btn-sm mt-2">Kirim Ulasan</button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                <dl class="mt-4 space-y-1.5 border-t border-brand-100 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Subtotal</dt><dd>{{ rupiah($order->subtotal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Ongkir</dt><dd>{{ rupiah($order->shipping_cost) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Diskon</dt><dd>-{{ rupiah($order->discount) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Biaya layanan</dt><dd>{{ rupiah($order->service_fee) }}</dd></div>
                    <div class="flex justify-between pt-2 text-base font-semibold"><dt>Total</dt><dd class="text-brand-600">{{ rupiah($order->total) }}</dd></div>
                </dl>
            </div>
        </div>
    </x-account-shell>
</div>
