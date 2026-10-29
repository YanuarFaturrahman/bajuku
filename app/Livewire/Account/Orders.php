<?php

namespace App\Livewire\Account;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pesanan Saya')]
class Orders extends Component
{
    use WithPagination;

    public string $tab = 'semua';

    // Kelompok tab => status di database
    protected array $groups = [
        'menunggu'   => ['pending_payment'],
        'diproses'   => ['to_process', 'packing'],
        'dikirim'    => ['shipped'],
        'selesai'    => ['completed', 'return_requested'],
        'dibatalkan' => ['cancelled', 'refunded'],
    ];

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->when($this->tab !== 'semua', fn ($q) => $q->whereIn('status', $this->groups[$this->tab]))
            ->latest()
            ->paginate(8);

        return view('livewire.account.orders', [
            'orders' => $orders,
            'tabs'   => ['semua' => 'Semua', 'menunggu' => 'Menunggu Pembayaran', 'diproses' => 'Diproses',
                         'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'],
        ])->layout('components.layouts.app');
    }
}
