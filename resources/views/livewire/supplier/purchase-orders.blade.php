<div>
    <x-page-title title="Manajemen Purchase Order" subtitle="Konfirmasi PO dari Bajuku, lalu kirim barang ke gudang." />

    <div class="mb-5 flex flex-wrap gap-2">
        @foreach ($tabs as $key => $label)
            <button wire:click="setTab('{{ $key }}')" class="rounded-full px-4 py-1.5 text-sm {{ $tab === $key ? 'bg-brand-500 text-white' : 'bg-white text-brand-700 ring-1 ring-brand-100 hover:bg-brand-50' }}">
                {{ $label }} <span class="ml-1 text-xs opacity-70">{{ $counts[$key] ?? 0 }}</span>
            </button>
        @endforeach
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>No. PO</th><th>Tanggal Order</th><th>Batas Kirim</th><th>Total Tagihan</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @forelse ($orders as $po)
                <tr wire:key="po-{{ $po->id }}">
                    <td class="font-medium">{{ $po->po_number }}</td>
                    <td>{{ $po->created_at->format('d M Y') }}</td>
                    <td>{{ $po->expected_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ rupiah($po->total) }}</td>
                    <td><span class="{{ $po->status_badge }}">{{ $po->status_label }}</span></td>
                    <td class="space-x-1 text-right">
                        <button wire:click="open({{ $po->id }})" class="btn-soft btn-sm">Detail</button>
                        @if ($po->status === 'sent')
                            <button wire:click="open({{ $po->id }}, 'confirm')" class="btn-primary btn-sm">Terima</button>
                            <button wire:click="open({{ $po->id }}, 'reject')" class="btn-danger btn-sm">Tolak</button>
                        @elseif ($po->status === 'confirmed')
                            <button wire:click="open({{ $po->id }}, 'ship')" class="btn-primary btn-sm">Kirim Barang</button>
                        @elseif ($po->status === 'shipped')
                            <a href="{{ route('supplier.delivery-note', $po) }}" target="_blank" class="btn-soft btn-sm">Surat Jalan</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-gray-400">Tidak ada PO pada tab ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>

    @if ($detail)
        <div class="modal-backdrop" wire:click.self="close">
            <div class="modal">
                <div class="flex justify-between"><div><h3 class="font-display text-xl">{{ $detail->po_number }}</h3><span class="{{ $detail->status_badge }}">{{ $detail->status_label }}</span></div><button wire:click="close" class="text-gray-400">✕</button></div>

                <table class="mt-4 w-full text-sm">
                    <thead class="text-left text-xs uppercase text-gray-400"><tr><th class="py-2">Barang</th><th>Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
                    @foreach ($detail->items as $i)
                        <tr class="border-t border-brand-50"><td class="py-2">{{ $i->variant->product->name }} <span class="text-gray-400">({{ $i->variant->label }})</span></td><td>{{ $i->qty_ordered }}</td><td class="text-right">{{ rupiah($i->unit_price) }}</td><td class="text-right">{{ rupiah($i->line_total) }}</td></tr>
                    @endforeach
                    <tr class="border-t border-brand-100"><td colspan="3" class="py-2 text-right text-gray-500">Subtotal</td><td class="text-right">{{ rupiah($detail->subtotal) }}</td></tr>
                    <tr><td colspan="3" class="py-1 text-right text-gray-500">PPN</td><td class="text-right">{{ rupiah($detail->tax) }}</td></tr>
                    <tr class="font-semibold"><td colspan="3" class="py-2 text-right">Total</td><td class="text-right text-brand-600">{{ rupiah($detail->total) }}</td></tr>
                </table>
                @if ($detail->notes)<p class="mt-3 rounded-xl bg-brand-50 p-3 text-sm">{{ $detail->notes }}</p>@endif
                @if ($detail->reject_reason)<p class="mt-3 rounded-xl bg-red-50 p-3 text-sm text-red-600">Alasan penolakan: {{ $detail->reject_reason }}</p>@endif

                @if ($mode === 'confirm')
                    <form wire:submit="confirmPo" class="mt-5 space-y-3 border-t border-brand-100 pt-4">
                        <label class="label">Estimasi Tanggal Siap Kirim</label>
                        <input type="date" wire:model="readyDate" class="input">@error('readyDate')<p class="error">{{ $message }}</p>@enderror
                        <button class="btn-primary">Terima PO</button>
                    </form>
                @elseif ($mode === 'reject')
                    <form wire:submit="rejectPo" class="mt-5 space-y-3 border-t border-brand-100 pt-4">
                        <label class="label">Alasan Penolakan</label>
                        <textarea wire:model="rejectReason" rows="2" class="input"></textarea>@error('rejectReason')<p class="error">{{ $message }}</p>@enderror
                        <button class="btn-danger">Tolak PO</button>
                    </form>
                @elseif ($mode === 'ship')
                    <form wire:submit="shipPo" class="mt-5 space-y-3 border-t border-brand-100 pt-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div><label class="label">Armada / Kurir</label><input wire:model="courier" class="input" placeholder="Contoh: CV Maju Jaya">@error('courier')<p class="error">{{ $message }}</p>@enderror</div>
                            <div><label class="label">Plat Kendaraan / Resi</label><input wire:model="plate" class="input" placeholder="Contoh: B 1234 XYZ">@error('plate')<p class="error">{{ $message }}</p>@enderror</div>
                        </div>
                        <button class="btn-primary">Kirim Notifikasi Pengiriman ke Gudang</button>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
