<?php

namespace App\Livewire\Superadmin;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Audit Log')]
class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        $logs = ActivityLog::with('user')
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                ->where('action', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"))))
            ->latest()->paginate(15);

        return view('livewire.superadmin.audit-logs', compact('logs'));
    }
}
