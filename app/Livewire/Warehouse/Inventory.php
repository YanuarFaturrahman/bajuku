<?php

namespace App\Livewire\Warehouse;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Manajemen Stok')]
class Inventory extends Component
{
    use WithPagination, Toasts;

    public string $search = '';
    public bool $lowOnly = false;

    // Stock opname
    public ?int $opnameId = null;
    public $physical = '';
    public string $note = '';

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedLowOnly(): void { $this->resetPage(); }

    public function openOpname(int $id): void
    {
        $this->opnameId = $id;
        $this->physical = ProductVariant::findOrFail($id)->stock;
        $this->note = '';
    }

    public function closeOpname(): void
    {
        $this->reset('opnameId', 'physical', 'note');
    }

    /** Samakan stok sistem dengan hasil hitung fisik. */
    public function saveOpname(): void
    {
        $this->validate(['physical' => 'required|integer|min:0', 'note' => 'nullable|string|max:150']);

        $variant = ProductVariant::findOrFail($this->opnameId);
        $diff = (int) $this->physical - $variant->stock;

        if ($diff === 0) {
            $this->toast('Stok sudah sesuai, tidak ada perubahan.');
            $this->closeOpname();
            return;
        }

        $variant->update(['stock' => (int) $this->physical]);
        StockMovement::record($variant, 'adjustment', $diff, null, 'Stock opname: ' . ($this->note ?: 'penyesuaian fisik'));
        ActivityLog::record('stock.opname', "Opname {$variant->sku}: selisih {$diff}");

        $this->closeOpname();
        $this->toast('Stok diperbarui (selisih ' . ($diff > 0 ? '+' : '') . $diff . ')');
    }

    public function render()
    {
        $variants = ProductVariant::with('product')
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('sku', 'like', "%{$this->search}%")
                ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$this->search}%"))))
            ->when($this->lowOnly, fn ($q) => $q->where('stock', '<=', config('shop.low_stock')))
            ->orderBy('stock')->paginate(12);

        return view('livewire.warehouse.inventory', [
            'variants'  => $variants,
            'movements' => StockMovement::with('variant.product', 'user')->latest()->take(8)->get(),
            'opname'    => $this->opnameId ? ProductVariant::with('product')->find($this->opnameId) : null,
        ]);
    }
}
