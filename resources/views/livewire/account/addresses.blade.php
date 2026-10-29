<div>
    <x-account-shell title="Buku Alamat">
        <button wire:click="create" class="btn-primary mb-6">+ Tambah Alamat</button>

        @if ($showForm)
            <form wire:submit="save" class="card mb-6 grid gap-4 sm:grid-cols-2">
                <div><label class="label">Label</label><input wire:model="label" class="input">@error('label')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Nama Penerima</label><input wire:model="recipient_name" class="input">@error('recipient_name')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">No. HP</label><input wire:model="phone" class="input">@error('phone')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Kota</label><input wire:model="city" class="input">@error('city')<p class="error">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-2"><label class="label">Alamat Lengkap</label><textarea wire:model="address" rows="2" class="input"></textarea>@error('address')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Kode Pos</label><input wire:model="postal_code" class="input">@error('postal_code')<p class="error">{{ $message }}</p>@enderror</div>
                <div class="flex items-end gap-2">
                    <button class="btn-primary">Simpan</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn-soft">Batal</button>
                </div>
            </form>
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            @forelse ($addresses as $a)
                <div wire:key="a-{{ $a->id }}" class="card !p-5 text-sm">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="font-medium text-gray-800">{{ $a->label }}</span>
                        @if ($a->is_default)<span class="badge-pink">Utama</span>@endif
                    </div>
                    <p>{{ $a->recipient_name }} · {{ $a->phone }}</p>
                    <p class="text-gray-500">{{ $a->full_address }}</p>
                    <div class="mt-4 flex gap-3 text-xs">
                        <button wire:click="edit({{ $a->id }})" class="text-brand-600 hover:underline">Ubah</button>
                        @unless ($a->is_default)<button wire:click="setDefault({{ $a->id }})" class="text-gray-500 hover:underline">Jadikan utama</button>@endunless
                        <button wire:click="delete({{ $a->id }})" wire:confirm="Hapus alamat ini?" class="text-red-500 hover:underline">Hapus</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada alamat tersimpan.</p>
            @endforelse
        </div>
    </x-account-shell>
</div>
