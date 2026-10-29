<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Dashboard Admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'unpaid'      => Order::where('status', 'pending_payment')->count(),
            'toProcess'   => Order::whereIn('status', ['to_process', 'packing'])->count(),
            'returns'     => Order::where('status', 'return_requested')->count(),
            'products'    => Product::count(),
            'lowStock'    => ProductVariant::where('stock', '<=', config('shop.low_stock'))->count(),
            'recent'      => Order::with('user')->latest()->take(6)->get(),
        ]);
    }
}
