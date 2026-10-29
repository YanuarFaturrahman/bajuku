<?php

namespace App\Livewire\Account;

use App\Models\Review;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Ulasan Saya')]
class MyReviews extends Component
{
    public function render()
    {
        return view('livewire.account.my-reviews', [
            'reviews' => Review::with('product')->where('user_id', auth()->id())->latest()->get(),
        ])->layout('components.layouts.app');
    }
}
