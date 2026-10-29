<?php
 
namespace App\Support;
 
class Menu
{
    /**
     * Daftar menu sidebar untuk tiap role: [label, nama route, pola route aktif]
     */
    public static function for(string $role): array
    {
        return match ($role) {
            'admin' => [
                ['Dashboard', 'admin.dashboard', 'admin.dashboard'],
                ['Produk', 'admin.products', 'admin.products*'],
                ['Kategori', 'admin.categories', 'admin.categories'],
                ['Pesanan', 'admin.orders', 'admin.orders'],
                ['Voucher & Promo', 'admin.vouchers', 'admin.vouchers'],
                ['Ulasan', 'admin.reviews', 'admin.reviews'],
                ['Lihat Toko', 'home', 'none'],
            ],
            'gudang' => [
                ['Dashboard', 'warehouse.dashboard', 'warehouse.dashboard'],
                ['Pengiriman (Outbound)', 'warehouse.outbound', 'warehouse.outbound'],
                ['Manajemen Stok', 'warehouse.inventory', 'warehouse.inventory'],
                ['Penerimaan (Inbound)', 'warehouse.inbound', 'warehouse.inbound'],
            ],
              'supplier' => [
                ['Dashboard', 'supplier.dashboard', 'supplier.dashboard'],
                ['Purchase Order', 'supplier.orders', 'supplier.orders'],
            ],
            'owner' => [
                ['Dashboard Eksekutif', 'owner.dashboard', 'owner.dashboard'],
                ['Laporan & Analisis', 'owner.reports', 'owner.reports'],
            ],
            'superadmin' => [
                ['Dashboard Sistem', 'superadmin.dashboard', 'superadmin.dashboard'],
                ['Pengguna', 'superadmin.users', 'superadmin.users'],
                ['Hak Akses (RBAC)', 'superadmin.access', 'superadmin.access'],
                ['Purchase Order', 'superadmin.purchase-orders', 'superadmin.purchase-orders'],
                ['Pengaturan', 'superadmin.settings', 'superadmin.settings'],
                ['Audit Log', 'superadmin.logs', 'superadmin.logs'],
            ],
            default => [],
        };
    }
}