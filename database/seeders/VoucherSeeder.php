<?php
 
namespace Database\Seeders;
 
use App\Models\Voucher;
use Illuminate\Database\Seeder;
 
class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::updateOrCreate(['code' => 'BAJUKU10'], [
            'description' => 'Diskon 10% semua produk', 'type' => 'percent', 'value' => 10,
            'min_purchase' => 100000, 'max_discount' => 50000, 'quota' => 200,
            'expires_at' => now()->addMonths(3), 'is_active' => true,
        ]);
 
        Voucher::updateOrCreate(['code' => 'WELCOME25'], [
            'description' => 'Potongan Rp25.000 untuk pembelian pertama', 'type' => 'fixed', 
'value' => 25000,
            'min_purchase' => 150000, 'quota' => 500,
            'expires_at' => now()->addMonths(6), 'is_active' => true,
        ]);
    }
}