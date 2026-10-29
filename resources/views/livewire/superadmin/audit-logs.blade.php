<div>
    <x-page-title title="Audit Trail" subtitle="Rekaman seluruh aktivitas penting pengguna di sistem." />

    <input type="search" wire:model.live.debounce.400ms="search" class="input mb-4 max-w-sm" placeholder="Cari aksi / deskripsi / pengguna">

    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Deskripsi</th><th>IP</th></tr></thead>
            <tbody>
            @forelse ($logs as $l)
                <tr wire:key="l-{{ $l->id }}">
                    <td class="whitespace-nowrap text-gray-400">{{ $l->created_at->format('d M Y H:i:s') }}</td>
                    <td>{{ $l->user?->name ?? 'Sistem' }}<p class="text-xs text-gray-400">{{ $l->user?->role_label }}</p></td>
                    <td><span class="badge-pink">{{ $l->action }}</span></td>
                    <td class="text-gray-600">{{ $l->description }}</td>
                    <td class="text-xs text-gray-400">{{ $l->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-gray-400">Belum ada log.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
