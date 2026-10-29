<?php

namespace App\Livewire\Account;

use App\Livewire\Concerns\Toasts;
use App\Models\Address;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Buku Alamat')]
class Addresses extends Component
{
    use Toasts;

    public ?int $editingId = null;
    public bool $showForm = false;

    public string $label = 'Rumah';
    public string $recipient_name = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';

    public function create(): void
    {
        $this->resetForm();
        $this->recipient_name = auth()->user()->name;
        $this->phone = (string) auth()->user()->phone;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $a = Address::where('user_id', auth()->id())->findOrFail($id);
        $this->editingId = $a->id;
        $this->fill($a->only('label', 'recipient_name', 'phone', 'address', 'city', 'postal_code'));
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'label'          => 'required|string|max:30',
            'recipient_name' => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:80',
            'postal_code'    => 'required|string|max:10',
        ]);

        if ($this->editingId) {
            Address::where('user_id', auth()->id())->findOrFail($this->editingId)->update($data);
        } else {
            $isFirst = ! Address::where('user_id', auth()->id())->exists();
            Address::create($data + ['user_id' => auth()->id(), 'is_default' => $isFirst]);
        }

        $this->resetForm();
        $this->toast('Alamat disimpan');
    }

    public function setDefault(int $id): void
    {
        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        Address::where('user_id', auth()->id())->where('id', $id)->update(['is_default' => true]);
        $this->toast('Alamat utama diubah');
    }

    public function delete(int $id): void
    {
        Address::where('user_id', auth()->id())->where('id', $id)->delete();
        $this->toast('Alamat dihapus');
    }

    protected function resetForm(): void
    {
        $this->reset('editingId', 'showForm', 'label', 'recipient_name', 'phone', 'address', 'city', 'postal_code');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.account.addresses', [
            'addresses' => Address::where('user_id', auth()->id())->orderByDesc('is_default')->get(),
        ])->layout('components.layouts.app');
    }
}
