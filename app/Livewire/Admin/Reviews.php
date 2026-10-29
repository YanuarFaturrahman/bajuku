<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\Toasts;
use App\Models\Review;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Moderasi Ulasan')]
class Reviews extends Component
{
    use WithPagination, Toasts;

    public function delete(int $id): void
    {
        Review::findOrFail($id)->delete();
        $this->toast('Ulasan dihapus');
    }

    public function render()
    {
        return view('livewire.admin.reviews', [
            'reviews' => Review::with(['user', 'product'])->latest()->paginate(10),
        ]);
    }
}
