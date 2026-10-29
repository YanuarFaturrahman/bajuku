<?php

namespace App\Livewire\Shop;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class Checkout extends Component
{
    use Toasts;

    public array $ids = [];
    public ?int $addressId = null;
    public string $courier = '';
    public string $service = '';
    public string $voucherCode = '';
    public string $appliedVoucher = '';
    public string $payment = '';

    // Form tambah alamat baru
    public bool $showAddressForm = false;
    public string $label = 'Rumah';
    public string $recipient_name = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';

    public function mount()
    {
        $this->ids = array_map('intval', session('checkout_ids', []));

        if (empty($this->ids) || $this->items->isEmpty()) {
            return $this->redirectRoute('cart');
        }

        $default = Address::where('user_id', auth()->id())->orderByDesc('is_default')->first();
        $this->addressId = $default?->id;
        $this->showAddressForm = $default === null;
        $this->recipient_name = auth()->user()->name;
        $this->phone = (string) auth()->user()->phone;

        $this->courier = array_key_first(config('shop.couriers'));
        $this->service = array_key_first(config('shop.couriers')[$this->courier]);
        $this->payment = config('shop.payment_methods')[0];
    }

    /* ---------- Data turunan (computed) ---------- */

    #[Computed]
    public function items()
    {
        return CartItem::with('variant.product')
            ->where('user_id', auth()->id())
            ->whereIn('id', $this->ids)
            ->whereHas('variant')
            ->get();
    }

    #[Computed]
    public function addresses()
    {
        return Address::where('user_id', auth()->id())->orderByDesc('is_default')->get();
    }

    #[Computed]
    public function subtotal(): int
    {
        return (int) $this->items->sum(fn ($i) => $i->line_total);
    }

    #[Computed]
    public function shippingCost(): int
    {
        $tariff = config("shop.couriers.{$this->courier}.{$this->service}.cost", 0);
        $grams  = $this->items->sum(fn ($i) => $i->variant->product->weight * $i->quantity);
        $kg     = max(1, (int) ceil($grams / 1000));

        // Gratis ongkir jika belanja mencapai batas minimum
        return $this->subtotal >= config('shop.free_shipping_min') ? 0 : $tariff * $kg;
    }

    #[Computed]
    public function voucher(): ?Voucher
    {
        return $this->appliedVoucher ? Voucher::where('code', $this->appliedVoucher)->first() : null;
    }

    #[Computed]
    public function discount(): int
    {
        return $this->voucher ? $this->voucher->discountFor($this->subtotal) : 0;
    }

    #[Computed]
    public function total(): int
    {
        return $this->subtotal + $this->shippingCost - $this->discount + config('shop.service_fee');
    }

    /* ---------- Interaksi ---------- */

    // Saat kurir diganti, layanan otomatis ke pilihan pertama kurir tsb.
    public function updatedCourier(): void
    {
        $this->service = array_key_first(config("shop.couriers.{$this->courier}"));
    }

    public function applyVoucher(): void
    {
        $voucher = Voucher::where('code', strtoupper(trim($this->voucherCode)))->first();

        if (! $voucher) {
            $this->toast('Kode voucher tidak ditemukan.', 'error');
            return;
        }

        [$ok, $message] = $voucher->check($this->subtotal);
        if (! $ok) {
            $this->toast($message, 'error');
            return;
        }

        $this->appliedVoucher = $voucher->code;
        $this->toast($message);
    }

    public function removeVoucher(): void
    {
        $this->appliedVoucher = '';
        $this->voucherCode = '';
    }

    public function saveAddress(): void
    {
        $data = $this->validate([
            'label'          => 'required|string|max:30',
            'recipient_name' => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:80',
            'postal_code'    => 'required|string|max:10',
        ]);

        $first = ! Address::where('user_id', auth()->id())->exists();
        $address = Address::create($data + ['user_id' => auth()->id(), 'is_default' => $first]);

        $this->addressId = $address->id;
        $this->showAddressForm = false;
        $this->reset('address', 'city', 'postal_code');
        unset($this->addresses);
        $this->toast('Alamat tersimpan');
    }

    public function placeOrder()
    {
        if (! $this->addressId) {
            $this->toast('Pilih atau tambahkan alamat pengiriman.', 'error');
            return;
        }

        $address = Address::where('user_id', auth()->id())->findOrFail($this->addressId);

        try {
            $order = DB::transaction(function () use ($address) {
                $items = CartItem::with('variant.product')
                    ->where('user_id', auth()->id())
                    ->whereIn('id', $this->ids)
                    ->get();

                if ($items->isEmpty()) {
                    throw new \RuntimeException('Keranjang kosong.');
                }

                // Kunci baris varian agar dua pembeli tidak membeli stok terakhir bersamaan
                $variants = ProductVariant::whereIn('id', $items->pluck('product_variant_id'))
                    ->lockForUpdate()->get()->keyBy('id');

                foreach ($items as $item) {
                    if ($variants[$item->product_variant_id]->stock < $item->quantity) {
                        throw new \RuntimeException('Stok ' . $item->variant->product->name . ' tidak mencukupi.');
                    }
                }

                $order = Order::create([
                    'order_number'     => Order::generateNumber(),
                    'user_id'          => auth()->id(),
                    'recipient_name'   => $address->recipient_name,
                    'recipient_phone'  => $address->phone,
                    'shipping_address' => $address->full_address,
                    'courier'          => $this->courier,
                    'courier_service'  => $this->service,
                    'shipping_cost'    => $this->shippingCost,
                    'subtotal'         => $this->subtotal,
                    'discount'         => $this->discount,
                    'voucher_code'     => $this->voucher?->code,
                    'service_fee'      => config('shop.service_fee'),
                    'total'            => $this->total,
                    'payment_method'   => $this->payment,
                    'payment_status'   => 'pending',
                    'status'           => 'pending_payment',
                ]);

                foreach ($items as $item) {
                    $variant = $variants[$item->product_variant_id];
                    $product = $item->variant->product;

                    $order->items()->create([
                        'product_id'         => $product->id,
                        'product_variant_id' => $variant->id,
                        'product_name'       => $product->name,
                        'variant_label'      => $variant->label,
                        'price'              => $product->final_price,
                        'cost_price'         => $product->cost_price,
                        'quantity'           => $item->quantity,
                    ]);

                    // Stok langsung dikurangi (di-reserve) saat pesanan dibuat
                    $variant->decrement('stock', $item->quantity);
                    StockMovement::record($variant, 'out', -$item->quantity, $order->order_number, 'Penjualan online');
                }

                if ($this->voucher) {
                    $this->voucher->increment('used');
                }

                CartItem::whereIn('id', $this->ids)->where('user_id', auth()->id())->delete();

                return $order;
            });
        } catch (\RuntimeException $e) {
            $this->toast($e->getMessage(), 'error');
            return;
        }

        session()->forget('checkout_ids');
        ActivityLog::record('order.create', 'Membuat pesanan ' . $order->order_number);
        $this->dispatch('cart-updated');

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Pesanan dibuat. Silakan selesaikan pembayaran.');
    }

    public function render()
    {
        return view('livewire.shop.checkout')->layout('components.layouts.app');
    }
}
