<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\Toasts;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Kategori')]
class Categories extends Component
{
    use Toasts;

    public ?int $editingId = null;
    public string $name = '';
    public $parent_id = '';

    public function edit(int $id): void
    {
        $c = Category::findOrFail($id);
        $this->editingId = $c->id;
        $this->name = $c->name;
        $this->parent_id = $c->parent_id ?? '';
    }

    public function cancel(): void
    {
        $this->reset('editingId', 'name', 'parent_id');
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate([
            'name'      => 'required|string|max:60',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        // Kategori tidak boleh menjadi induk bagi dirinya sendiri
        if ($this->editingId && (int) $this->parent_id === $this->editingId) {
            $this->addError('parent_id', 'Induk tidak valid.');
            return;
        }

        $slug = Str::slug($this->name);
        $exists = Category::where('slug', $slug)->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))->exists();
        if ($exists) $slug .= '-' . Str::lower(Str::random(3));

        Category::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $this->name, 'slug' => $slug, 'parent_id' => $this->parent_id ?: null]
        );

        $this->cancel();
        $this->toast('Kategori disimpan');
    }

    public function delete(int $id): void
    {
        $c = Category::withCount(['products', 'children'])->findOrFail($id);

        if ($c->products_count || $c->children_count) {
            $this->toast('Kategori masih memiliki produk / sub-kategori.', 'error');
            return;
        }
        $c->delete();
        $this->toast('Kategori dihapus');
    }

    public function render()
    {
        return view('livewire.admin.categories', [
            'categories' => Category::with('parent')->withCount('products')->orderBy('name')->get(),
            'parents'    => Category::whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }
}
