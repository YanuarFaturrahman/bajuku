<?php

namespace App\Livewire\Superadmin;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Pengadaan (Purchase Order)')]
class PurchaseOrders extends Component
{
    use WithPagination, Toasts;

    public bool $showForm = false;

    // Header PO
    public $supplier_id = '';
    public string $expected_date = '';
    public string $due_date = '';
    public string $notes = '';

    // Baris item: [['variant_id' => , 'qty' => , 'price' => ], ...]
    public array $lines = [];

    public function create(): void
    {
        $this->reset('supplier_id', 'expected_date', 'due_date', 'notes', 'lines');
        $this->lines = [['variant_id' => '', 'qty' => 10, 'price' => 0]];
        $this->expected_date = now()->addDays(7)->format('Y-m-d');
        $this->due_date = now()->addDays(14)->format('Y-m-d');
        $this->showForm = true;
    }

    public function addLine(): void
    {
        $this->lines[] = ['variant_id' => '', 'qty' => 10, 'price' => 0];
    }

    public function removeLine(int $i): void
    {
        unset($this->lines[$i]);
        $this->lines = array_values($this->lines);
    }

    /** Pilih varian -> harga beli otomatis terisi dari harga modal produk. */
    public function updatedLines($value, $key): void
    {
        if (str_ends_with($key, '.variant_id') && $value) {
            $i = (int) explode('.', $key)[0];
            $this->lines[$i]['price'] = ProductVariant::with('product')->find($value)?->product->cost_price ?? 0;
        }
    }

    /** Tambahkan otomatis semua varian yang stoknya menipis. */
    public function addLowStock(): void
    {
        $low = ProductVariant::with('product')->where('stock', '<=', config('shop.low_stock'))->get();

        if ($low->isEmpty()) {
            $this->toast('Tidak ada stok menipis.');
            return;
        }

        $this->lines = $low->map(fn ($v) => ['variant_id' => $v->id, 'qty' => 20, 'price' => $v->product->cost_price])->all();
    }

    #[Computed]
    public function subtotal(): int
    {
        return (int) collect($this->lines)->sum(fn ($l) => ((int) $l['qty']) * ((int) $l['price']));
    }

    #[Computed]
    public function tax(): int
    {
        return (int) round($this->subtotal * config('shop.tax_rate') / 100);
    }

    public function publish(): void
    {
        $this->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'expected_date'        => 'required|date',
            'due_date'             => 'required|date|after_or_equal:expected_date',
            'lines'                => 'required|array|min:1',
            'lines.*.variant_id'   => 'required|exists:product_variants,id|distinct',
            'lines.*.qty'          => 'required|integer|min:1',
            'lines.*.price'        => 'required|integer|min:0',
        ], [
            'lines.*.variant_id.required' => 'Pilih produk pada setiap baris.',
            'lines.*.variant_id.distinct' => 'Ada produk yang dipilih dua kali.',
        ]);

        $po = DB::transaction(function () {
            $po = PurchaseOrder::create([
                'po_number'     => PurchaseOrder::generateNumber(),
                'supplier_id'   => $this->supplier_id,
                'created_by'    => auth()->id(),
                'status'        => 'sent',
                'expected_date' => $this->expected_date,
                'due_date'      => $this->due_date,
                'subtotal'      => $this->subtotal,
                'tax'           => $this->tax,
                'total'         => $this->subtotal + $this->tax,
                'notes'         => $this->notes ?: null,
            ]);

            foreach ($this->lines as $l) {
                $po->items()->create([
                    'product_variant_id' => $l['variant_id'],
                    'qty_ordered'        => $l['qty'],
                    'unit_price'         => $l['price'],
                ]);
            }
            return $po;
        });

        ActivityLog::record('po.create', 'Menerbitkan ' . $po->po_number);
        $this->showForm = false;
        $this->toast($po->po_number . ' dikirim ke supplier');
    }

    public function cancelPo(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        if ($po->status !== 'sent') {
            $this->toast('Hanya PO yang belum dikonfirmasi yang bisa dibatalkan.', 'error');
            return;
        }
        $po->update(['status' => 'cancelled']);
        ActivityLog::record('po.cancel', 'Membatalkan ' . $po->po_number);
        $this->toast('PO dibatalkan');
    }

    /** Approval pembayaran: hanya PO yang sudah diterima gudang. */
    public function markPaid(int $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        if ($po->status !== 'received' || $po->payment_status === 'paid') return;

        $po->update(['payment_status' => 'paid', 'paid_at' => now()]);
        ActivityLog::record('po.paid', 'Pembayaran ' . $po->po_number);
        $this->toast('PO ditandai lunas');
    }

    public function render()
    {
        return view('livewire.superadmin.purchase-orders', [
            'orders'    => PurchaseOrder::with('supplier')->latest()->paginate(8),
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'variants'  => $this->showForm ? ProductVariant::with('product')->orderBy('product_id')->get() : collect(),
        ]);
    }
}
