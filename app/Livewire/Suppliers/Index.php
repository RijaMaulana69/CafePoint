<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // state Pencarian & Modal
    public $search = '';
    public $showModal = false;
    public $supplierId = null;
    
    // state Form Input Supplier
    public $name = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:150',
        'phone' => 'nullable|string|max:30',
        'email' => 'nullable|string|max:100',
        'address' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $query = Supplier::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }
        
        return view('livewire.suppliers.index',
            [
                'suppliers' => $query->latest()->paginate(10),
            ])->layout('layouts.cafepoint');
    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {

        $supplier = Supplier::findOrFail($id);

        $this->supplierId = $supplier->id;
        $this->name = $supplier->name;
        $this->phone = $supplier->phone;
        $this->email = $supplier->email;
        $this->address = $supplier->address;
        $this->is_active = $supplier->is_active;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if($this->supplierId) {
            $supplier = Supplier::findOrFail($this->supplierId);
            $supplier->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Data supplier berhasil diperbarui.');
        } else {
            Supplier::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Data supplier berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->showModal = false;
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        session()->flash('success', 'Data supplier berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->supplierId = null;
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->is_active = true;
    }
}
