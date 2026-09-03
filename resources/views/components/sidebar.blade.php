<aside class="w-64 bg-slate-900 text-white flex flex-col h-full flex-shrink-0 shadow-xl border-r border-slate-800">


    {{-- Brand Logo --}}
    <div class="p-6 text-2xl font-bold border-b border-slate-800 flex items-center gap-3">
        <span class="text-3xl">☕</span>
        <span class="tracking-wide">CafePoint</span>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 px-4 py-6 space-y-1">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
            <span>📊</span>
            <span>Dashboard</span>
        </a>

        {{-- Section Header --}}
        <div class="px-4 pt-6 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">
            Master Data
        </div>
        
        {{-- Kategori --}}
        <a href="{{ route('kategori') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 font-medium {{ request()->routeIs('kategori') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
            <span>🏷️</span>
            <span>Kategori</span>
        </a>

        {{-- Produk --}}
        <a href="{{ route('produk') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 font-medium {{ request()->routeIs('produk') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
            <span>☕</span>
            <span>Produk</span>
        </a>

        {{-- Supplier --}}
        <a href="{{ route('supplier') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 font-medium {{ request()->routeIs('supplier') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
            <span>🚚</span>
            <span>Supplier</span>
        </a>

        {{-- Customer --}}
        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 font-medium {{ request()->routeIs('customer*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
            <span>👥</span>
            <span>Customer</span>
        </a>

    </nav>

</aside>
