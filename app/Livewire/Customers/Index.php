<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // State Pencarian & Modal
    public $search = '';
    public $showModal = false;
    public $customerId = null;

    // State Form Input Customer
    public $code = '';
    public $name = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $is_active = true;

    // Rules Validasi Dinamis
    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:customers,code,' . $this->customerId,
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function render()
    {
        $query = Customer::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.customers.index', [
            'customers' => $query->latest()->paginate(10),
        ])->layout('layouts.cafepoint');
    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        
        // Auto generate kode customer CUST-001, CUST-002, dst
        $nextNumber = Customer::count() + 1;
        $this->code = 'CUST-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $this->showModal = true;
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        $this->customerId = $customer->id;
        $this->code = $customer->code;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->email = $customer->email;
        $this->address = $customer->address;
        $this->is_active = $customer->is_active;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->customerId) {
            $customer = Customer::findOrFail($this->customerId);
            $customer->update([
                'code' => $this->code,
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Data customer berhasil diperbarui.');
        } else {
            Customer::create([
                'code' => $this->code,
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Data customer berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->showModal = false;
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        session()->flash('success', 'Data customer berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->customerId = null;
        $this->code = '';
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->is_active = true;
    }
}
