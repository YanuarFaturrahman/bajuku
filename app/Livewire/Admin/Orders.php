<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Manajemen Pesanan')]
class Orders extends Component
{
    use WithPagination, Toasts;

    public string $tab = 'all';
    public string $search = '';
    public string $courier = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $detailId = null;

    public function setTab(string $tab): void { $this->tab = $tab; $this->resetPage(); }
    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedCourier(): void     { $this->resetPage(); }

    public function showDetail(int $id): void  { $this->detailId = $id; }
    public function closeDetail(): void        { $this->detailId = null; }

    /** Verifikasi pembayaran manual -> pesanan diteruskan ke antrean gudang. */
    public function verifyPayment(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status !== 'pending_payment') return;

        $order->markAsPaid();
        ActivityLog::record('order.verify', 'Verifikasi pembayaran ' . $order->order_number);
        $this->toast('Pembayaran diverifikasi & diteruskan ke Gudang');
    }

    public function cancelOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        if (! in_array($order->status, ['pending_payment', 'to_process'])) {
            $this->toast('Pesanan sudah diproses gudang, tidak bisa dibatalkan.', 'error');
            return;
        }

        $order->cancelOrder('Dibatalkan oleh Admin');
        ActivityLog::record('order.cancel', 'Admin membatalkan ' . $order->order_number);
        $this->toast('Pesanan dibatalkan & stok dikembalikan');
    }

    public function approveReturn(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status !== 'return_requested') return;

        $order->refund();
        ActivityLog::record('order.refund', 'Refund retur ' . $order->order_number);
        $this->toast('Retur disetujui, stok dikembalikan');
    }

    public function rejectReturn(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status !== 'return_requested') return;

        $order->update(['status' => 'completed']);
        ActivityLog::record('order.return.reject', 'Retur ditolak ' . $order->order_number);
        $this->toast('Pengajuan retur ditolak');
    }

    public function render()
    {
        $groups = [
            'pending_payment' => ['pending_payment'],
            'to_process'      => ['to_process', 'packing'],
            'shipped'         => ['shipped'],
            'completed'       => ['completed'],
            'cancelled'       => ['cancelled', 'refunded'],
            'return'          => ['return_requested'],
        ];

        $orders = Order::with('user')
            ->when($this->tab !== 'all', fn ($q) => $q->whereIn('status', $groups[$this->tab]))
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('order_number', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"))))
            ->when($this->courier, fn ($q) => $q->where('courier', $this->courier))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()->paginate(10);

        return view('livewire.admin.orders', [
            'orders' => $orders,
            'detail' => $this->detailId ? Order::with('items', 'user')->find($this->detailId) : null,
            'tabs'   => ['all' => 'Semua', 'pending_payment' => 'Belum Bayar', 'to_process' => 'Perlu Diproses',
                         'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Batal', 'return' => 'Retur'],
        ]);
    }
}
