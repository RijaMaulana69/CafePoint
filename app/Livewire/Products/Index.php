<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // State Pencarian & Modal
    public $search = '';
    public $showModal = false;
    public $productId = null;

    // State Form Input Produk
    public $category_id = '';
    public $code = '';
    public $name = '';
    public $description = '';
    public $purchase_price = 0;
    public $selling_price = 0;
    public $stock = 0;
    public $minimum_stock = 0;
    public $is_active = true;

    public function render()
    {
        // 1. Query Produk dengan filter pencarian nama atau kode
        $query = Product::with('category');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Ambil data kategori aktif untuk pilihan dropdown di modal
        $categories = Category::where('is_active', true)->get();

        return view('livewire.products.index', [
            'products' => $query->latest()->paginate(10),
            'categories' => $categories,
        ])->layout('layouts.cafepoint');
    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    // Rules Validasi Dinamis
    protected function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|max:50|unique:products,code,' . $this->productId,
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    // Buka Modal Tambah Produk
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    // Buka Modal Edit Produk
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->code = $product->code;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->stock = $product->stock;
        $this->minimum_stock = $product->minimum_stock;
        $this->is_active = $product->is_active;

        $this->showModal = true;
    }

    // Simpan Data (Create / Update)
    public function save()
    {
        $this->validate();

        if ($this->productId) {
            // Update
            $product = Product::findOrFail($this->productId);
            $product->update([
                'category_id' => $this->category_id,
                'code' => $this->code,
                'name' => $this->name,
                'description' => $this->description,
                'purchase_price' => $this->purchase_price,
                'selling_price' => $this->selling_price,
                'stock' => $this->stock,
                'minimum_stock' => $this->minimum_stock,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Produk berhasil diperbarui.');
        } else {
            // Create
            Product::create([
                'category_id' => $this->category_id,
                'code' => $this->code,
                'name' => $this->name,
                'description' => $this->description,
                'purchase_price' => $this->purchase_price,
                'selling_price' => $this->selling_price,
                'stock' => $this->stock,
                'minimum_stock' => $this->minimum_stock,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Produk berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->showModal = false;
    }

    // Hapus Produk
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus.');
    }

    // Reset Form Input
    private function resetForm()
    {
        $this->productId = null;
        $this->category_id = '';
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->purchase_price = 0;
        $this->selling_price = 0;
        $this->stock = 0;
        $this->minimum_stock = 0;
        $this->is_active = true;
    }
}
