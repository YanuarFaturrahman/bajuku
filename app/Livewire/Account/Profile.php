<?php

namespace App\Livewire\Account;

use App\Livewire\Concerns\Toasts;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profil Saya')]
class Profile extends Component
{
    use Toasts;

    public string $name = '';
    public string $phone = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->name  = auth()->user()->name;
        $this->phone = (string) auth()->user()->phone;
    }

    public function saveProfile(): void
    {
        $data = $this->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        auth()->user()->update($data);
        $this->toast('Profil diperbarui');
    }

    public function savePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');
            return;
        }

        auth()->user()->update(['password' => $this->password]);
        $this->reset('current_password', 'password', 'password_confirmation');
        $this->toast('Password berhasil diganti');
    }

    public function render()
    {
        return view('livewire.account.profile', [
            'orderCount' => auth()->user()->orders()->count(),
            'wishCount'  => auth()->user()->wishlists()->count(),
        ])->layout('components.layouts.app');
    }
}
