<div>
    <div class="p-6">
        <h1 class="text-3xl font-bold">
            Master Kategori
        </h1>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"> Tambah Kategori </button>
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
                        <td class="px-5 py-4">{{ $loop->iteration }}</td>
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
                            <button class="text-blue"> Edit </button>
                            <button class="text-red"> Hapus </button>
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
</div>