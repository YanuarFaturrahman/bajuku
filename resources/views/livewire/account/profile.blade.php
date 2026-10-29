<div>
    <x-account-shell title="Profil Saya">
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Total Pesanan" :value="$orderCount" />
            <x-stat-card label="Wishlist" :value="$wishCount" />
            <x-stat-card label="Email" :value="auth()->user()->email" />
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <form wire:submit="saveProfile" class="card space-y-4">
                <h2 class="font-display text-lg">Data Diri</h2>
                <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">No. HP</label><input wire:model="phone" class="input">@error('phone')<p class="error">{{ $message }}</p>@enderror</div>
                <button class="btn-primary">Simpan Perubahan</button>
            </form>

            <form wire:submit="savePassword" class="card space-y-4">
                <h2 class="font-display text-lg">Ganti Password</h2>
                <div><label class="label">Password Saat Ini</label><input type="password" wire:model="current_password" class="input">@error('current_password')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Password Baru</label><input type="password" wire:model="password" class="input">@error('password')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Ulangi Password Baru</label><input type="password" wire:model="password_confirmation" class="input"></div>
                <button class="btn-dark">Ganti Password</button>
            </form>
        </div>
    </x-account-shell>
</div>
