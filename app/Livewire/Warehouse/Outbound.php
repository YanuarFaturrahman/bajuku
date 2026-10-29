<?php

namespace App\Livewire\Warehouse;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Pengiriman (Outbound)')]
class Outbound extends Component
{
    use Toasts;

    public string $scan = '';
    public ?int $detailId = null;
    public array $tracking = [];   // [orderId => nomor resi]

    /** Input scanner barcode: tekan Enter -> cari pesanan & buka pick list. */
    public function doScan(): void
    {
        $code = trim($this->scan);
        if ($code === '') return;

        $order = Order::whereIn('status', ['to_process', 'packing'])
            ->where(fn ($q) => $q->where('order_number', $code)->orWhere('tracking_number', $code))
            ->first();

        if (! $order) {
            $this->toast('Pesanan "' . $code . '" tidak ada di antrean.', 'error');
        } else {
            $this->detailId = $order->id;
            $this->toast('Pesanan ditemukan: ' . $order->order_number);
        }
        $this->scan = '';
    }

    public function showPickList(int $id): void { $this->detailId = $id; }
    public function closeDetail(): void         { $this->detailId = null; }

    public function startPacking(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status !== 'to_process') return;

        $order->update(['status' => 'packing', 'packed_at' => now()]);
        $this->toast('Status: sedang dikemas');
    }

    /** Konfirmasi serah terima ke kurir + input nomor resi. */
    public function ship(int $id): void
    {
        $resi = trim($this->tracking[$id] ?? '');

        if (strlen($resi) < 5) {
            $this->toast('Isi nomor resi (min. 5 karakter).', 'error');
            return;
        }

        $order = Order::findOrFail($id);
        if (! in_array($order->status, ['to_process', 'packing'])) return;

        $order->update([
            'status'          => 'shipped',
            'tracking_number' => $resi,
            'shipped_at'      => now(),
            'packed_at'       => $order->packed_at ?? now(),
        ]);

        ActivityLog::record('order.ship', "Kirim {$order->order_number} resi {$resi}");
        $this->detailId = null;
        $this->toast('Pesanan diserahkan ke kurir');
    }

    public function render()
    {
        return view('livewire.warehouse.outbound', [
            'orders' => Order::with('items.variant')->whereIn('status', ['to_process', 'packing'])->oldest('paid_at')->get(),
            'detail' => $this->detailId ? Order::with('items.variant')->find($this->detailId) : null,
        ]);
    }
}
