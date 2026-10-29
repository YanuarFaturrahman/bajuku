<div class="mx-auto max-w-md px-4 py-16">
    <div class="card !p-8">
        <h1 class="text-center font-display text-3xl">Buat Akun</h1>
        <p class="mb-8 mt-2 text-center text-sm text-gray-500">Daftar gratis dan dapatkan voucher selamat datang.</p>

        <form wire:submit="register" class="space-y-4">
            <div>
                <label class="label">Nama Lengkap</label>
                <input type="text" wire:model="name" class="input">
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Email</label>
                <input type="email" wire:model="email" class="input">
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">No. HP / WhatsApp</label>
                <input type="text" wire:model="phone" class="input" placeholder="08xxxxxxxxxx">
                @error('phone')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Password</label>
                    <input type="password" wire:model="password" class="input">
                    @error('password')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Ulangi Password</label>
                    <input type="password" wire:model="password_confirmation" class="input">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">Daftar</button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:underline">Masuk</a></p>
    </div>
</div>
