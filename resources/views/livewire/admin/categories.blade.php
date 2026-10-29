<div>
    <x-page-title title="Kategori & Sub-Kategori" subtitle="Contoh: Baju → Kaos, Kemeja; Jaket → Parka, Denim." />

    <div class="grid gap-6 lg:grid-cols-[340px_1fr]">
        <form wire:submit="save" class="card h-fit space-y-4">
            <h2 class="font-display text-lg">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
            <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="error">{{ $message }}</p>@enderror</div>
            <div>
                <label class="label">Induk (opsional)</label>
                <select wire:model="parent_id" class="input">
                    <option value="">— Kategori utama —</option>
                    @foreach ($parents as $p)@if ($p->id !== $editingId)<option value="{{ $p->id }}">{{ $p->name }}</option>@endif @endforeach
                </select>
                @error('parent_id')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2">
                <button class="btn-primary">Simpan</button>
                @if ($editingId)<button type="button" wire:click="cancel" class="btn-soft">Batal</button>@endif
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>Induk</th><th>Produk</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                @foreach ($categories as $c)
                    <tr wire:key="c-{{ $c->id }}">
                        <td class="font-medium">{{ $c->parent ? '↳ ' : '' }}{{ $c->name }}</td>
                        <td class="text-gray-400">{{ $c->parent?->name ?? '—' }}</td>
                        <td>{{ $c->products_count }}</td>
                        <td class="text-right">
                            <button wire:click="edit({{ $c->id }})" class="btn-soft btn-sm">Edit</button>
                            <button wire:click="delete({{ $c->id }})" wire:confirm="Hapus kategori ini?" class="btn-danger btn-sm">Hapus</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
