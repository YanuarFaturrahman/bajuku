<div>
    <x-account-shell title="Voucher & Promo">
        <div class="grid gap-4 sm:grid-cols-2">
            @forelse ($vouchers as $v)
                <div class="relative overflow-hidden rounded-2xl border border-dashed border-brand-300 bg-brand-50/60 p-5">
                    <p class="text-xs uppercase tracking-widest text-brand-600">Diskon {{ $v->label }}</p>
                    <p class="mt-1 font-display text-2xl text-gray-900">{{ $v->code }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $v->description }}</p>
                    <p class="mt-3 text-xs text-gray-400">Min. belanja {{ rupiah($v->min_purchase) }}{{ $v->expires_at ? ' · s/d ' . $v->expires_at->format('d M Y') : '' }}</p>
                    <button type="button" x-data @click="navigator.clipboard.writeText('{{ $v->code }}'); $dispatch('toast', { message: 'Kode disalin' })" class="btn-primary btn-sm mt-3">Salin Kode</button>
                </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada voucher aktif saat ini.</p>
            @endforelse
        </div>
    </x-account-shell>
</div>
