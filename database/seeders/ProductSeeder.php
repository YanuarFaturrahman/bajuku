<?php
 
namespace Database\Seeders;
 
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
 
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // [nama, slug kategori, harga, diskon, modal, warna[], ukuran[], bahan]
        $items = [
            ['Kaos Oversize Basic',       'kaos',      129000, 99000,  55000, ['Putih', 'Pink', 
'Hitam'], ['S', 'M', 'L', 'XL'], 'Katun combed 24s'],
            ['Kaos Crop Rib Soft',        'kaos',       89000, null,   38000, ['Pink', 'Krem'], 
['S', 'M', 'L'],       'Rib cotton stretch'],
            ['Kemeja Linen Elegan',       'kemeja',    249000, 199000, 110000, ['Krem', 'Sage', 
'Putih'], ['S', 'M', 'L', 'XL'], 'Linen blend'],
            ['Kemeja Oxford Klasik',      'kemeja',    229000, null,   100000, ['Putih', 'Navy'], 
['M', 'L', 'XL'],      'Oxford cotton'],
            ['Parka Ringan Waterproof',   'parka',     399000, 349000, 180000, ['Hitam', 'Sage'], 
['M', 'L', 'XL'],      'Parasut waterproof'],
  ['Jaket Denim Washed',        'denim',     459000, null,   210000, ['Navy'], 
['S', 'M', 'L', 'XL'], 'Denim 11oz'],
            ['Celana Chino Slim',         'chino',     299000, 249000, 130000, ['Krem', 'Hitam', 
'Navy'], ['S', 'M', 'L', 'XL'], 'Twill stretch'],
            ['Jeans Straight Classic',    'jeans',     349000, null,   160000, ['Navy', 'Hitam'], 
['S', 'M', 'L', 'XL'], 'Denim stretch'],
            ['Dress Midi Floral Pink',    'dress',     329000, 279000, 140000, ['Pink', 'Putih'], 
['S', 'M', 'L'],       'Rayon viscose'],
            ['Dress Satin Minimalis',     'dress',     389000, null,   170000, ['Krem', 'Hitam'], 
['S', 'M', 'L'],       'Satin silk'],
            ['Tote Bag Canvas Pink',      'aksesoris',  99000, 79000,  35000, ['Pink', 'Krem'], 
['M'],                 'Kanvas tebal'],
            ['Topi Baseball Polos',       'aksesoris',  69000, null,   25000, ['Hitam', 'Putih', 
'Pink'], ['M'],                 'Katun twill'],
        ];
 
        foreach ($items as $n => [$name, $catSlug, $price, $disc, $cost, $colors, $sizes, 
$material]) {
            $category = Category::where('slug', $catSlug)->firstOrFail();
 
            $product = Product::updateOrCreate(['slug' => Str::slug($name)], [
                'category_id'    => $category->id,
                'name'           => $name,
                'description'    => "{$name} dengan potongan modern, nyaman dipakai seharian, dan 
mudah dipadukan dengan outfit apa pun.",
                'material'       => $material,
                'care'           => 'Cuci dengan air dingin, hindari pemutih, setrika suhu rendah, 
jangan dikeringkan dengan mesin.',
                'price'          => $price,
                'discount_price' => $disc,
                'cost_price'     => $cost,
                'weight'         => 300,
                'is_active'      => true,
                'sold_count'     => rand(5, 90),
            ]);
 
            $rackLetter = chr(65 + ($category->parent_id ?? $category->id) % 5);   // A..E
 
            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    ProductVariant::updateOrCreate(
                        ['product_id' => $product->id, 'color' => $color, 'size' => $size],
                        [
                            'sku'           => 'BJK-' . str_pad((string) ($n + 1), 2, '0', 
STR_PAD_LEFT) . '-' . strtoupper(substr($color, 0, 3)) . '-' . $size,
                            // beberapa varian sengaja stok tipis agar fitur "stok menipis" bisa dicoba
                            'stock'         => rand(1, 10) === 1 ? rand(1, 4) : rand(12, 45),
                            'rack_location' => $rackLetter . '-' . rand(1, 20),
                        ]
                    );
                }
            }
        }
    }
}