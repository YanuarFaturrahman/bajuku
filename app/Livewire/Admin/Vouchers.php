<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\Toasts;
use App\Models\Voucher;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.panel')]
#[Title('Voucher & Promo')]
class Vouchers extends Component
{
    use Toasts;

    public ?int $editingId = null;
    public bool $showForm = false;

    public string $code = '';
    public string $description = '';
    public string $type = 'percent';
    public $value = '';
    public $min_purchase = 0;
    public $max_discount = '';
    public $quota = 100;
    public $expires_at = '';
    public bool $is_active = true;

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $v = Voucher::findOrFail($id);
        $this->editingId    = $v->id;
        $this->code         = $v->code;
        $this->description  = (string) $v->description;
        $this->type         = $v->type;
        $this->value        = $v->value;
        $this->min_purchase = $v->min_purchase;
        $this->max_discount = $v->max_discount ?? '';
        $this->quota        = $v->quota;
        $this->expires_at   = $v->expires_at?->format('Y-m-d') ?? '';
        $this->is_active    = $v->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'code'         => 'required|alpha_dash|max:30|unique:vouchers,code,' . ($this->editingId ?? 'NULL'),
            'type'         => 'required|in:percent,fixed',
            'value'        => 'required|integer|min:1' . ($this->type === 'percent' ? '|max:100' : ''),
            'min_purchase' => 'required|integer|min:0',
            'max_discount' => 'nullable|integer|min:1',
            'quota'        => 'required|integer|min:1',
            'expires_at'   => 'nullable|date',
        ]);

        Voucher::updateOrCreate(['id' => $this->editingId], [
            'code'         => strtoupper($this->code),
            'description'  => $this->description ?: null,
            'type'         => $this->type,
            'value'        => $this->value,
            'min_purchase' => $this->min_purchase,
            'max_discount' => $this->max_discount !== '' ? $this->max_discount : null,
            'quota'        => $this->quota,
            'expires_at'   => $this->expires_at ?: null,
            'is_active'    => $this->is_active,
        ]);

        $this->resetForm();
        $this->toast('Voucher disimpan');
    }

    public function delete(int $id): void
    {
        Voucher::findOrFail($id)->delete();
        $this->toast('Voucher dihapus');
    }

    protected function resetForm(): void
    {
        $this->reset('editingId', 'showForm', 'code', 'description', 'type', 'value', 'min_purchase', 'max_discount', 'quota', 'expires_at', 'is_active');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.vouchers', ['vouchers' => Voucher::latest()->get()]);
    }
}
