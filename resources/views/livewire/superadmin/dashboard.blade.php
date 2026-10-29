<div>
    <x-page-title title="Dashboard Sistem" subtitle="Kesehatan sistem dan ringkasan pengguna." />

    <div class="grid gap-4 sm:grid-cols-3 xl:grid-cols-6">
        @foreach (config('rbac.roles') as $key => $label)
            <x-stat-card :label="$label" :value="$byRole[$key] ?? 0" />
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card">
            <h2 class="mb-4 font-display text-lg">Health Check</h2>
            @foreach ($checks as $label => $ok)
                <div class="flex items-center justify-between border-b border-brand-50 py-2.5 text-sm">
                    <span>{{ $label }}</span>
                    <span class="{{ $ok ? 'badge-green' : 'badge-amber' }}">{{ $ok ? 'OK' : 'Perlu dicek' }}</span>
                </div>
            @endforeach
            <p class="mt-3 text-xs text-gray-400">Akun disuspend: {{ $suspended }}</p>
        </div>

        <div class="card">
            <h2 class="mb-4 font-display text-lg">Aktivitas Terakhir</h2>
            @forelse ($logs as $l)
                <div class="border-b border-brand-50 py-2 text-sm">
                    <p><span class="badge-pink">{{ $l->action }}</span> <span class="text-gray-600">{{ $l->description }}</span></p>
                    <p class="text-xs text-gray-400">{{ $l->user?->name ?? 'Sistem' }} · {{ $l->created_at->diffForHumans() }}</p>
                </div>
            @empty <p class="text-sm text-gray-400">Belum ada aktivitas.</p> @endforelse
        </div>
    </div>
</div>
