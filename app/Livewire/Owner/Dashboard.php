<?php

namespace App\Livewire\Owner;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Dashboard Eksekutif')]
class Dashboard extends Component
{
    public int $days = 14;

    public function render()
    {
        // Penjualan yang dihitung: sudah dibayar & tidak batal/refund
        $valid = function ($q) {
            return $q->where('payment_status', 'paid')
                ->whereNotIn('status', ['cancelled', 'refunded']);
        };

        $gmv        = (int) Order::where($valid)->sum('total');
        $ordersDone = Order::where('status', 'completed')->count();
        $allOrders  = Order::count();
        $customers  = User::where('role', 'konsumen')->count();
        $buyers     = Order::where($valid)->distinct('user_id')->count('user_id');

        // Laba = (harga jual - modal) x qty - diskon voucher
        $itemProfit = (int) OrderItem::whereHas('order', $valid)
            ->sum(DB::raw('(price - cost_price) * quantity'));
        $discounts  = (int) Order::where($valid)->sum('discount');
        $netProfit  = $itemProfit - $discounts;

        $orderCount = Order::where($valid)->count();
        $aov = $orderCount ? (int) round($gmv / $orderCount) : 0;

        // Penjualan harian (bar chart)
        $daily = collect(range($this->days - 1, 0))->map(function ($i) use ($valid) {
            $date = now()->subDays($i)->startOfDay();

            return [
                'label' => $date->format('d/m'),
                'total' => (int) Order::where($valid)
                    ->whereDate('created_at', $date)
                    ->sum('total'),
            ];
        });
        $dailyMax = max(1, $daily->max('total'));

        // Distribusi penjualan per kategori utama
        $rows = OrderItem::select(
                'products.category_id',
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereHas('order', $valid)
            ->groupBy('products.category_id')
            ->get();

        $perCategory = $rows
            ->groupBy(function ($row) {
                $cat = Category::find($row->category_id);

                return $cat?->parent_id ?? $row->category_id;
            })
            ->map(function ($group, $catId) {
                return [
                    'name'    => Category::find($catId)?->name ?? '-',
                    'revenue' => (int) $group->sum('revenue'),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        // Data untuk pie chart (dihitung di sini, bukan di Blade)
        $palette  = ['#f0508a', '#ffa8c9', '#8f2150', '#ffd0e2', '#d93573', '#fda4bf'];
        $pieTotal = max(1, $perCategory->sum('revenue'));
        $acc      = 0;
        $stops    = [];

        foreach ($perCategory as $i => $c) {
            $from    = $acc;
            $acc    += $c['revenue'] / $pieTotal * 100;
            $stops[] = $palette[$i % 6] . ' ' . $from . '% ' . $acc . '%';
        }
        $pieGradient = count($stops) ? implode(',', $stops) : '#ffe8f0 0 100%';

        // Top 5 produk
        $top = OrderItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as qty'),
                DB::raw('SUM(price * quantity) as revenue')
            )
            ->whereHas('order', $valid)
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        // Keandalan supplier
        $suppliers = Supplier::with('purchaseOrders')->get()->map(function ($s) {
            $done   = $s->purchaseOrders->where('status', 'received');
            $onTime = $done->filter(function ($po) {
                return $po->expected_date
                    && $po->received_at
                    && $po->received_at->lte($po->expected_date->endOfDay());
            })->count();

            return [
                'name'   => $s->company_name,
                'total'  => $s->purchaseOrders->count(),
                'done'   => $done->count(),
                'onTime' => $done->count() ? round($onTime / $done->count() * 100) : null,
            ];
        });

        return view('livewire.owner.dashboard', compact(
            'gmv', 'netProfit', 'ordersDone', 'allOrders', 'customers',
            'buyers', 'aov', 'daily', 'dailyMax', 'perCategory', 'palette',
            'pieTotal', 'pieGradient', 'top', 'suppliers'
        ));
    }
}