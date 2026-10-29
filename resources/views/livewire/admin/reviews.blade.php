<div>
    <x-page-title title="Ulasan Produk" subtitle="Moderasi ulasan pembeli (hapus komentar yang melanggar)." />

    <div class="table-wrap">
        <table>
            <thead><tr><th>Produk</th><th>Pembeli</th><th>Rating</th><th>Komentar</th><th>Tanggal</th><th></th></tr></thead>
            <tbody>
            @forelse ($reviews as $r)
                <tr wire:key="r-{{ $r->id }}">
                    <td class="font-medium">{{ $r->product->name }}</td>
                    <td>{{ $r->user->name }}</td>
                    <td class="text-brand-500">{{ str_repeat('★', $r->rating) }}</td>
                    <td class="max-w-xs truncate text-gray-500">{{ $r->comment }}</td>
                    <td class="text-gray-400">{{ $r->created_at->format('d M Y') }}</td>
                    <td class="text-right"><button wire:click="delete({{ $r->id }})" wire:confirm="Hapus ulasan ini?" class="btn-danger btn-sm">Hapus</button></td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-gray-400">Belum ada ulasan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $reviews->links() }}</div>
</div>
