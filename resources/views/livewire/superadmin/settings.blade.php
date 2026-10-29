<div>
    <x-page-title title="Pengaturan Sistem & Integrasi" subtitle="Konfigurasi toko, payment gateway, API kurir, dan template notifikasi." />

    <form wire:submit="save" class="space-y-6">
        <div class="card space-y-4">
            <h2 class="font-display text-lg">Umum</h2>
            <div class="max-w-md"><label class="label">Nama Toko</label><input wire:model="store_name" class="input">@error('store_name')<p class="error">{{ $message }}</p>@enderror</div>
        </div>

        <div class="card space-y-4">
            <h2 class="font-display text-lg">Payment Gateway</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div><label class="label">Provider</label><select wire:model="pg_provider" class="input"><option value="midtrans">Midtrans</option><option value="xendit">Xendit</option></select></div>
                <div><label class="label">Mode</label><select wire:model="pg_mode" class="input"><option value="sandbox">Sandbox</option><option value="production">Production</option></select></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="label">Server Key</label><input type="password" wire:model="pg_server_key" class="input" autocomplete="off"></div>
                <div><label class="label">Client Key</label><input wire:model="pg_client_key" class="input"></div>
            </div>
            <p class="text-xs text-gray-400">Pada versi tutorial ini pembayaran memakai simulasi. Key tersimpan di tabel settings untuk dipakai saat integrasi nyata.</p>
        </div>

        <div class="card space-y-4">
            <h2 class="font-display text-lg">API Logistik</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="label">Provider</label><select wire:model="logistics_provider" class="input"><option value="rajaongkir">RajaOngkir</option><option value="biteship">Biteship</option></select></div>
                <div><label class="label">API Key</label><input type="password" wire:model="logistics_api_key" class="input" autocomplete="off"></div>
            </div>
        </div>

        <div class="card space-y-4">
            <h2 class="font-display text-lg">Template Notifikasi</h2>
            <div><label class="label">Template WhatsApp</label><textarea wire:model="wa_template" rows="3" class="input" placeholder="Halo {nama}, pesanan {order} sedang dikemas."></textarea></div>
            <div><label class="label">Template Email</label><textarea wire:model="email_template" rows="3" class="input"></textarea></div>
        </div>

        <button class="btn-primary">Simpan Pengaturan</button>
    </form>
</div>
