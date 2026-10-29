<?php

namespace App\Livewire\Supplier;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Purchase Order')]
class PurchaseOrders extends Component
{
    use WithPagination, Toasts;

    public string $tab = 'sent';
    public ?int $detailId = null;

    // Form aksi
    public string $mode = '';          // confirm | reject | ship
    public string $readyDate = '';
    public string $rejectReason = '';
    public string $courier = '';
    public string $plate = '';

    public function setTab(string $tab): void { $this->tab = $tab; $this->resetPage(); }

    public function open(int $id, string $mode = ''): void
    {
        $this->detailId = $id;
        $this->mode = $mode;
        $this->reset('readyDate', 'rejectReason', 'courier', 'plate');
        $this->resetValidation();
    }

    public function close(): void
    {
        $this->reset('detailId', 'mode');
    }

    /** Query yang HANYA mengambil PO milik supplier yang sedang login. */
    protected function ownPo(int $id): PurchaseOrder
    {
        return PurchaseOrder::where('supplier_id', auth()->user()->supplier->id)->findOrFail($id);
    }

    public function confirmPo(): void
    {
        $this->validate(['readyDate' => 'required|date|after_or_equal:today']);
        $po = $this->ownPo($this->detailId);
        if ($po->status !== 'sent') return;

        $po->update(['status' => 'confirmed', 'estimated_ready_date' => $this->readyDate]);
        ActivityLog::record('po.confirm', $po->po_number . ' dikonfirmasi supplier');
        $this->close();
        $this->toast('PO diterima. Silakan siapkan barang.');
    }

    public function rejectPo(): void
    {
        $this->validate(['rejectReason' => 'required|min:5']);
        $po = $this->ownPo($this->detailId);
        if ($po->status !== 'sent') return;

        $po->update(['status' => 'rejected', 'reject_reason' => $this->rejectReason]);
        ActivityLog::record('po.reject', $po->po_number . ' ditolak supplier');
        $this->close();
        $this->toast('PO ditolak');
    }

    public function shipPo(): void
    {
        $this->validate(['courier' => 'required|string|max:60', 'plate' => 'required|string|max:40']);
        $po = $this->ownPo($this->detailId);
        if ($po->status !== 'confirmed') return;

        $po->update([
            'status' => 'shipped', 'courier' => $this->courier, 'vehicle_plate' => $this->plate, 'shipped_at' => now(),
        ]);
        ActivityLog::record('po.ship', $po->po_number . ' dikirim ke gudang');
        $this->close();
        $this->toast('Pengiriman dicatat. Gudang akan diberi tahu.');
    }

    public function render()
    {
        $supplierId = auth()->user()->supplier?->id;
        abort_unless($supplierId, 403);

        $query = PurchaseOrder::where('supplier_id', $supplierId);
        $counts = (clone $query)->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        return view('livewire.supplier.purchase-orders', [
            'orders' => $query->where('status', $this->tab)->latest()->paginate(8),
            'counts' => $counts,
            'detail' => $this->detailId ? PurchaseOrder::with('items.variant.product')->where('supplier_id', $supplierId)->find($this->detailId) : null,
            'tabs'   => ['sent' => 'PO Masuk', 'confirmed' => 'Diproses', 'shipped' => 'Dikirim', 'received' => 'Selesai', 'rejected' => 'Ditolak'],
        ]);
    }
}
