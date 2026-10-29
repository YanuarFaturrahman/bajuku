<?php
 
namespace Database\Seeders;
 
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Semua akun demo memakai password: password
        $accounts = [
            ['Super Admin',     'superadmin@bajuku.test', 'superadmin'],
            ['Admin Katalog',   'admin@bajuku.test',      'admin'],
            ['Petugas Gudang',  'gudang@bajuku.test',     'gudang'],
            ['Owner Bajuku',    'owner@bajuku.test',      'owner'],
            ['Sinta Konsumen',  'konsumen@bajuku.test',   'konsumen'],
        ];
 
        foreach ($accounts as [$name, $email, $role]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name, 'role' => $role, 'password' => 'password',
                'phone' => '0812' . rand(10000000, 99999999), 'is_active' => true,
            ]);
        }
 
        // Dua supplier (user + data perusahaan)
        foreach ([
            ['PIC Tekstil Indah', 'supplier@bajuku.test',  'PT Tekstil Indah'],
            ['PIC Garmen Nusantara', 'supplier2@bajuku.test', 'CV Garmen Nusantara'],
        ] as [$name, $email, $company]) {
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $name, 'role' => 'supplier', 'password' => 'password', 'is_active' => 
true,
            ]);
            Supplier::updateOrCreate(['user_id' => $user->id], [
                'company_name' => $company, 'phone' => '021-5550' . rand(100, 999), 'address' => 
'Bandung, Jawa Barat',
            ]);
        }
 
        // Alamat contoh untuk konsumen demo
        User::where('email', 'konsumen@bajuku.test')->first()->addresses()->firstOrCreate(
            ['label' => 'Rumah'],
            ['recipient_name' => 'Sinta Konsumen', 'phone' => '081234567890',
             'address' => 'Jl. Melati No. 12', 'city' => 'Subang', 'postal_code' => '41211', 
'is_default' => true]
        );
    }
}