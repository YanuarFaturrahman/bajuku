<?php

namespace App\Livewire\Superadmin;

use App\Livewire\Concerns\Toasts;
use App\Models\ActivityLog;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.panel')]
#[Title('Manajemen Pengguna')]
class Users extends Component
{
    use WithPagination, Toasts;

    public string $search = '';
    public string $roleFilter = '';

    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role = 'admin';
    public string $password = '';
    public string $company_name = '';   // khusus role supplier

    public function updatedSearch(): void     { $this->resetPage(); }
    public function updatedRoleFilter(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $u = User::with('supplier')->findOrFail($id);
        $this->editingId = $u->id;
        $this->name = $u->name;
        $this->email = $u->email;
        $this->phone = (string) $u->phone;
        $this->role = $u->role;
        $this->company_name = $u->supplier?->company_name ?? '';
        $this->password = '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'         => 'required|string|max:100',
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingId)],
            'phone'        => 'nullable|string|max:20',
            'role'         => ['required', Rule::in(array_keys(config('rbac.roles')))],
            'password'     => ($this->editingId ? 'nullable' : 'required') . '|min:8',
            'company_name' => $this->role === 'supplier' ? 'required|string|max:120' : 'nullable',
        ]);

        // Superadmin tidak boleh menurunkan role dirinya sendiri (mencegah terkunci)
        if ($this->editingId === auth()->id() && $this->role !== 'superadmin') {
            $this->addError('role', 'Anda tidak dapat mengubah role akun sendiri.');
            return;
        }

        $data = ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone ?: null, 'role' => $this->role];
        if ($this->password !== '') $data['password'] = $this->password;

        $user = $this->editingId
            ? tap(User::findOrFail($this->editingId))->update($data)
            : User::create($data);

        // Role supplier wajib punya data perusahaan
        if ($this->role === 'supplier') {
            Supplier::updateOrCreate(['user_id' => $user->id], ['company_name' => $this->company_name, 'phone' => $this->phone ?: null]);
        }

        ActivityLog::record($this->editingId ? 'user.update' : 'user.create', "Pengguna {$user->email} ({$user->role})");
        $this->resetForm();
        $this->toast('Pengguna disimpan');
    }

    public function toggleActive(int $id): void
    {
        if ($id === auth()->id()) {
            $this->toast('Tidak bisa menonaktifkan akun sendiri.', 'error');
            return;
        }

        $u = User::findOrFail($id);
        $u->update(['is_active' => ! $u->is_active]);
        ActivityLog::record('user.suspend', ($u->is_active ? 'Mengaktifkan ' : 'Suspend ') . $u->email);
        $this->toast($u->is_active ? 'Akun diaktifkan' : 'Akun di-suspend');
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            $this->toast('Tidak bisa menghapus akun sendiri.', 'error');
            return;
        }

        $u = User::findOrFail($id);
        if ($u->orders()->exists()) {
            $this->toast('Pengguna punya riwayat pesanan. Gunakan Suspend saja.', 'error');
            return;
        }
        ActivityLog::record('user.delete', 'Menghapus ' . $u->email);
        $u->delete();
        $this->toast('Pengguna dihapus');
    }

    protected function resetForm(): void
    {
        $this->reset('showForm', 'editingId', 'name', 'email', 'phone', 'role', 'password', 'company_name');
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->roleFilter, fn ($q) => $q->where('role', $this->roleFilter))
            ->latest()->paginate(10);

        return view('livewire.superadmin.users', compact('users'));
    }
}
