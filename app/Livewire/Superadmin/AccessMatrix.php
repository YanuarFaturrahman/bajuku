<?php

namespace App\Livewire\Superadmin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Hak Akses (RBAC)')]
class AccessMatrix extends Component
{
    public function render()
    {
        return view('livewire.superadmin.access-matrix', [
            'roles' => config('rbac.roles'),
            'menus' => config('rbac.menus'),
        ]);
    }
}
