<?php

namespace App\Livewire\Account;

use App\Models\Voucher;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Voucher Saya')]
class Vouchers extends Component
{
    public function render()
    {
        $vouchers = Voucher::where('is_active', true)
            ->whereColumn('used', '<', 'quota')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', today()))
            ->latest()->get();

        return view('livewire.account.vouchers', compact('vouchers'))->layout('components.layouts.app');
    }
}
