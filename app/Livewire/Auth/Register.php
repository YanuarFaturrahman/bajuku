<?php

namespace App\Livewire\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Daftar')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $data = $this->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        // Registrasi publik SELALU menghasilkan role konsumen
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => $data['password'],
            'role'     => 'konsumen',
        ]);

        Auth::login($user);
        session()->regenerate();
        ActivityLog::record('register', 'Konsumen baru mendaftar: ' . $user->email);

        return redirect()->route('home')->with('success', 'Selamat datang di Bajuku, ' . $user->name . '!');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.app');
    }
}
