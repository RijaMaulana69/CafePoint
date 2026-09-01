<div>
    @if (session()->has('success'))
    <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-700">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="flex items-center justify-between mb-6">
        <div class="mb-4">
            <input
                type="text"
                wire:model="search"
                wire:keyup="updateSearch"
                placeholder="Cari kategori..."
                class="w-full md:w-1/3 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"> 

        </div>
        <h1 class="text-3xl font-bold">
            Master Kategori
        </h1>

        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">+ Tambah Kategori</button>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left px-5 py-3"> No </th>
                    <th class="text-left px-5 py-3"> Nama</th>
                    <th class="text-left px-5 py-3"> Deskripsi</th>
                    <th class="text-left px-5 py-3"> Status</th>
                    <th class="text-left px-5 py-3"> Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-t">
                        <td class="px-5 py-4">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                        <td class="px-5 py-4 font-medium">{{ $category->name }}</td>
                        <td class="px-5 py-4">{{ $category->description }}</td>
                        <td class="px-5 py-4">
                            @if ($category->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded"> Aktif </span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded"> Nonaktif </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-center">
                            <button wire:click="edit({{ $category->id }})"
                            class="text-blue-600 hover:text-blue-800"> Edit </button>
                            <button 
                            wire:click="delete({{ $category->id }})"
                            wire:confirm="Apakah Anda yakin ingin menghapus kategori ini?"
                            class="text-red-600 hover:text-red-800"> Hapus 
                            </button>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500"> Belum ada kategori. </td>
                    </tr>

                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2 text-sm text-gray-500">
        Menampilkan
        {{ $categories->firstItem() }}
        Sampai
        {{ $categories->lastItem() }}
        dari
        {{ $categories->total() }}
        kategori
    </div>>

    </div>
    <div class="mt-4">
        {{ $categories->links() }}
    </div>  

    @if($showModal)

    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

        <div
            class="w-full max-w-lg bg-white rounded-2xl shadow-xl">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">

                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori' }}
                </h2>

                <button
                    wire:click="$set('showModal', false)"
                    class="text-gray-400 hover:text-gray-600 text-2xl">

                    &times;

                </button>

            </div>

            {{-- Form --}}
            <div class="p-6 space-y-5">

                {{-- Nama --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori
                    </label>

                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Contoh: Kopi"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                    @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Deskripsi --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>

                    <textarea
                        wire:model="description"
                        rows="3"
                        placeholder="Deskripsi kategori..."
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>

                    @error('description')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3">

                    <input
                        type="checkbox"
                        wire:model="is_active"
                        id="is_active"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                    <label for="is_active"
                           class="text-sm text-gray-700">

                        Kategori Aktif

                    </label>

                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">

                <button
                    wire:click="$set('showModal', false)"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">

                    Batal

                </button>

                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">

                    <span wire:loading.remove>
                        {{ $categoryId ? 'Update' : 'Simpan' }}
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