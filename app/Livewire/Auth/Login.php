<?php

namespace App\Livewire\Auth;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Masuk')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Batasi percobaan login (5x per email + IP) untuk mencegah brute force
        $key = 'login:' . Str::lower($this->email) . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.');
            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key);
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        RateLimiter::clear($key);
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $this->addError('email', 'Akun Anda dinonaktifkan. Hubungi Superadmin.');
            return;
        }

        session()->regenerate();
        $user->update(['last_login_at' => now()]);
        ActivityLog::record('login', 'Pengguna masuk ke sistem');

        return redirect()->intended(route($user->dashboardRoute()));
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.app');
    }
}
