<?php

namespace App\Livewire\Account;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Review;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Pesanan')]
class OrderDetail extends Component
{
    use Toasts;

    public Order $order;

    public array $ratings = [];
    public array $comments = [];
    public string $returnReason = '';
    public bool $showReturn = false;

    public function mount(Order $order): void
    {
        // Konsumen hanya boleh melihat pesanannya sendiri
        abort_unless($order->user_id === auth()->id(), 403);
        $this->order = $order;
    }

    /** SIMULASI payment gateway: tombol ini mewakili callback "pembayaran berhasil". */
    public function simulatePayment(): void
    {
        if ($this->order->status !== 'pending_payment') return;

        $this->order->markAsPaid();
        ActivityLog::record('order.paid', 'Pembayaran pesanan ' . $this->order->order_number);
        $this->toast('Pembayaran berhasil! Pesanan diteruskan ke gudang.');
    }

    public function cancel(): void
    {
        if ($this->order->status !== 'pending_payment') return;

        $this->order->cancelOrder('Dibatalkan oleh pembeli');
        ActivityLog::record('order.cancel', 'Pembeli membatalkan ' . $this->order->order_number);
        $this->toast('Pesanan dibatalkan');
    }

    public function confirmReceived(): void
    {
        if ($this->order->status !== 'shipped') return;

        $this->order->update(['status' => 'completed', 'completed_at' => now()]);

        // Tambah counter "terjual" pada produk
        foreach ($this->order->items as $item) {
            $item->product?->increment('sold_count', $item->quantity);
        }

        $this->toast('Terima kasih! Jangan lupa beri ulasan.');
    }

    public function requestReturn(): void
    {
        $this->validate(['returnReason' => 'required|min:10']);

        if ($this->order->status !== 'completed') return;

        $this->order->update(['status' => 'return_requested', 'return_reason' => $this->returnReason]);
        $this->showReturn = false;
        $this->toast('Pengajuan retur dikirim ke Admin');
    }

    public function submitReview(int $productId): void
    {
        $rating = (int) ($this->ratings[$productId] ?? 0);

        if ($this->order->status !== 'completed' || $rating < 1 || $rating > 5) {
            $this->toast('Pilih rating bintang 1-5.', 'error');
            return;
        }

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $productId, 'order_id' => $this->order->id],
            ['rating' => $rating, 'comment' => $this->comments[$productId] ?? null]
        );

        $this->toast('Ulasan terkirim. Terima kasih!');
    }

    public function render()
    {
        $this->order->refresh()->load('items.product');

        $steps = [
            'Pesanan Dibuat'   => $this->order->created_at,
            'Terbayar'         => $this->order->paid_at,
            'Diproses Gudang'  => $this->order->packed_at ?? ($this->order->shipped_at ? $this->order->paid_at : null),
            'Dikirim'          => $this->order->shipped_at,
            'Selesai'          => $this->order->completed_at,
        ];

        $reviewed = Review::where('order_id', $this->order->id)->where('user_id', auth()->id())
            ->get()->keyBy('product_id');

        return view('livewire.account.order-detail', compact('steps', 'reviewed'))
            ->layout('components.layouts.app');
    }
}
