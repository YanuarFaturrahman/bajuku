<?php

namespace App\Livewire\Owner;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Laporan & Analisis')]
class Reports extends Component
{
    public string $tab = 'penjualan';
    public string $from = '';
    public string $to = '';

    public function mount(): void
    {
        $this->from = now()->subDays(29)->format('Y-m-d');
        $this->to   = now()->format('Y-m-d');
    }

    public function render()
    {
        $valid = fn ($q) => $q->where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to);

        $data = [];

        if ($this->tab === 'penjualan') {
            $data['sales'] = Order::where($valid)
                ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as gmv'), DB::raw('SUM(shipping_cost) as shipping'))
                ->groupBy('day')->orderByDesc('day')->get();
        }

        if ($this->tab === 'keuangan') {
            $revenue  = (int) OrderItem::whereHas('order', $valid)->sum(DB::raw('price * quantity'));
            $cogs     = (int) OrderItem::whereHas('order', $valid)->sum(DB::raw('cost_price * quantity'));
            $discount = (int) Order::where($valid)->sum('discount');
            $fee      = (int) Order::where($valid)->sum('service_fee');

            $data['pl'] = [
                'revenue' => $revenue, 'cogs' => $cogs, 'discount' => $discount, 'fee' => $fee,
                'gross' => $revenue - $cogs - $discount,
            ];

            $data['margins'] = OrderItem::select('products.category_id', DB::raw('SUM(order_items.price * order_items.quantity) as revenue'), DB::raw('SUM(order_items.cost_price * order_items.quantity) as cogs'))
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->whereHas('order', $valid)->groupBy('products.category_id')->get()
                ->map(fn ($r) => [
                    'name'    => Category::find($r->category_id)?->full_name ?? '-',
                    'revenue' => (int) $r->revenue,
                    'profit'  => (int) ($r->revenue - $r->cogs),
                    'margin'  => $r->revenue ? round(($r->revenue - $r->cogs) / $r->revenue * 100, 1) : 0,
                ]);

            $data['poPaid'] = (int) PurchaseOrder::where('payment_status', 'paid')->sum('total');
            $data['poUnpaid'] = (int) PurchaseOrder::where('status', 'received')->where('payment_status', 'unpaid')->sum('total');
        }

        if ($this->tab === 'produk') {
            $data['best'] = OrderItem::select('product_name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(price*quantity) as revenue'))
                ->whereHas('order', $valid)->groupBy('product_name')->orderByDesc('qty')->take(10)->get();

            // Dead stock: stok > 0 tetapi tidak terjual sama sekali di periode
            $data['dead'] = ProductVariant::with('product')->where('stock', '>', 0)
                ->whereDoesntHave('orderItems', fn ($q) => $q->whereHas('order', $valid))
                ->orderByDesc('stock')->take(10)->get();
        }

        if ($this->tab === 'logistik') {
            $data['couriers'] = Order::where($valid)->select('courier', DB::raw('COUNT(*) as orders'), DB::raw('SUM(shipping_cost) as cost'))
                ->groupBy('courier')->get();
        }

        return view('livewire.owner.reports', $data + [
            'tabs' => ['penjualan' => 'Penjualan (GMV)', 'keuangan' => 'Keuangan (P&L)', 'produk' => 'Produk & Tren', 'logistik' => 'Logistik'],
        ]);
    }
}
