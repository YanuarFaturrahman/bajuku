<div>
    <x-account-shell title="Pesanan Saya">
        <div class="mb-6 flex flex-wrap gap-2">
            @foreach ($tabs as $key => $label)
                <button wire:click="setTab('{{ $key }}')" class="rounded-full px-4 py-1.5 text-sm {{ $tab === $key ? 'bg-brand-500 text-white' : 'bg-brand-50 text-brand-700 hover:bg-brand-100' }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse ($orders as $order)
                <a href="{{ route('account.orders.show', $order) }}" wire:key="o-{{ $order->id }}" class="block rounded-2xl border border-brand-100 bg-white p-5 transition hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-medium text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="{{ $order->status_badge }}">{{ $order->status_label }}</span>
                    </div>
                    <p class="mt-3 truncate text-sm text-gray-500">{{ $order->items->pluck('product_name')->join(', ') }}</p>
                    <p class="mt-2 text-sm">Total <strong class="text-brand-600">{{ rupiah($order->total) }}</strong> · {{ $order->items->sum('quantity') }} barang</p>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-brand-200 py-16 text-center text-sm text-gray-400">Belum ada pesanan pada kategori ini.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </x-account-shell>
</div>
