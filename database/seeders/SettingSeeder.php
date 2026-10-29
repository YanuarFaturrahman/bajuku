<?php
 
namespace Database\Seeders;
 
use App\Models\Setting;
use Illuminate\Database\Seeder;
 
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name'         => 'Bajuku',
            'pg_provider'        => 'midtrans',
            'pg_mode'            => 'sandbox',
            'logistics_provider' => 'rajaongkir',
            'wa_template'        => 'Halo {nama}, pesanan {order} sedang kami proses. Terima kasih 
sudah berbelanja di Bajuku!',
            'email_template'     => 'Terima kasih telah berbelanja di Bajuku. Pesanan {order} Anda 
sedang diproses.',
        ];
 
        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}