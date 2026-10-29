<?php

namespace App\Livewire\Concerns;

trait Toasts
{
    /** Tampilkan notifikasi kecil di pojok kanan atas (ditangkap oleh <x-toast>). */
    protected function toast(string $message, string $type = 'success'): void
    {
        $this->dispatch('toast', message: $message, type: $type);
    }
}
