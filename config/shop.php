<?php
 
return [
 
    // Kurir & layanan. 'cost' = tarif per 1 kg (dibulatkan ke atas).
    'couriers' => [
        'JNE' => [
            'Reguler' => ['cost' => 12000, 'eta' => '2-3 hari'],
            'Express' => ['cost' => 22000, 'eta' => '1 hari'],
        ],
        'J&T' => [
            'Reguler' => ['cost' => 11000, 'eta' => '2-4 hari'],
            'Express' => ['cost' => 20000, 'eta' => '1-2 hari'],
        ],
        'SiCepat' => [
            'Reguler' => ['cost' => 10000, 'eta' => '2-3 hari'],
            'Sameday' => ['cost' => 30000, 'eta' => 'Hari yang sama'],
        ],
    ],
 
    'payment_methods' => [
        'Virtual Account BCA',
        'Virtual Account Mandiri',
        'QRIS',
        'E-Wallet (GoPay)',
        'E-Wallet (OVO)',
        'Kartu Kredit',
    ],
 
    'sizes'  => ['S', 'M', 'L', 'XL'],
 
    // nama warna => kode warna untuk swatch
    'colors' => [
        'Pink'  => '#f9a8c9',
        'Putih' => '#ffffff',
        'Hitam' => '#1f2937',
        'Navy'  => '#1e3a8a',
        'Krem'  => '#f5e6d3',
        'Sage'  => '#a3b899',
        'Cokelat' => '#7c4a2e',
    ],
 
    'service_fee'       => 2000,      // biaya layanan per pesanan
    'free_shipping_min' => 150000,    // gratis ongkir jika subtotal >= nilai ini
    'low_stock'         => 5,         // batas stok menipis
    'tax_rate'          => 11,        // PPN (%) untuk Purchase Order
];