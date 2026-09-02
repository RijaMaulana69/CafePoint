<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $name = '';

    public $description = '';

    public $is_active = true;

    public $showModal = false;

    public $categoryId = null;

    public $search = '';

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

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->is_active = $category->is_active;

        $this->showModal = true;
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        session()->flash('success', 'Kategori berhasil dihapus.');
    }

    public function save()
    {
        $this->validate();

        if ($this->categoryId) {

            $category = Category::findOrFail($this->categoryId);

            $category->update([
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            session()->flash(
                'success',
                'Kategori berhasil diperbarui.'
            );

        } else {

            Category::create([
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            session()->flash(
                'success',
                'Kategori berhasil ditambahkan.'
            );
        }

        $this->resetForm();

        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->categoryId = null;

        $this->name = '';

        $this->description = '';

        $this->is_active = true;
    }

    public function render()
    {
        $query = Category::query();
            if ($this->search) {
                $query->where ('name', 'like', '%' . $this->search . '%');
            }
        return view('livewire.categories.index', [
            'categories' => $query->latest()->paginate(10),
        ])->layout('layouts.cafepoint');
    }

    public function updateSearch()
    {
        $this->resetPage();
    }
}