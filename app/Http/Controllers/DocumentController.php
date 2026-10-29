<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PurchaseOrder;

class DocumentController extends Controller
{
    /** Invoice: boleh dibuka pemilik pesanan, admin, owner, superadmin. */
    public function invoice(Order $order)
    {
        $user = auth()->user();
        abort_unless(
            $order->user_id === $user->id || $user->hasRole('admin', 'owner', 'superadmin'),
            403
        );

        return view('documents.invoice', ['order' => $order->load('items', 'user')]);
    }

    /** Label resi + packing slip: khusus gudang & admin. */
    public function label(Order $order)
    {
        abort_unless(auth()->user()->hasRole('gudang', 'admin'), 403);

        return view('documents.label', ['order' => $order->load('items.variant', 'user')]);
    }

    /** Surat jalan PO: supplier pemilik PO, gudang, superadmin. */
    public function deliveryNote(PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();
        $isOwner = $user->hasRole('supplier') && $purchaseOrder->supplier->user_id === $user->id;
        abort_unless($isOwner || $user->hasRole('gudang', 'superadmin'), 403);

        return view('documents.delivery-note', ['po' => $purchaseOrder->load('items.variant.product', 'supplier')]);
    }
}
