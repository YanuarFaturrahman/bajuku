<?php

use App\Http\Controllers\DocumentController;
use App\Livewire\Account;
use App\Livewire\Admin;
use App\Livewire\Auth;
use App\Livewire\Owner;
use App\Livewire\Shop;
use App\Livewire\Superadmin;
use App\Livewire\Supplier;
use App\Livewire\Warehouse;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik (toko)
|--------------------------------------------------------------------------
*/
Route::get('/', Shop\Home::class)->name('home');
Route::get('/katalog', Shop\Catalog::class)->name('catalog');
Route::get('/produk/{slug}', Shop\ProductDetail::class)->name('product.show');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', Auth\Login::class)->name('login');
    Route::get('/daftar', Auth\Register::class)->name('register');
});

Route::post('/keluar', function () {
    AuthFacade::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| KONSUMEN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:konsumen'])->group(function () {
    Route::get('/keranjang', Shop\Cart::class)->name('cart');
    Route::get('/checkout', Shop\Checkout::class)->name('checkout');

    Route::prefix('akun')->name('account.')->group(function () {
        Route::get('/', Account\Profile::class)->name('profile');
        Route::get('/pesanan', Account\Orders::class)->name('orders');
        Route::get('/pesanan/{order}', Account\OrderDetail::class)
            ->name('orders.show');
        Route::get('/alamat', Account\Addresses::class)->name('addresses');
        Route::get('/wishlist', Account\Wishlist::class)->name('wishlist');
        Route::get('/ulasan', Account\MyReviews::class)->name('reviews');
        Route::get('/voucher', Account\Vouchers::class)->name('vouchers');
    });
});

// Invoice & dokumen cetak (hak akses diperiksa di controller)
Route::middleware('auth')->group(function () {
    Route::get('/invoice/{order}', [DocumentController::class, 'invoice'])
        ->name('invoice.show');
    Route::get('/gudang/label/{order}', [DocumentController::class, 'label'])
        ->name('warehouse.label');
    Route::get('/po/{purchaseOrder}/surat-jalan', [DocumentController::class, 'deliveryNote'])
        ->name('supplier.delivery-note');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Admin\Dashboard::class)->name('dashboard');
        Route::get('/produk', Admin\Products::class)->name('products');
        Route::get('/produk/tambah', Admin\ProductForm::class)
            ->name('products.create');
        Route::get('/produk/{product}/edit', Admin\ProductForm::class)
            ->name('products.edit');
        Route::get('/kategori', Admin\Categories::class)->name('categories');
        Route::get('/pesanan', Admin\Orders::class)->name('orders');
        Route::get('/voucher', Admin\Vouchers::class)->name('vouchers');
        Route::get('/ulasan', Admin\Reviews::class)->name('reviews');
    });

/*
|--------------------------------------------------------------------------
| GUDANG
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:gudang'])
    ->prefix('gudang')
    ->name('warehouse.')
    ->group(function () {
        Route::get('/', Warehouse\Dashboard::class)->name('dashboard');
        Route::get('/pengiriman', Warehouse\Outbound::class)->name('outbound');
        Route::get('/stok', Warehouse\Inventory::class)->name('inventory');
        Route::get('/penerimaan', Warehouse\Inbound::class)->name('inbound');
    });

/*
|--------------------------------------------------------------------------
| SUPPLIER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:supplier'])
    ->prefix('supplier')
    ->name('supplier.')
    ->group(function () {
        Route::get('/', Supplier\Dashboard::class)->name('dashboard');
        Route::get('/purchase-order', Supplier\PurchaseOrders::class)
            ->name('orders');
    });

/*
|--------------------------------------------------------------------------
| OWNER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/', Owner\Dashboard::class)->name('dashboard');
        Route::get('/laporan', Owner\Reports::class)->name('reports');
    });

/*
|--------------------------------------------------------------------------
| SUPERADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/', Superadmin\Dashboard::class)->name('dashboard');
        Route::get('/pengguna', Superadmin\Users::class)->name('users');
        Route::get('/hak-akses', Superadmin\AccessMatrix::class)->name('access');
        Route::get('/purchase-order', Superadmin\PurchaseOrders::class)
            ->name('purchase-orders');
        Route::get('/pengaturan', Superadmin\Settings::class)->name('settings');
        Route::get('/audit-log', Superadmin\AuditLogs::class)->name('logs');
    });