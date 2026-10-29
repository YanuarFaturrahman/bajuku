<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.panel')]
class ProductForm extends Component
{
    use WithFileUploads, Toasts;

    public ?Product $product = null;

    // Informasi dasar
    public string $name = '';
    public $category_id = '';
    public string $description = '';
    public string $material = '';
    public string $care = '';

    // Harga & pengiriman
    public $price = '';
    public $discount_price = '';
    public $cost_price = 0;
    public $weight = 300;
    public bool $is_active = true;

    // Media
    public $photo = null;
    public ?string $currentImage = null;

    // Varian
    public array $colors = [];
    public array $sizes = [];
    public array $rows = [];   // tiap baris: color, size, sku, stock, rack

    public function mount(?Product $product = null): void
    {
        if ($product && $product->exists) {
            $this->product        = $product;
            $this->name           = $product->name;
            $this->category_id    = $product->category_id;
            $this->description    = (string) $product->description;
            $this->material       = (string) $product->material;
            $this->care           = (string) $product->care;
            $this->price          = $product->price;
            $this->discount_price = $product->discount_price ?? '';
            $this->cost_price     = $product->cost_price;
            $this->weight         = $product->weight;
            $this->is_active      = $product->is_active;
            $this->currentImage   = $product->image;

            foreach ($product->variants as $v) {
                $this->rows[] = ['color' => $v->color, 'size' => $v->size, 'sku' => $v->sku, 'stock' => $v->stock, 'rack' => (string) $v->rack_location];
            }
            $this->colors = collect($this->rows)->pluck('color')->unique()->values()->all();
            $this->sizes  = collect($this->rows)->pluck('size')->unique()->values()->all();
        }
    }

    public function updatedColors(): void { $this->rebuildRows(); }
    public function updatedSizes(): void  { $this->rebuildRows(); }

    /** Bentuk ulang matriks varian (warna × ukuran) tanpa menghapus isian yang sudah ada. */
    protected function rebuildRows(): void
    {
        $existing = collect($this->rows)->keyBy(fn ($r) => $r['color'] . '|' . $r['size']);
        $rows = [];

        foreach ($this->colors as $color) {
            foreach (config('shop.sizes') as $size) {
                if (! in_array($size, $this->sizes)) continue;
                $rows[] = $existing[$color . '|' . $size] ?? ['color' => $color, 'size' => $size, 'sku' => '', 'stock' => 0, 'rack' => ''];
            }
        }
        $this->rows = $rows;
    }

    public function removePhoto(): void
    {
        $this->photo = null;
    }

    public function save()
    {
        $this->validate([
            'name'           => 'required|string|max:150',
            'category_id'    => 'required|exists:categories,id',
            'description'    => 'nullable|string',
            'price'          => 'required|integer|min:1000',
            'discount_price' => 'nullable|integer|min:0|lt:price',
            'cost_price'     => 'required|integer|min:0',
            'weight'         => 'required|integer|min:1',
            'photo'          => 'nullable|image|max:2048',
            'rows'           => 'required|array|min:1',
            'rows.*.stock'   => 'required|integer|min:0',
            'rows.*.sku'     => 'nullable|string|max:40',
        ], [
            'rows.required' => 'Pilih minimal satu warna dan satu ukuran untuk membuat varian.',
            'rows.min'      => 'Pilih minimal satu warna dan satu ukuran untuk membuat varian.',
            'discount_price.lt' => 'Harga diskon harus lebih kecil dari harga normal.',
        ]);

        // Cek SKU manual agar tidak bentrok dengan produk lain
        foreach ($this->rows as $i => $row) {
            if ($row['sku'] !== '' && ProductVariant::where('sku', $row['sku'])
                ->whereNot(fn ($q) => $q->where('product_id', $this->product?->id)->where('color', $row['color'])->where('size', $row['size']))
                ->exists()) {
                $this->addError("rows.$i.sku", 'SKU sudah dipakai.');
                return;
            }
        }

        $product = DB::transaction(function () {
            $data = [
                'category_id'    => $this->category_id,
                'name'           => $this->name,
                'description'    => $this->description ?: null,
                'material'       => $this->material ?: null,
                'care'           => $this->care ?: null,
                'price'          => $this->price,
                'discount_price' => $this->discount_price !== '' ? $this->discount_price : null,
                'cost_price'     => $this->cost_price,
                'weight'         => $this->weight,
                'is_active'      => $this->is_active,
            ];

            if ($this->photo) {
                if ($this->currentImage) Storage::disk('public')->delete($this->currentImage);
                $data['image'] = $this->photo->store('products', 'public');
            }

            if ($this->product) {
                $this->product->update($data);
                $product = $this->product;
            } else {
                $data['slug'] = $this->uniqueSlug($this->name);
                $product = Product::create($data);
            }

            $keep = [];
            foreach ($this->rows as $row) {
                $variant = ProductVariant::firstOrNew([
                    'product_id' => $product->id, 'color' => $row['color'], 'size' => $row['size'],
                ]);
                $oldStock = $variant->exists ? $variant->stock : 0;

                $variant->sku = $row['sku'] !== ''
                    ? $row['sku']
                    : ($variant->sku ?: 'BJK-' . $product->id . '-' . strtoupper(substr($row['color'], 0, 3)) . '-' . $row['size']);
                $variant->stock = (int) $row['stock'];
                $variant->rack_location = $row['rack'] ?: null;
                $variant->save();

                if ($variant->stock !== $oldStock) {
                    $isNew = $variant->wasRecentlyCreated;
                    StockMovement::record(
                        $variant,
                        $isNew ? 'in' : 'adjustment',
                        $variant->stock - $oldStock,
                        null,
                        $isNew ? 'Stok awal produk' : 'Penyesuaian dari form produk'
                    );
                }
                $keep[] = $variant->id;
            }

            // Varian yang tidak lagi dicentang dihapus
            ProductVariant::where('product_id', $product->id)->whereNotIn('id', $keep)->delete();

            return $product;
        });

        ActivityLog::record($this->product ? 'product.update' : 'product.create', ($this->product ? 'Mengubah' : 'Menambah') . ' produk ' . $product->name);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil disimpan.');
    }

    protected function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = Str::slug($name) . '-' . $i++;
        }
        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.product-form', [
            'categories' => Category::with('parent')->orderBy('parent_id')->orderBy('name')->get(),
        ])->title($this->product ? 'Edit Produk' : 'Tambah Produk');
    }
}
