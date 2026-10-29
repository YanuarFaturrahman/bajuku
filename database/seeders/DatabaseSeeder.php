<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
 
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: user -> kategori -> produk -> lainnya -> transaksi
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            VoucherSeeder::class,
            SettingSeeder::class,
            OrderSeeder::class,
            ProcurementSeeder::class,
        ]);
    }
}