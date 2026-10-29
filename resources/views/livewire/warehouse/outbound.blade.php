<div>
    <x-page-title title="Packing & Shipping Console" subtitle="Scan, kemas, cetak label, lalu serahkan ke kurir." />

    {{-- Scanner --}}
    <form wire:submit="doScan" class="card mb-6 flex flex-wrap items-center gap-3 !p-4">
        <span class="text-sm font-medium text-gray-600">Scan Barcode</span>
        <input wire:model="scan" autofocus class="input max-w-md" placeholder="Arahkan scanner / ketik No. Pesanan lalu Enter">
        <button class="btn-dark">Cari</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Order ID</th><th>Rak Lokasi</th><th>Item</th><th>Kurir</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @forelse ($orders as $o)
                <tr wire:key="o-{{ $o->id }}">
                    <td class="font-medium">{{ $o->order_number }}<p class="text-xs font-normal text-gray-400">{{ $o->recipient_name }}</p></td>
                    <td>{{ $o->items->map(fn ($i) => $i->variant?->rack_location)->filter()->unique()->join(', ') ?: '—' }}</td>
                    <td class="text-xs">{{ $o->items->map(fn ($i) => $i->variant_label . ' ×' . $i->quantity)->join(', ') }}</td>
                    <td>{{ $o->courier }} {{ $o->courier_service }}</td>
                    <td><span class="{{ $o->status_badge }}">{{ $o->status_label }}</span></td>
                    <td class="space-x-1 text-right">
                        <button wire:click="showPickList({{ $o->id }})" class="btn-soft btn-sm">Pick List</button>
                        <a href="{{ route('warehouse.label', $o) }}" target="_blank" class="btn-soft btn-sm">Cetak Label</a>
                        @if ($o->status === 'to_process')
                            <button wire:click="startPacking({{ $o->id }})" class="btn-primary btn-sm">Mulai Packing</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-12 text-center text-gray-400">Tidak ada pesanan yang perlu dikirim. 🎉</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL PICK LIST + RESI --}}
    @if ($detail)
        <div class="modal-backdrop" wire:click.self="closeDetail">
            <div class="modal !max-w-xl">
                <div class="flex justify-between"><h3 class="font-display text-xl">Pick List — {{ $detail->order_number }}</h3><button wire:click="closeDetail" class="text-gray-400">✕</button></div>
                <p class="mt-1 text-sm text-gray-500">Ambil barang berikut dari rak:</p>
                <table class="mt-4 w-full text-sm">
                    <thead class="text-left text-xs uppercase text-gray-400"><tr><th class="py-2">Rak</th><th>Produk</th><th>Varian</th><th class="text-right">Qty</th></tr></thead>
                    @foreach ($detail->items as $i)
                        <tr class="border-t border-brand-50"><td class="py-2 font-medium text-brand-600">{{ $i->variant?->rack_location ?? '-' }}</td><td>{{ $i->product_name }}</td><td>{{ $i->variant_label }}</td><td class="text-right font-semibold">{{ $i->quantity }}</td></tr>
                    @endforeach
                </table>

                <div class="mt-6 border-t border-brand-100 pt-5">
                    <label class="label">Nomor Resi Kurir ({{ $detail->courier }})</label>
                    <div class="flex gap-2">
                        <input wire:model="tracking.{{ $detail->id }}" class="input" placeholder="Masukkan / scan nomor resi">
                        <button wire:click="ship({{ $detail->id }})" class="btn-primary whitespace-nowrap">Serahkan ke Kurir</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
