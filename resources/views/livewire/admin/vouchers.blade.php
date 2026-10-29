<div>
    <x-page-title title="Voucher & Promo" subtitle="Kode diskon yang bisa dipakai konsumen saat checkout.">
        <button wire:click="create" class="btn-primary">+ Voucher Baru</button>
    </x-page-title>

    @if ($showForm)
        <form wire:submit="save" class="card mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div><label class="label">Kode</label><input wire:model="code" class="input uppercase">@error('code')<p class="error">{{ $message }}</p>@enderror</div>
            <div class="lg:col-span-3"><label class="label">Deskripsi</label><input wire:model="description" class="input"></div>
            <div><label class="label">Tipe</label>
                <select wire:model.live="type" class="input"><option value="percent">Persen (%)</option><option value="fixed">Nominal (Rp)</option></select></div>
            <div><label class="label">Nilai</label><input type="number" wire:model="value" class="input">@error('value')<p class="error">{{ $message }}</p>@enderror</div>
            <div><label class="label">Min. Belanja</label><input type="number" wire:model="min_purchase" class="input"></div>
            <div><label class="label">Maks. Potongan</label><input type="number" wire:model="max_discount" class="input" placeholder="opsional"></div>
            <div><label class="label">Kuota</label><input type="number" wire:model="quota" class="input"></div>
            <div><label class="label">Kedaluwarsa</label><input type="date" wire:model="expires_at" class="input"></div>
            <label class="flex items-end gap-2 pb-2 text-sm"><input type="checkbox" wire:model="is_active" class="rounded accent-pink-500"> Aktif</label>
            <div class="flex items-end gap-2"><button class="btn-primary">Simpan</button><button type="button" wire:click="$set('showForm', false)" class="btn-soft">Batal</button></div>
        </form>
    @endif

    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Potongan</th><th>Min. Belanja</th><th>Terpakai</th><th>Berlaku s/d</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @forelse ($vouchers as $v)
                <tr wire:key="v-{{ $v->id }}">
                    <td class="font-medium">{{ $v->code }}<p class="text-xs font-normal text-gray-400">{{ $v->description }}</p></td>
                    <td>{{ $v->label }}</td>
                    <td>{{ rupiah($v->min_purchase) }}</td>
                    <td>{{ $v->used }} / {{ $v->quota }}</td>
                    <td>{{ $v->expires_at?->format('d M Y') ?? '—' }}</td>
                    <td><span class="{{ $v->is_active ? 'badge-green' : 'badge-gray' }}">{{ $v->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="text-right">
                        <button wire:click="edit({{ $v->id }})" class="btn-soft btn-sm">Edit</button>
                        <button wire:click="delete({{ $v->id }})" wire:confirm="Hapus voucher?" class="btn-danger btn-sm">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-10 text-center text-gray-400">Belum ada voucher.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
