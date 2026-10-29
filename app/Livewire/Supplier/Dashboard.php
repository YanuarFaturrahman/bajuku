<?php

namespace App\Livewire\Supplier;

use App\Models\PurchaseOrder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Dashboard Supplier')]
class Dashboard extends Component
{
    public function render()
    {
        $supplier = auth()->user()->supplier;
        abort_unless($supplier, 403, 'Akun ini belum terhubung ke data supplier.');

        $pos = PurchaseOrder::where('supplier_id', $supplier->id);

        return view('livewire.supplier.dashboard', [
            'supplier'  => $supplier,
            'newPo'     => (clone $pos)->where('status', 'sent')->count(),
            'process'   => (clone $pos)->where('status', 'confirmed')->count(),
            'shipping'  => (clone $pos)->where('status', 'shipped')->count(),
            'done'      => (clone $pos)->where('status', 'received')->count(),
            'unpaid'    => (clone $pos)->where('status', 'received')->where('payment_status', 'unpaid')->sum('total'),
            'paid'      => (clone $pos)->where('payment_status', 'paid')->sum('total'),
            'recent'    => (clone $pos)->latest()->take(5)->get(),
        ]);
    }
}
