<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Membuat pesanan contoh 14 hari terakhir
     * agar dashboard Owner tidak kosong.
     */
    public function run(): void
    {
        // Beberapa konsumen contoh
        $customers = collect(['Rani', 'Dewi', 'Putri', 'Maya'])
            ->map(function ($nama) {
                return User::firstOrCreate(
                    ['email' => strtolower($nama) . '@bajuku.test'],
                    [
                        'name'     => $nama . ' Pelanggan',
                        'role'     => 'konsumen',
                        'password' => 'password',
                        'phone'    => '0813' . rand(1000000, 9999999),
                    ]
                );
            })
            ->push(User::where('email', 'konsumen@bajuku.test')->first());

        $variants = ProductVariant::with('product')->get();

        $statuses = [
            'completed', 'completed', 'completed', 'completed',
            'shipped', 'to_process', 'pending_payment', 'cancelled',
        ];

        $couriers = ['JNE', 'J&T', 'SiCepat'];

        $komentar = [
            'Bahannya adem dan jahitannya rapi!',
            'Sesuai foto, pengiriman cepat.',
            'Suka banget, pasti beli lagi.',
        ];

        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            $status   = $statuses[array_rand($statuses)];
            $date     = now()
                ->subDays(rand(0, 13))
                ->setTime(rand(8, 21), rand(0, 59));

            $picked   = $variants->random(rand(1, 3));
            $subtotal = 0;
            $lines    = [];

            foreach ($picked as $v) {
                $qty = rand(1, 2);
                $subtotal += $v->product->final_price * $qty;
                $lines[] = [$v, $qty];
            }

            $freeMin  = config('shop.free_shipping_min');
            $shipping = $subtotal >= $freeMin ? 0 : 12000;
            $fee      = config('shop.service_fee');
            $paid     = ! in_array($status, ['pending_payment', 'cancelled']);
            $sent     = in_array($status, ['shipped', 'completed']);
            $done     = $status === 'completed';

            $resi        = $sent ? 'RESI' . rand(100000000, 999999999) : null;
            $shippedAt   = $sent ? $date->copy()->addDay() : null;
            $completedAt = $done ? $date->copy()->addDays(3) : null;

            $order = Order::create([
                'order_number'     => Order::generateNumber(),
                'user_id'         => $customer->id,
                'recipient_name'  => $customer->name,
                'recipient_phone' => (string) $customer->phone,
                'shipping_address' => 'Jl. Contoh No. ' . rand(1, 99)
                    . ', Bandung 40111',
                'courier'         => $couriers[array_rand($couriers)],
                'courier_service' => 'Reguler',
                'shipping_cost'   => $shipping,
                'subtotal'        => $subtotal,
                'discount'        => 0,
                'service_fee'     => $fee,
                'total'           => $subtotal + $shipping + $fee,
                'payment_method'  => 'QRIS',
                'payment_status'  => $paid ? 'paid' : 'pending',
                'status'          => $status,
                'tracking_number' => $resi,
                'paid_at'         => $paid ? $date : null,
                'shipped_at'      => $shippedAt,
                'completed_at'    => $completedAt,
                'created_at'      => $date,
                'updated_at'      => $date,
            ]);

            foreach ($lines as [$v, $qty]) {
                $order->items()->create([
                    'product_id'         => $v->product_id,
                    'product_variant_id' => $v->id,
                    'product_name'       => $v->product->name,
                    'variant_label'      => $v->label,
                    'price'              => $v->product->final_price,
                    'cost_price'         => $v->product->cost_price,
                    'quantity'           => $qty,
                    'created_at'         => $date,
                    'updated_at'         => $date,
                ]);

                // Ulasan acak untuk pesanan selesai
                if ($done && rand(1, 3) === 1) {
                    Review::firstOrCreate(
                        [
                            'user_id'    => $customer->id,
                            'product_id' => $v->product_id,
                            'order_id'   => $order->id,
                        ],
                        [
                            'rating'  => rand(4, 5),
                            'comment' => $komentar[array_rand($komentar)],
                        ]
                    );
                }
            }
        }
    }
}