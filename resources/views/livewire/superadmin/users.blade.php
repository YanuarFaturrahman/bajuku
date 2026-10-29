<div>
    <x-page-title title="Manajemen Pengguna" subtitle="Kelola akun dan role (Admin, Gudang, Supplier, Owner, Superadmin).">
        <button wire:click="create" class="btn-primary">+ Pengguna Baru</button>
    </x-page-title>

    <div class="mb-4 flex flex-wrap gap-3">
        <input type="search" wire:model.live.debounce.400ms="search" class="input max-w-xs" placeholder="Cari nama / email">
        <select wire:model.live="roleFilter" class="input max-w-[180px]">
            <option value="">Semua role</option>
            @foreach (config('rbac.roles') as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach
        </select>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Login Terakhir</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @foreach ($users as $u)
                <tr wire:key="u-{{ $u->id }}">
                    <td class="font-medium">{{ $u->name }}</td>
                    <td class="text-gray-500">{{ $u->email }}</td>
                    <td><span class="badge-pink">{{ $u->role_label }}</span></td>
                    <td><button wire:click="toggleActive({{ $u->id }})" class="{{ $u->is_active ? 'badge-green' : 'badge-red' }}">{{ $u->is_active ? 'Aktif' : 'Suspend' }}</button></td>
                    <td class="text-xs text-gray-400">{{ $u->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</td>
                    <td class="space-x-1 text-right">
                        <button wire:click="edit({{ $u->id }})" class="btn-soft btn-sm">Edit</button>
                        <button wire:click="delete({{ $u->id }})" wire:confirm="Hapus pengguna ini?" class="btn-danger btn-sm">Hapus</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>

    @if ($showForm)
        <div class="modal-backdrop" wire:click.self="$set('showForm', false)">
            <form wire:submit="save" class="modal !max-w-lg space-y-4">
                <h3 class="font-display text-xl">{{ $editingId ? 'Edit Pengguna' : 'Pengguna Baru' }}</h3>
                <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="error">{{ $message }}</p>@enderror</div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="label">Email</label><input wire:model="email" class="input">@error('email')<p class="error">{{ $message }}</p>@enderror</div>
                    <div><label class="label">No. HP</label><input wire:model="phone" class="input"></div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="label">Role</label>
                        <select wire:model.live="role" class="input">@foreach (config('rbac.roles') as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select>
                        @error('role')<p class="error">{{ $message }}</p>@enderror</div>
                    <div><label class="label">Password {{ $editingId ? '(kosongkan jika tetap)' : '' }}</label><input type="password" wire:model="password" class="input">@error('password')<p class="error">{{ $message }}</p>@enderror</div>
                </div>
                @if ($role === 'supplier')
                    <div><label class="label">Nama Perusahaan Supplier</label><input wire:model="company_name" class="input">@error('company_name')<p class="error">{{ $message }}</p>@enderror</div>
                @endif
                <div class="flex gap-2"><button class="btn-primary">Simpan</button><button type="button" wire:click="$set('showForm', false)" class="btn-soft">Batal</button></div>
            </form>
        </div>
    @endif
</div>
