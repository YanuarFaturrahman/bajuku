<?php

namespace App\Livewire\Warehouse;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Dashboard Gudang')]
class Dashboard extends Component
{
    public function render()
    {
        $since = now()->subDays(30);

        // Fast moving: varian paling banyak terjual 30 hari terakhir
        $fast = OrderItem::select('product_variant_id', DB::raw('SUM(quantity) as qty'))
            ->whereNotNull('product_variant_id')
            ->whereHas('order', fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded'])->where('created_at', '>=', $since))
            ->groupBy('product_variant_id')->orderByDesc('qty')->take(5)
            ->with('variant.product')->get();

        // Slow moving: stok ada tetapi tidak terjual 30 hari terakhir
        $slow = ProductVariant::with('product')->where('stock', '>', 0)
            ->whereDoesntHave('orderItems', fn ($q) => $q->where('created_at', '>=', $since))
            ->orderByDesc('stock')->take(5)->get();

        return view('livewire.warehouse.dashboard', [
            'toProcess' => Order::where('status', 'to_process')->count(),
            'packing'   => Order::where('status', 'packing')->count(),
            'lowStock'  => ProductVariant::where('stock', '<=', config('shop.low_stock'))->count(),
            'incoming'  => PurchaseOrder::where('status', 'shipped')->count(),
            'fast'      => $fast,
            'slow'      => $slow,
        ]);
    }
}
