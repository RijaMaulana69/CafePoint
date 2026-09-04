<div class="h-full flex flex-col gap-4">
    {{-- Notifikasi Error --}}
    @if (session()->has('error'))
        <div class="rounded-lg bg-red-100 px-4 py-3 text-red-700 font-medium">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Layout 2 Kolom: Sisi Kiri (Produk) & Sisi Kanan (Cart) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 flex-1">

        {{-- SISI KIRI: KATALOG PRODUK (8 Kolom) --}}
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-4">
            
            {{-- Search & Filter Kategori --}}
            <div class="bg-white p-4 rounded-2xl shadow flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="w-full sm:w-1/2">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="🔍 Cari nama atau kode produk..."
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
                    <button
                        wire:click="$set('selectedCategory', '')"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $selectedCategory === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua Kategori
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            wire:click="$set('selectedCategory', {{ $cat->id }})"
                            class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $selectedCategory == $cat->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid Kartu Produk --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 overflow-y-auto max-h-[calc(100vh-220px)] p-1">
                @forelse ($products as $prod)
                    <div
                        wire:click="addToCart({{ $prod->id }})"
                        class="bg-white rounded-2xl p-4 shadow hover:shadow-md transition cursor-pointer border border-transparent hover:border-blue-500 flex flex-col justify-between group">
                        
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-mono font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">
                                    {{ $prod->code }}
                                </span>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $prod->stock <= $prod->minimum_stock ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    Stok: {{ $prod->stock }}
                                </span>
                            </div>
                            <h3 class="font-bold text-gray-800 text-sm group-hover:text-blue-600 transition line-clamp-2">
                                {{ $prod->name }}
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $prod->category->name ?? '-' }}
                            </p>
                        </div>

                        <div class="mt-4 flex justify-between items-center pt-2 border-t border-gray-100">
                            <span class="font-bold text-blue-600 text-sm">
                                Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                            </span>
                            <span class="bg-blue-600 text-white p-1.5 rounded-xl group-hover:scale-110 transition">
                                ➕
                            </span>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full bg-white p-8 rounded-2xl text-center text-gray-500 shadow">
                        Tidak ada produk ditemukan.
                    </div>
                @endforelse
            </div>

        </div>

        {{-- SISI KANAN: KERANJANG BELANJA / CART (4-5 Kolom) --}}
        <div class="lg:col-span-5 xl:col-span-4 bg-white rounded-2xl shadow p-5 flex flex-col justify-between h-full">
            
            <div>
                {{-- Header Keranjang & Pelanggan --}}
                <div class="flex justify-between items-center mb-4 pb-3 border-b">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        🛒 Keranjang Belanja
                    </h2>
                    @if (!empty($cart))
                        <button wire:click="clearCart" class="text-xs text-red-600 hover:text-red-800 font-semibold">
                            Kosongkan
                        </button>
                    @endif
                </div>

                {{-- Select Pelanggan --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pelanggan</label>
                    <select
                        wire:model="customer_id"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">-- Non Member / Pembeli Umum --</option>
                        @foreach ($customers as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->code }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Lista Item Keranjang --}}
                <div class="space-y-3 overflow-y-auto max-h-[calc(100vh-420px)] pr-1">
                    @forelse ($cart as $id => $item)
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <div class="flex-1 min-w-0 pr-2">
                                <h4 class="font-semibold text-gray-800 text-sm truncate">{{ $item['name'] }}</h4>
                                <div class="text-xs text-gray-500">
                                    Rp {{ number_format($item['selling_price'], 0, ',', '.') }} x {{ $item['qty'] }}
                                </div>
                                <div class="font-semibold text-blue-600 text-xs mt-0.5">
                                    Subtotal: Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="updateQty({{ $id }}, {{ $item['qty'] - 1 }})"
                                    class="w-7 h-7 bg-white rounded-lg border font-bold text-gray-600 hover:bg-gray-100 flex items-center justify-center">
                                    -
                                </button>

                                <span class="text-xs font-bold w-6 text-center">{{ $item['qty'] }}</span>

                                <button
                                    wire:click="updateQty({{ $id }}, {{ $item['qty'] + 1 }})"
                                    class="w-7 h-7 bg-white rounded-lg border font-bold text-gray-600 hover:bg-gray-100 flex items-center justify-center">
                                    +
                                </button>

                                <button
                                    wire:click="removeItem({{ $id }})"
                                    class="text-red-500 hover:text-red-700 ml-1">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-400 text-sm">
                            Keranjang belanja masih kosong.<br>Klik produk di sebelah kiri untuk menambah.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Ringkasan Total & Tombol Bayar --}}
            <div class="pt-4 border-t border-gray-200 mt-4 space-y-3">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-semibold">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Diskon (Rp)</label>
                        <input
                            type="number"
                            wire:model.live="discount"
                            min="0"
                            class="w-full rounded-lg border-gray-300 text-xs p-1.5">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Pajak (Rp)</label>
                        <input
                            type="number"
                            wire:model.live="tax"
                            min="0"
                            class="w-full rounded-lg border-gray-300 text-xs p-1.5">
                    </div>
                </div>

                <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t">
                    <span>Total Bayar</span>
                    <span class="text-blue-600 text-xl">Rp {{ number_format($this->totalPrice, 0, ',', '.') }}</span>
                </div>

                <button
                    wire:click="openPaymentModal"
                    @disabled(empty($cart))
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white font-bold rounded-xl transition shadow-lg flex items-center justify-center gap-2 text-base">
                    💳 Bayar Sekarang
                </button>
            </div>

        </div>

    </div>

    {{-- MODAL PEMBAYARAN --}}
    @if ($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-b">
                    <h3 class="font-bold text-gray-800 text-lg">💳 Form Pembayaran</h3>
                    <button wire:click="$set('showPaymentModal', false)" class="text-gray-400 text-xl font-bold">&times;</button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 p-4 rounded-xl text-center">
                        <span class="text-xs text-blue-600 font-semibold uppercase">Total Tagihan</span>
                        <div class="text-3xl font-extrabold text-blue-700">
                            Rp {{ number_format($this->totalPrice, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Metode Pembayaran</label>
                        <select wire:model="payment_method" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="cash">💵 Tunai (Cash)</option>
                            <option value="qris">📱 QRIS / E-Wallet</option>
                            <option value="transfer">🏦 Bank Transfer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Uang Diterima (Rp)</label>
                        <input
                            type="number"
                            wire:model.live="pay_amount"
                            class="w-full rounded-xl border-gray-300 text-lg font-bold text-gray-800">
                    </div>

                    {{-- Shortcut Tombol Uang Pas / Pecahan --}}
                    <div class="flex gap-2 flex-wrap">
                        <button wire:click="$set('pay_amount', {{ $this->totalPrice }})" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-lg font-semibold">Uang Pas</button>
                        <button wire:click="$set('pay_amount', 10000)" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-lg font-semibold">10.000</button>
                        <button wire:click="$set('pay_amount', 20000)" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-lg font-semibold">20.000</button>
                        <button wire:click="$set('pay_amount', 50000)" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-lg font-semibold">50.000</button>
                        <button wire:click="$set('pay_amount', 100000)" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-lg font-semibold">100.000</button>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl flex justify-between items-center border">
                        <span class="text-xs font-semibold text-gray-600">Kembalian</span>
                        <span class="text-lg font-bold {{ $this->changeAmount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Transaksi</label>
                        <textarea wire:model="note" rows="2" placeholder="Catatan tambahan..." class="w-full rounded-xl border-gray-300 text-xs"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                    <button wire:click="$set('showPaymentModal', false)" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-200 rounded-xl">Batal</button>
                    <button
                        wire:click="processPayment"
                        wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition">
                        ✓ Konfirmasi Transaksi
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL STRUK / NOTA PENJUALAN --}}
    @if ($showReceiptModal && $lastSale)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6 overflow-y-auto">
            <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 font-mono text-xs space-y-4">
                
                {{-- Header Struk --}}
                <div class="text-center border-b pb-3 space-y-1">
                    <h2 class="text-base font-bold tracking-wider text-gray-800">☕ CAFEPOINT POS</h2>
                    <p class="text-gray-500">Jl. Kopi Nikmat No. 1, Majalengka</p>
                    <p class="text-gray-500">Telp: 0812-3456-7890</p>
                </div>

                {{-- Info Transaksi --}}
                <div class="space-y-1 border-b pb-3 text-gray-700">
                    <div class="flex justify-between">
                        <span>No. Faktur:</span>
                        <span class="font-bold">{{ $lastSale->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal:</span>
                        <span>{{ $lastSale->transaction_date }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir:</span>
                        <span>{{ $lastSale->user->name ?? 'Kasir' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pelanggan:</span>
                        <span>{{ $lastSale->customer->name ?? 'Pembeli Umum' }}</span>
                    </div>
                </div>

                {{-- Rincian Barang --}}
                <div class="space-y-2 border-b pb-3">
                    @foreach ($lastSale->details as $d)
                        <div>
                            <div class="font-bold text-gray-800">{{ $d->product_name }}</div>
                            <div class="flex justify-between text-gray-600">
                                <span>{{ $d->quantity }} x Rp {{ number_format($d->selling_price, 0, ',', '.') }}</span>
                                <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Total & Bayar --}}
                <div class="space-y-1 border-b pb-3">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($lastSale->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($lastSale->discount > 0)
                        <div class="flex justify-between">
                            <span>Diskon:</span>
                            <span>- Rp {{ number_format($lastSale->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($lastSale->tax > 0)
                        <div class="flex justify-between">
                            <span>Pajak:</span>
                            <span>+ Rp {{ number_format($lastSale->tax, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-sm text-gray-900 pt-1">
                        <span>TOTAL:</span>
                        <span>Rp {{ number_format($lastSale->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Bayar ({{ strtoupper($lastSale->payment_method) }}):</span>
                        <span>Rp {{ number_format($lastSale->pay_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span>Kembali:</span>
                        <span>Rp {{ number_format($lastSale->change_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Footer Struk --}}
                <div class="text-center text-gray-500 pt-2 space-y-1">
                    <p>Terima kasih atas kunjungan Anda!</p>
                    <p>-- Selamat Menikmati Kopi Anda ☕ --</p>
                </div>

                <div class="pt-3 flex gap-2 font-sans">
                    <button onclick="window.print()" class="flex-1 py-2 bg-gray-800 text-white rounded-xl font-bold text-xs hover:bg-gray-900">
                        🖨️ Cetak Struk
                    </button>
                    <button wire:click="$set('showReceiptModal', false)" class="flex-1 py-2 bg-blue-600 text-white rounded-xl font-bold text-xs hover:bg-blue-700">
                        ✨ Transaksi Baru
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
