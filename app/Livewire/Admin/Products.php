<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Data Produk')]
class Products extends Component
{
    use WithPagination, Toasts;

    public string $search = '';
    public string $categoryId = '';

    public function updatedSearch(): void     { $this->resetPage(); }
    public function updatedCategoryId(): void { $this->resetPage(); }

    public function toggleActive(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);
        $this->toast($product->is_active ? 'Produk diaktifkan' : 'Produk dinonaktifkan');
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $name = $product->name;
        $product->delete();

        ActivityLog::record('product.delete', 'Menghapus produk ' . $name);
        $this->toast('Produk dihapus');
    }

    public function render()
    {
        $products = Product::with(['category', 'variants'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->latest()->paginate(10);

        return view('livewire.admin.products', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
