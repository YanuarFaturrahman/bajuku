<div>
    <x-page-title title="Penerimaan Barang Supplier" subtitle="Cek fisik & Quality Control barang kiriman PO." />

    <div class="card mb-6">
        <label class="label">Pilih Nomor PO yang Tiba</label>
        <select wire:model.live="poId" class="input max-w-md">
            <option value="">— pilih PO —</option>
            @foreach ($pos as $p)<option value="{{ $p->id }}">{{ $p->po_number }} · {{ $p->supplier->company_name }}</option>@endforeach
        </select>
        @if ($pos->isEmpty())<p class="mt-2 text-sm text-gray-400">Belum ada PO berstatus "Dalam Pengiriman".</p>@endif
    </div>

    @if ($current)
        <form wire:submit="submit" class="card space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-display text-lg">Checklist QC — {{ $current->po_number }}</h2>
                <a href="{{ route('supplier.delivery-note', $current) }}" target="_blank" class="btn-soft btn-sm">Lihat Surat Jalan</a>
            </div>
            <p class="text-sm text-gray-500">Supplier: {{ $current->supplier->company_name }} · Armada: {{ $current->courier }} ({{ $current->vehicle_plate }})</p>

            <div class="table-wrap">
                <table>
                    <thead><tr><th>Item</th><th>Dipesan</th><th>Diterima</th><th>Rusak / Reject</th><th>Masuk Stok</th></tr></thead>
                    <tbody>
                    @foreach ($current->items as $item)
                        <tr wire:key="i-{{ $item->id }}">
                            <td class="font-medium">{{ $item->variant->product->name }} <span class="text-gray-400">({{ $item->variant->label }})</span></td>
                            <td>{{ $item->qty_ordered }}</td>
                            <td><input type="number" min="0" wire:model.live="received.{{ $item->id }}" class="input !w-24 !py-1.5"></td>
                            <td><input type="number" min="0" wire:model.live="rejected.{{ $item->id }}" class="input !w-24 !py-1.5"></td>
                            <td class="font-semibold text-emerald-600">{{ max(0, (int) ($received[$item->id] ?? 0) - (int) ($rejected[$item->id] ?? 0)) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div><label class="label">Catatan Kondisi / Barang Cacat</label><textarea wire:model="notes" rows="2" class="input"></textarea></div>
            <button class="btn-primary">Submit Inbound & Update Stok Fisik</button>
        </form>
    @endif

    @if ($history->count())
        <h2 class="mb-3 mt-10 font-display text-lg">Inbound Terakhir</h2>
        <div class="table-wrap"><table>
            <thead><tr><th>No. PO</th><th>Supplier</th><th>Diterima</th></tr></thead>
            <tbody>@foreach ($history as $h)<tr><td class="font-medium">{{ $h->po_number }}</td><td>{{ $h->supplier->company_name }}</td><td class="text-gray-400">{{ $h->received_at->format('d M Y H:i') }}</td></tr>@endforeach</tbody>
        </table></div>
    @endif
</div>
