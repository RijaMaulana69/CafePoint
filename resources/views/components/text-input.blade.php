<aside class="w-64 bg-slate-900 text-white flex flex-col">
    <div class="p-6 text-2xl font-bold border-b border-slate-700">☕ CafePoint</div>

    <nav class="flex-1 mt-4">

        <a href="{{ route('dashboard') }}"
           class="block px-6 py-3 hover:bg-slate-800"> Dashboard</a>

        <div class="px-6 mt-5 text-xs uppercase text-gray-400">Master</div>

        <a href="{{ route('kategori') }}"class="block px-6 py-3 hover:bg-slate-800">Kategori</a>
        <a href="#"class="block px-6 py-3 hover:bg-slate-800">Produk</a>
        <a href="#"class="block px-6 py-3 hover:bg-slate-800">Supplier</a>
        <a href="#"class="block px-6 py-3 hover:bg-slate-800">Customer</a>
    </nav>
</aside>