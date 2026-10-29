<?php
 
return [
 
    'roles' => [
        'konsumen'   => 'Konsumen',
        'admin'      => 'Admin',
        'gudang'     => 'Gudang',
        'supplier'   => 'Supplier',
        'owner'      => 'Owner',
        'superadmin' => 'Superadmin',
    ],
 
    // Rangkuman Modul Akses Menu (Recap Matrix): nama menu => role yang boleh
    'menus' => [
        'Katalog Public / Shop'        => ['konsumen', 'admin'],
        'Checkout & Pay'               => ['konsumen'],
        'Akun, Pesanan & Wishlist'     => ['konsumen'],
        'Manajemen Produk & Kategori'  => ['admin'],
        'Pesanan, Voucher & Ulasan'    => ['admin'],
        'Fulfillment & Scan Resi'      => ['gudang'],
        'Stok Opname & Inbound QC'     => ['gudang'],
        'Buka & Konfirmasi PO Vendor'  => ['supplier'],
        'Buat PO Pengadaan'            => ['superadmin'],
        'Dashboard Keuangan & BI'      => ['owner'],
        'System Settings & RBAC'       => ['superadmin'],
        'Audit Log & Kelola User'      => ['superadmin'],
    ],
];