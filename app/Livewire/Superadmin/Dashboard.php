<?php

namespace App\Livewire\Superadmin;

use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Dashboard Sistem')]
class Dashboard extends Component
{
    public function render()
    {
        // Health check sederhana
        try { DB::select('select 1'); $db = true; } catch (\Throwable $e) { $db = false; }

        $checks = [
            'Koneksi database'          => $db,
            'Storage link (public)'     => is_link(public_path('storage')) || is_dir(public_path('storage')),
            'Payment Gateway terisi'    => (bool) Setting::get('pg_server_key'),
            'API Kurir terisi'          => (bool) Setting::get('logistics_api_key'),
            'Folder storage dapat ditulis' => is_writable(storage_path()),
        ];

        return view('livewire.superadmin.dashboard', [
            'checks'    => $checks,
            'byRole'    => User::select('role', DB::raw('COUNT(*) as total'))->groupBy('role')->pluck('total', 'role'),
            'suspended' => User::where('is_active', false)->count(),
            'logs'      => ActivityLog::with('user')->latest()->take(6)->get(),
        ]);
    }
}
