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
                placeholder="Cari nama, telepon, atau email supplier..."
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>

        <h1 class="text-3xl font-bold text-gray-800">
            Master Supplier
        </h1>

        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
            + Tambah Supplier
        </button>
    </div>

    {{-- Tabel Data Supplier --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">No</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Nama Supplier</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Telepon / WA</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Email</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Alamat</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($suppliers as $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm">{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-gray-900">{{ $supplier->name }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $supplier->phone ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $supplier->email ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $supplier->address ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm">
                            @if ($supplier->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-center space-x-2">
                            <button wire:click="edit({{ $supplier->id }})" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                            <button
                                wire:click="delete({{ $supplier->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus supplier ini?"
                                class="text-red-600 hover:text-red-800 font-medium">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">Belum ada data supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>

    {{-- Modal Tambah / Edit Supplier --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6 overflow-y-auto">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl my-8">

                {{-- Header Modal --}}
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ $supplierId ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">
                        &times;
                    </button>
                </div>

                {{-- Form Modal --}}
                <div class="p-6 space-y-4">

                    {{-- Nama Supplier --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Supplier / Vendor <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: PT Kopi Nusantara"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid 2 Kolom: Telepon & Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon / WhatsApp</label>
                            <input
                                type="text"
                                wire:model="phone"
                                placeholder="Contoh: 081234567890"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input
                                type="email"
                                wire:model="email"
                                placeholder="sales@vendor.com"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea
                            wire:model="address"
                            rows="3"
                            placeholder="Alamat kantor / gudang supplier..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Aktif --}}
                    <div class="flex items-center gap-3 pt-2">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            id="is_active_supp"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                        <label for="is_active_supp" class="text-sm font-medium text-gray-700">
                            Supplier Aktif
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
                            {{ $supplierId ? 'Update Supplier' : 'Simpan Supplier' }}
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
