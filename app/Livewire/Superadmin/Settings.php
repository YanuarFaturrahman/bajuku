<?php

namespace App\Livewire\Superadmin;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Pengaturan Sistem')]
class Settings extends Component
{
    use Toasts;

    public string $store_name = '';
    public string $pg_provider = 'midtrans';
    public string $pg_server_key = '';
    public string $pg_client_key = '';
    public string $pg_mode = 'sandbox';
    public string $logistics_provider = 'rajaongkir';
    public string $logistics_api_key = '';
    public string $wa_template = '';
    public string $email_template = '';

    protected array $keys = [
        'store_name', 'pg_provider', 'pg_server_key', 'pg_client_key', 'pg_mode',
        'logistics_provider', 'logistics_api_key', 'wa_template', 'email_template',
    ];

    public function mount(): void
    {
        foreach ($this->keys as $key) {
            $this->$key = (string) Setting::get($key, $this->$key);
        }
    }

    public function save(): void
    {
        $this->validate([
            'store_name'    => 'required|string|max:80',
            'pg_mode'       => 'required|in:sandbox,production',
            'pg_provider'   => 'required|in:midtrans,xendit',
            'logistics_provider' => 'required|in:rajaongkir,biteship',
        ]);

        foreach ($this->keys as $key) {
            Setting::set($key, $this->$key);
        }

        ActivityLog::record('settings.update', 'Pengaturan sistem diperbarui');
        $this->toast('Pengaturan disimpan');
    }

    public function render()
    {
        return view('livewire.superadmin.settings');
    }
}
