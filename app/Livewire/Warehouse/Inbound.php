<?php

namespace App\Livewire\Warehouse;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Penerimaan Barang (Inbound)')]
class Inbound extends Component
{
    use Toasts;

    public $poId = '';
    public array $received = [];   // [itemId => jumlah diterima]
    public array $rejected = [];   // [itemId => jumlah rusak]
    public string $notes = '';

    /** Saat PO dipilih, isi default jumlah diterima = jumlah dipesan. */
    public function updatedPoId(): void
    {
        $this->received = [];
        $this->rejected = [];
        $this->notes = '';

        if (! $this->poId) return;

        foreach (PurchaseOrder::with('items')->findOrFail($this->poId)->items as $item) {
            $this->received[$item->id] = $item->qty_ordered;
            $this->rejected[$item->id] = 0;
        }
    }

    public function submit(): void
    {
        $po = PurchaseOrder::with('items.variant')->where('status', 'shipped')->findOrFail($this->poId);

        // Validasi tiap baris
        foreach ($po->items as $item) {
            $rec = (int) ($this->received[$item->id] ?? 0);
            $rej = (int) ($this->rejected[$item->id] ?? 0);

            if ($rec < 0 || $rej < 0 || $rej > $rec) {
                $this->toast('Periksa jumlah pada ' . $item->variant->label . ': reject tidak boleh melebihi diterima.', 'error');
                return;
            }
        }

        DB::transaction(function () use ($po) {
            foreach ($po->items as $item) {
                $rec = (int) $this->received[$item->id];
                $rej = (int) $this->rejected[$item->id];
                $good = $rec - $rej;      // hanya barang lolos QC yang masuk stok

                $item->update(['qty_received' => $rec, 'qty_rejected' => $rej]);

                if ($good > 0) {
                    $item->variant->increment('stock', $good);
                    StockMovement::record($item->variant, 'in', $good, $po->po_number, 'Inbound PO' . ($rej ? " (reject {$rej})" : ''));
                }
            }

            $po->update([
                'status'      => 'received',
                'received_at' => now(),
                'notes'       => trim($po->notes . "\nQC Gudang: " . $this->notes),
            ]);
        });

        ActivityLog::record('po.receive', 'Inbound ' . $po->po_number);
        $this->reset('poId', 'received', 'rejected', 'notes');
        $this->toast('Inbound tersimpan & stok fisik diperbarui');
    }

    public function render()
    {
        return view('livewire.warehouse.inbound', [
            'pos'     => PurchaseOrder::with('supplier')->where('status', 'shipped')->latest()->get(),
            'current' => $this->poId ? PurchaseOrder::with('items.variant.product', 'supplier')->find($this->poId) : null,
            'history' => PurchaseOrder::with('supplier')->where('status', 'received')->latest('received_at')->take(5)->get(),
        ]);
    }
}
