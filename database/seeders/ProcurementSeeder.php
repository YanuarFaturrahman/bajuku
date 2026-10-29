<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProcurementSeeder extends Seeder
{
    /**
     * Membuat 4 PO contoh dengan status berbeda
     * agar alur supplier & gudang bisa langsung dicoba.
     */
    public function run(): void
    {
        $admin     = User::where('email', 'superadmin@bajuku.test')->first();
        $suppliers = Supplier::all();
        $variants  = ProductVariant::with('product')->inRandomOrder()->get();

        $plans = [
            ['sent',      'unpaid'],
            ['confirmed', 'unpaid'],
            ['shipped',   'unpaid'],
            ['received',  'unpaid'],
        ];

        foreach ($plans as $i => [$status, $pay]) {
            $items    = $variants->slice($i * 3, 3);
            $subtotal = $items->sum(fn ($v) => 20 * $v->product->cost_price);
            $tax      = (int) round($subtotal * config('shop.tax_rate') / 100);

            // Penanda status supaya baris di bawah tetap pendek
            $isReady = in_array($status, ['confirmed', 'shipped', 'received']);
            $isSent  = in_array($status, ['shipped', 'received']);
            $isDone  = $status === 'received';

            $po = PurchaseOrder::create([
                'po_number'            => PurchaseOrder::generateNumber(),
                'supplier_id'          => $suppliers[$i % $suppliers->count()]->id,
                'created_by'           => $admin->id,
                'status'               => $status,
                'payment_status'       => $pay,
                'expected_date'        => now()->addDays(7),
                'due_date'             => now()->addDays(14),
                'estimated_ready_date' => $isReady ? now()->addDays(3) : null,
                'subtotal'             => $subtotal,
                'tax'                  => $tax,
                'total'                => $subtotal + $tax,
                'courier'              => $isSent ? 'CV Maju Logistik' : null,
                'vehicle_plate'        => $isSent ? 'D 1234 ABC' : null,
                'shipped_at'           => $isSent ? now()->subDay() : null,
                'received_at'          => $isDone ? now() : null,
            ]);

            foreach ($items as $v) {
                $po->items()->create([
                    'product_variant_id' => $v->id,
                    'qty_ordered'        => 20,
                    'unit_price'         => $v->product->cost_price,
                    'qty_received'       => $isDone ? 20 : 0,
                ]);
            }
        }
    }
}