<div>
    <x-page-title title="Manajemen Pesanan" subtitle="Verifikasi pembayaran, pantau status, dan tangani retur." />

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($tabs as $key => $label)
            <button wire:click="setTab('{{ $key }}')" class="rounded-full px-4 py-1.5 text-sm {{ $tab === $key ? 'bg-brand-500 text-white' : 'bg-white text-brand-700 ring-1 ring-brand-100 hover:bg-brand-50' }}">{{ $label }}</button>
        @endforeach
    </div>

    <div class="mb-4 flex flex-wrap gap-3">
        <input type="search" wire:model.live.debounce.400ms="search" class="input max-w-xs" placeholder="No. pesanan / nama konsumen">
        <select wire:model.live="courier" class="input max-w-[160px]">
            <option value="">Semua kurir</option>
            @foreach (array_keys(config('shop.couriers')) as $c)<option>{{ $c }}</option>@endforeach
        </select>
        <input type="date" wire:model.live="dateFrom" class="input max-w-[160px]">
        <input type="date" wire:model.live="dateTo" class="input max-w-[160px]">
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>ID Pesanan</th><th>Tanggal</th><th>Konsumen</th><th>Total</th><th>Pembayaran</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @forelse ($orders as $o)
                <tr wire:key="o-{{ $o->id }}">
                    <td class="font-medium">{{ $o->order_number }}</td>
                    <td class="text-gray-400">{{ $o->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $o->user->name }}</td>
                    <td>{{ rupiah($o->total) }}</td>
                    <td class="text-xs">{{ $o->payment_method }}<br><span class="text-gray-400">{{ $o->payment_status }}</span></td>
                    <td><span class="{{ $o->status_badge }}">{{ $o->status_label }}</span></td>
                    <td class="space-x-1 text-right">
                        <button wire:click="showDetail({{ $o->id }})" class="btn-soft btn-sm">Detail</button>
                        <a href="{{ route('invoice.show', $o) }}" target="_blank" class="btn-soft btn-sm">Invoice</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-10 text-center text-gray-400">Tidak ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>

    {{-- MODAL DETAIL --}}
    @if ($detail)
        <div class="modal-backdrop" wire:click.self="closeDetail">
            <div class="modal">
                <div class="flex items-start justify-between">
                    <div><h3 class="font-display text-xl">{{ $detail->order_number }}</h3><span class="{{ $detail->status_badge }}">{{ $detail->status_label }}</span></div>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-700">✕</button>
                </div>

                <div class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div><p class="label">Penerima</p>{{ $detail->recipient_name }} ({{ $detail->recipient_phone }})<br><span class="text-gray-500">{{ $detail->shipping_address }}</span></div>
                    <div><p class="label">Pengiriman</p>{{ $detail->courier }} {{ $detail->courier_service }}<br>Resi: {{ $detail->tracking_number ?? '-' }}</div>
                </div>

                <table class="mt-4 w-full text-sm">
                    @foreach ($detail->items as $i)
                        <tr class="border-t border-brand-50"><td class="py-2">{{ $i->product_name }} <span class="text-xs text-gray-400">({{ $i->variant_label }}) ×{{ $i->quantity }}</span></td><td class="text-right">{{ rupiah($i->line_total) }}</td></tr>
                    @endforeach
                    <tr class="border-t border-brand-100 font-semibold"><td class="py-2">Total</td><td class="text-right text-brand-600">{{ rupiah($detail->total) }}</td></tr>
                </table>

                @if ($detail->return_reason)
                    <p class="mt-4 rounded-xl bg-orange-50 p-3 text-sm text-orange-700"><strong>Alasan retur:</strong> {{ $detail->return_reason }}</p>
                @endif

                <div class="mt-6 flex flex-wrap gap-2">
                    @if ($detail->status === 'pending_payment')
                        <button wire:click="verifyPayment({{ $detail->id }})" class="btn-primary btn-sm">Verifikasi Bayar & Teruskan ke Gudang</button>
                    @endif
                    @if (in_array($detail->status, ['pending_payment', 'to_process']))
                        <button wire:click="cancelOrder({{ $detail->id }})" wire:confirm="Batalkan pesanan ini?" class="btn-danger btn-sm">Batalkan</button>
                    @endif
                    @if ($detail->status === 'return_requested')
                        <button wire:click="approveReturn({{ $detail->id }})" wire:confirm="Setujui refund?" class="btn-primary btn-sm">Setujui Refund</button>
                        <button wire:click="rejectReturn({{ $detail->id }})" class="btn-danger btn-sm">Tolak Retur</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
