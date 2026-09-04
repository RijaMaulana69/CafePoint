<?php

namespace App\Livewire\Pos;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    // Filter & Search Produk
    public $search = '';
    public $selectedCategory = '';

    // Keranjang Belanja (Cart)
    public $cart = [];

    // Form Transaksi
    public $customer_id = '';
    public $discount = 0;
    public $tax = 0;
    public $pay_amount = 0;
    public $payment_method = 'cash';
    public $note = '';

    // Modal State
    public $showPaymentModal = false;
    public $showReceiptModal = false;
    public $lastSale = null;

    // Tambah Produk ke Keranjang Belanja
    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->stock <= 0) {
            session()->flash('error', 'Stok produk ini sedang habis!');
            return;
        }

        if (isset($this->cart[$productId])) {
            // Jika sudah ada di cart, cek batas stok
            if ($this->cart[$productId]['qty'] + 1 > $product->stock) {
                session()->flash('error', 'Jumlah melebihi stok yang tersedia!');
                return;
            }
            $this->cart[$productId]['qty'] += 1;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['selling_price'];
        } else {
            // Item baru di cart
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'purchase_price' => $product->purchase_price,
                'selling_price' => $product->selling_price,
                'qty' => 1,
                'subtotal' => $product->selling_price,
                'stock' => $product->stock,
            ];
        }
    }

    // Ubah Jumlah Qty di Keranjang
    public function updateQty($productId, $newQty)
    {
        $newQty = (int) $newQty;

        if ($newQty <= 0) {
            $this->removeItem($productId);
            return;
        }

        $product = Product::findOrFail($productId);

        if ($newQty > $product->stock) {
            session()->flash('error', 'Jumlah melebihi stok yang tersedia!');
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty'] = $newQty;
            $this->cart[$productId]['subtotal'] = $newQty * $this->cart[$productId]['selling_price'];
        }
    }

    // Hapus Item dari Keranjang
    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
    }

    // Kosongkan Keranjang
    public function clearCart()
    {
        $this->cart = [];
        $this->discount = 0;
        $this->tax = 0;
        $this->pay_amount = 0;
    }

    // Hitung Subtotal Seluruh Item
    public function getSubtotalProperty()
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    // Hitung Total Bayar Akhir
    public function getTotalPriceProperty()
    {
        $subtotal = $this->subtotal;
        $discountAmount = (float) $this->discount;
        $taxAmount = (float) $this->tax;

        $total = $subtotal - $discountAmount + $taxAmount;
        return $total > 0 ? $total : 0;
    }

    // Hitung Kembalian Uang
    public function getChangeAmountProperty()
    {
        $pay = (float) $this->pay_amount;
        $total = $this->totalPrice;

        return $pay >= $total ? $pay - $total : 0;
    }

    // Buka Modal Pembayaran
    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja masih kosong!');
            return;
        }

        $this->pay_amount = $this->totalPrice; // Default terisi uang pas
        $this->showPaymentModal = true;
    }

    // Proses Simpan Transaksi Penjualan (Checkout)
    public function processPayment()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja kosong!');
            return;
        }

        if ((float) $this->pay_amount < $this->totalPrice) {
            session()->flash('error', 'Jumlah uang pembayaran kurang!');
            return;
        }

        DB::transaction(function () {
            // 1. Generate Nomor Faktur INV-YYYYMMDD-0001
            $todayCount = Sale::whereDate('created_at', today())->count() + 1;
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($todayCount, 4, '0', STR_PAD_LEFT);

            // 2. Simpan Header Transaksi (Sale)
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id() ?? 1,
                'customer_id' => $this->customer_id ?: null,
                'transaction_date' => now(),
                'subtotal' => $this->subtotal,
                'discount' => (float) $this->discount,
                'tax' => (float) $this->tax,
                'total_price' => $this->totalPrice,
                'pay_amount' => (float) $this->pay_amount,
                'change_amount' => $this->changeAmount,
                'payment_method' => $this->payment_method,
                'note' => $this->note,
            ]);

            // 3. Simpan Rincian Item (SaleDetail) & Otomatis Potong Stok Produk
            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_code' => $item['code'],
                    'product_name' => $item['name'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Potong Stok Produk
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            $this->lastSale = $sale->load(['details', 'customer', 'user']);
        });

        // Selesai Transaksi, Reset Cart & Tampilkan Modal Struk Nota
        $this->showPaymentModal = false;
        $this->showReceiptModal = true;
        $this->clearCart();
    }

    public function render()
    {
        // Query Produk Aktif
        $productQuery = Product::with('category')->where('is_active', true);

        if ($this->selectedCategory) {
            $productQuery->where('category_id', $this->selectedCategory);
        }

        if ($this->search) {
            $productQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.pos.index', [
            'products' => $productQuery->latest()->get(),
            'categories' => Category::where('is_active', true)->get(),
            'customers' => Customer::where('is_active', true)->get(),
        ])->layout('layouts.cafepoint');
    }
}
