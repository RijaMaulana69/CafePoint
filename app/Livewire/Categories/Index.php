<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class Index extends Component
{
    public $name = '';

    public $description = '';

    public $is_active = true;

    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function create()
    {
        $this->resetForm();

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();

        $this->showModal = false;

        session()->flash('success', 'Kategori berhasil ditambahkan.');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.categories.index', [
            'categories' => Category::latest()->get(),
        ])->layout('layouts.cafepoint');
    }
}