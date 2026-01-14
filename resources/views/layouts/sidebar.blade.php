<aside x-data="{ openMenus: { master: false, student: false, book: false, transaction: false, report: false } }" 
       class="sidebar"
       :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
    
    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
        <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div>
            <h1 class="font-bold text-lg">Perpustakaan</h1>
            <p class="text-xs text-slate-400">Sistem Manajemen</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-4 px-3 overflow-y-auto h-[calc(100vh-100px)]">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Master Data -->
        <div class="mt-2">
            <button @click="openMenus.master = !openMenus.master" 
                    class="sidebar-link w-full justify-between {{ request()->is('master/*') ? 'active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                    <span>Master Data</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.master }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openMenus.master" x-collapse class="sidebar-submenu">
                <a href="{{ route('master.academic-years.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.academic-years.*') ? 'active' : '' }}">Tahun Ajaran</a>
                <a href="{{ route('master.classes.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.classes.*') ? 'active' : '' }}">Kelas</a>
                <a href="{{ route('master.majors.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.majors.*') ? 'active' : '' }}">Jurusan</a>
                <a href="{{ route('master.classifications.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.classifications.*') ? 'active' : '' }}">Klasifikasi DDC</a>
                <a href="{{ route('master.sub-classifications.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.sub-classifications.*') ? 'active' : '' }}">Sub Klasifikasi</a>
                <a href="{{ route('master.publishers.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.publishers.*') ? 'active' : '' }}">Penerbit</a>
                <a href="{{ route('master.categories.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.categories.*') ? 'active' : '' }}">Kategori</a>
                <a href="{{ route('master.book-sources.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.book-sources.*') ? 'active' : '' }}">Sumber Buku</a>
                <a href="{{ route('master.fine-settings.index') }}" class="sidebar-submenu-link {{ request()->routeIs('master.fine-settings.*') ? 'active' : '' }}">Pengaturan Denda</a>
            </div>
        </div>

        <!-- Students -->
        <div class="mt-2">
            <button @click="openMenus.student = !openMenus.student" 
                    class="sidebar-link w-full justify-between {{ request()->is('students*') ? 'active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Siswa</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.student }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openMenus.student" x-collapse class="sidebar-submenu">
                <a href="{{ route('students.index') }}" class="sidebar-submenu-link {{ request()->routeIs('students.index') ? 'active' : '' }}">Daftar Siswa</a>
                <a href="{{ route('students.create') }}" class="sidebar-submenu-link {{ request()->routeIs('students.create') ? 'active' : '' }}">Tambah Siswa</a>
                <a href="{{ route('students.import') }}" class="sidebar-submenu-link {{ request()->routeIs('students.import') ? 'active' : '' }}">Import Siswa</a>
                <a href="{{ route('students.promotion') }}" class="sidebar-submenu-link {{ request()->routeIs('students.promotion') ? 'active' : '' }}">Kenaikan Kelas</a>
            </div>
        </div>

        <!-- Books -->
        <div class="mt-2">
            <button @click="openMenus.book = !openMenus.book" 
                    class="sidebar-link w-full justify-between {{ request()->is('books*') ? 'active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Buku</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.book }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openMenus.book" x-collapse class="sidebar-submenu">
                <a href="{{ route('books.index') }}" class="sidebar-submenu-link {{ request()->routeIs('books.index') ? 'active' : '' }}">Daftar Buku</a>
                <a href="{{ route('books.create') }}" class="sidebar-submenu-link {{ request()->routeIs('books.create') ? 'active' : '' }}">Tambah Buku</a>
                <a href="{{ route('books.import') }}" class="sidebar-submenu-link {{ request()->routeIs('books.import') ? 'active' : '' }}">Import Buku</a>
                <a href="{{ route('books.barcode') }}" class="sidebar-submenu-link {{ request()->routeIs('books.barcode') ? 'active' : '' }}">Generate Barcode</a>
            </div>
        </div>

        <!-- Transactions -->
        <div class="mt-2">
            <button @click="openMenus.transaction = !openMenus.transaction" 
                    class="sidebar-link w-full justify-between {{ request()->is('transactions*') ? 'active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>Transaksi</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.transaction }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openMenus.transaction" x-collapse class="sidebar-submenu">
                <a href="{{ route('transactions.loans.create') }}" class="sidebar-submenu-link {{ request()->routeIs('transactions.loans.create') ? 'active' : '' }}">Peminjaman</a>
                <a href="{{ route('transactions.returns.create') }}" class="sidebar-submenu-link {{ request()->routeIs('transactions.returns.create') ? 'active' : '' }}">Pengembalian</a>
            </div>
        </div>

        <!-- Reports -->
        <div class="mt-2">
            <button @click="openMenus.report = !openMenus.report" 
                    class="sidebar-link w-full justify-between {{ request()->is('reports*') ? 'active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.report }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openMenus.report" x-collapse class="sidebar-submenu">
                <a href="{{ route('reports.loans') }}" class="sidebar-submenu-link {{ request()->routeIs('reports.loans') ? 'active' : '' }}">Laporan Peminjaman</a>
                <a href="{{ route('reports.fines') }}" class="sidebar-submenu-link {{ request()->routeIs('reports.fines') ? 'active' : '' }}">Laporan Denda</a>
                <a href="{{ route('reports.books') }}" class="sidebar-submenu-link {{ request()->routeIs('reports.books') ? 'active' : '' }}">Laporan Buku</a>
            </div>
        </div>
    </nav>
</aside>
