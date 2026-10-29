<?php

namespace App\Livewire\Shop;

use App\Livewire\Concerns\HandlesWishlist;
use App\Livewire\Concerns\Toasts;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Katalog Produk')]
class Catalog extends Component
{
    use WithPagination, Toasts, HandlesWishlist;

    // #[Url] membuat filter tersimpan di alamat URL (bisa di-bookmark / dibagikan)
    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: 'terbaru')]
    public string $sort = 'terbaru';

    #[Url(except: '')]
    public $minPrice = '';

    #[Url(except: '')]
    public $maxPrice = '';

    #[Url]
    public array $sizes = [];

    #[Url]
    public array $colors = [];

    public string $layout = 'grid';

    /** Setiap filter berubah -> kembali ke halaman 1. */
    public function updated($property): void
    {
        if ($property !== 'layout') {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'category', 'minPrice', 'maxPrice', 'sizes', 'colors');
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::active()
            ->with(['category', 'variants', 'reviews'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->category, function ($q) {
                $q->whereHas('category', fn ($c) => $c
                    ->where('slug', $this->category)
                    ->orWhereHas('parent', fn ($p) => $p->where('slug', $this->category)));
            })
            ->when($this->minPrice !== '' && $this->minPrice !== null,
                fn ($q) => $q->whereRaw('COALESCE(discount_price, price) >= ?', [(int) $this->minPrice]))
            ->when($this->maxPrice !== '' && $this->maxPrice !== null,
                fn ($q) => $q->whereRaw('COALESCE(discount_price, price) <= ?', [(int) $this->maxPrice]))
            ->when($this->sizes, fn ($q) => $q->whereHas('variants', fn ($v) => $v->whereIn('size', $this->sizes)))
            ->when($this->colors, fn ($q) => $q->whereHas('variants', fn ($v) => $v->whereIn('color', $this->colors)));

        match ($this->sort) {
            'populer'  => $query->orderByDesc('sold_count'),
            'termurah' => $query->orderByRaw('COALESCE(discount_price, price) asc'),
            'termahal' => $query->orderByRaw('COALESCE(discount_price, price) desc'),
            default    => $query->latest(),
        };

        return view('livewire.shop.catalog', [
            'products'   => $query->paginate(12),
            'categories' => Category::whereNull('parent_id')->with('children')->orderBy('name')->get(),
            'wishlist'   => $this->wishlistIds(),
        ])->layout('components.layouts.app');
    }
}
