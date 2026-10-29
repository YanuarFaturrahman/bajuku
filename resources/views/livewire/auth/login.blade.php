<div class="mx-auto max-w-md px-4 py-16">
    <div class="card !p-8">
        <h1 class="text-center font-display text-3xl">Selamat Datang</h1>
        <p class="mb-8 mt-2 text-center text-sm text-gray-500">Masuk untuk melanjutkan belanja atau mengelola toko.</p>

        <form wire:submit="login" class="space-y-5">
            <div>
                <label class="label">Email</label>
                <input type="email" wire:model="email" class="input" placeholder="nama@email.com" autofocus>
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Password</label>
                <input type="password" wire:model="password" class="input" placeholder="••••••••">
                @error('password')<p class="error">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-500">
                <input type="checkbox" wire:model="remember" class="rounded accent-pink-500"> Ingat saya
            </label>
            <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">Memproses…</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:underline">Daftar sekarang</a></p>
    </div>
</div>
