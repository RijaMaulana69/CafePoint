<div>
    {{-- Flash Message Success --}}
    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header & Search --}}
    <div class="flex items-center justify-between mb-6">
        <div class="w-full md:w-1/3">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari kode atau nama produk..."
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>

        <h1 class="text-3xl font-bold text-gray-800">
            Master Produk
        </h1>

        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
            + Tambah Produk
        </button>
    </div>

    {{-- Tabel Data Produk --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">No</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Kode</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Nama Produk</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Kategori</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Harga Beli</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Harga Jual</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Stok</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                        <td class="px-5 py-4 text-sm font-mono font-semibold text-blue-600">{{ $product->code }}</td>
                        <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            <span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded-full font-medium">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-gray-900">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-sm">
                            @if ($product->stock <= $product->minimum_stock)
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold" title="Stok menipis!">
                                    {{ $product->stock }} (Min: {{ $product->minimum_stock }})
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">
                                    {{ $product->stock }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm">
                            @if ($product->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-center space-x-2">
                            <button wire:click="edit({{ $product->id }})" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                            <button
                                wire:click="delete({{ $product->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus produk ini?"
                                class="text-red-600 hover:text-red-800 font-medium">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-500">Belum ada produk yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>

    {{-- Modal Tambah / Edit Produk --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6 overflow-y-auto">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl my-8">

                {{-- Header Modal --}}
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ $productId ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">
                        &times;
                    </button>
                </div>

                {{-- Form Modal --}}
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                    {{-- Grid 2 Kolom: Kategori & Kode Produk --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="category_id"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kode Produk --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kode Produk <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="code"
                                placeholder="Contoh: KOP-001"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 uppercase">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Nama Produk --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: Espresso Double Shot"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea
                            wire:model="description"
                            rows="2"
                            placeholder="Keterangan singkat produk..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid 2 Kolom: Harga Beli & Harga Jual --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Harga Beli (Modal) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="purchase_price"
                                min="0"
                                placeholder="0"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('purchase_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Harga Jual <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="selling_price"
                                min="0"
                                placeholder="0"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('selling_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Grid 2 Kolom: Stok & Stok Minimum --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Stok Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="stock"
                                min="0"
                                placeholder="0"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Stok Minimum (Peringatan) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="minimum_stock"
                                min="0"
                                placeholder="0"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('minimum_stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status Aktif --}}
                    <div class="flex items-center gap-3 pt-2">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            id="is_active_prod"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                        <label for="is_active_prod" class="text-sm font-medium text-gray-700">
                            Produk Aktif (Tampil di Kasir/POS)
                        </label>
                    </div>

                </div>

                {{-- Footer Modal --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
                    <button
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium">
                        Batal
                    </button>

                    <button
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium transition flex items-center gap-2">
                        <span wire:loading.remove>
                            {{ $productId ? 'Update Produk' : 'Simpan Produk' }}
                        </span>
                        <span wire:loading>
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
