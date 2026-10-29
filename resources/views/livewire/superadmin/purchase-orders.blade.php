<div>
    <x-page-title title="Pengadaan Barang" subtitle="Buat PO ke supplier, pantau status, dan setujui pembayaran.">
        <button wire:click="create" class="btn-primary">+ Buat PO Baru</button>
    </x-page-title>

    <div class="table-wrap">
        <table>
            <thead><tr><th>No. PO</th><th>Supplier</th><th>Target Terima</th><th>Total</th><th>Status</th><th>Bayar</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @forelse ($orders as $po)
                <tr wire:key="po-{{ $po->id }}">
                    <td class="font-medium">{{ $po->po_number }}</td>
                    <td>{{ $po->supplier->company_name }}</td>
                    <td>{{ $po->expected_date?->format('d M Y') }}</td>
                    <td>{{ rupiah($po->total) }}</td>
                    <td><span class="{{ $po->status_badge }}">{{ $po->status_label }}</span></td>
                    <td><span class="{{ $po->payment_status === 'paid' ? 'badge-green' : 'badge-amber' }}">{{ $po->payment_status === 'paid' ? 'Lunas' : 'Belum' }}</span></td>
                    <td class="space-x-1 text-right">
                        @if ($po->status === 'sent')<button wire:click="cancelPo({{ $po->id }})" wire:confirm="Batalkan PO?" class="btn-danger btn-sm">Batalkan</button>@endif
                        @if ($po->status === 'received' && $po->payment_status === 'unpaid')<button wire:click="markPaid({{ $po->id }})" wire:confirm="Tandai PO ini sudah dibayar?" class="btn-primary btn-sm">Setujui Pembayaran</button>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-10 text-center text-gray-400">Belum ada PO.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>

    @if ($showForm)
        <div class="modal-backdrop">
            <form wire:submit="publish" class="modal !max-w-4xl space-y-5">
                <div class="flex justify-between"><h3 class="font-display text-xl">Buat Purchase Order</h3><button type="button" wire:click="$set('showForm', false)" class="text-gray-400">✕</button></div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div><label class="label">Supplier</label>
                        <select wire:model="supplier_id" class="input"><option value="">Pilih supplier…</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select>
                        @error('supplier_id')<p class="error">{{ $message }}</p>@enderror</div>
                    <div><label class="label">Target Penerimaan</label><input type="date" wire:model="expected_date" class="input">@error('expected_date')<p class="error">{{ $message }}</p>@enderror</div>
                    <div><label class="label">Jatuh Tempo Bayar</label><input type="date" wire:model="due_date" class="input">@error('due_date')<p class="error">{{ $message }}</p>@enderror</div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <p class="label !mb-0">Item PO</p>
                        <button type="button" wire:click="addLowStock" class="text-xs text-brand-600 underline">Isi otomatis dari stok menipis</button>
                    </div>
                    <div class="space-y-2">
                        @foreach ($lines as $i => $line)
                            <div class="grid grid-cols-[1fr_90px_130px_120px_32px] items-center gap-2" wire:key="line-{{ $i }}">
                                <select wire:model.live="lines.{{ $i }}.variant_id" class="input !py-2">
                                    <option value="">Pilih produk / varian…</option>
                                    @foreach ($variants as $v)<option value="{{ $v->id }}">{{ $v->product->name }} — {{ $v->label }} ({{ $v->sku }})</option>@endforeach
                                </select>
                                <input type="number" min="1" wire:model.live="lines.{{ $i }}.qty" class="input !py-2" placeholder="Qty">
                                <input type="number" min="0" wire:model.live="lines.{{ $i }}.price" class="input !py-2" placeholder="Harga beli">
                                <span class="text-right text-sm font-medium">{{ rupiah(((int) $line['qty']) * ((int) $line['price'])) }}</span>
                                <button type="button" wire:click="removeLine({{ $i }})" class="text-gray-300 hover:text-red-500">✕</button>
                            </div>
                            @error("lines.$i.variant_id")<p class="error">{{ $message }}</p>@enderror
                        @endforeach
                    </div>
                    @error('lines')<p class="error">{{ $message }}</p>@enderror
                    <button type="button" wire:click="addLine" class="btn-soft btn-sm mt-3">+ Tambah Baris</button>
                </div>

                <div><label class="label">Catatan untuk Supplier</label><input wire:model="notes" class="input"></div>

                <div class="ml-auto max-w-xs space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>{{ rupiah($this->subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">PPN {{ config('shop.tax_rate') }}%</span><span>{{ rupiah($this->tax) }}</span></div>
                    <div class="flex justify-between border-t border-brand-100 pt-2 text-base font-semibold"><span>Grand Total</span><span class="text-brand-600">{{ rupiah($this->subtotal + $this->tax) }}</span></div>
                </div>

                <button class="btn-primary w-full">Terbitkan & Kirim PO ke Supplier</button>
            </form>
        </div>
    @endif
</div>
