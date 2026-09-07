<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Absensi RPL SMK</title>
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const dark = saved ? saved === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased dark:bg-slate-900 dark:text-slate-100">

<div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex flex-col w-64 bg-slate-900 text-slate-200 shrink-0">
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800">
            <div class="w-9 h-9 rounded-lg bg-indigo-500 flex items-center justify-center font-bold text-white text-sm">AR</div>
            <div>
                <div class="font-semibold text-white leading-tight">Absensi RPL</div>
                <div class="text-xs text-slate-400">SMK RPL</div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-heroicon.home class="w-5 h-5"/>
                <span>Dashboard</span>
            </x-nav-link>

            @if(auth()->user()->isAdmin())
                <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Data Master</div>
                <x-nav-link :href="route('admin.siswa.index')" :active="request()->routeIs('admin.siswa.*')">
                    <x-heroicon.users class="w-5 h-5"/>
                    <span>Data Siswa</span>
                </x-nav-link>
                <x-nav-link :href="route('admin.guru.index')" :active="request()->routeIs('admin.guru.*')">
                    <x-heroicon.academic-cap class="w-5 h-5"/>
                    <span>Data Guru</span>
                </x-nav-link>
                <x-nav-link :href="route('admin.mapel.index')" :active="request()->routeIs('admin.mapel.*')">
                    <x-heroicon.book-open class="w-5 h-5"/>
                    <span>Mata Pelajaran</span>
                </x-nav-link>
                <x-nav-link :href="route('admin.jadwal.index')" :active="request()->routeIs('admin.jadwal.*')">
                    <x-heroicon.calendar-days class="w-5 h-5"/>
                    <span>Jadwal Mengajar</span>
                </x-nav-link>
                <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Laporan</div>
                <x-nav-link :href="route('admin.laporan')" :active="request()->routeIs('admin.laporan')">
                    <x-heroicon.chart-bar class="w-5 h-5"/>
                    <span>Laporan Absensi</span>
                </x-nav-link>
            @endif

            @if(auth()->user()->isGuru())
                <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Absensi</div>
                <x-nav-link :href="route('guru.absensi.jadwal')" :active="request()->routeIs('guru.absensi.jadwal') || request()->routeIs('guru.absensi.create')">
                    <x-heroicon.finger-print class="w-5 h-5"/>
                    <span>Input Absensi</span>
                </x-nav-link>
                <x-nav-link :href="route('guru.absensi.riwayat')" :active="request()->routeIs('guru.absensi.riwayat')">
                    <x-heroicon.clock class="w-5 h-5"/>
                    <span>Riwayat Absensi</span>
                </x-nav-link>
            @endif

            @if(auth()->user()->isSiswa())
                <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Menu</div>
                <x-nav-link :href="route('siswa.riwayat')" :active="request()->routeIs('siswa.riwayat')">
                    <x-heroicon.clock class="w-5 h-5"/>
                    <span>Riwayat Absensi</span>
                </x-nav-link>
                <x-nav-link :href="route('siswa.profil')" :active="request()->routeIs('siswa.profil')">
                    <x-heroicon.user-circle class="w-5 h-5"/>
                    <span>Profil Saya</span>
                </x-nav-link>
            @endif
        </nav>

        <div class="border-t border-slate-800 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <x-heroicon.arrow-left-start-on-rectangle class="w-5 h-5"/>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Topbar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 dark:bg-slate-800 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center gap-2 font-semibold text-slate-800">
                    <span class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center font-bold text-white text-xs">AR</span>
                    Absensi RPL
                </a>
                <h1 class="hidden lg:block text-sm font-medium text-gray-500 capitalize dark:text-slate-400">
                    {{ ucfirst(str_replace('-', ' ', request()->segment(1) ?: 'dashboard')) }}
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleTheme()" title="Ganti tema" class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 transition dark:text-slate-300 dark:hover:bg-slate-700">
                    <span id="theme-icon-light" class="hidden">
                        <x-heroicon.sun class="w-5 h-5"/>
                    </span>
                    <span id="theme-icon-dark" class="hidden">
                        <x-heroicon.moon class="w-5 h-5"/>
                    </span>
                </button>
                <div class="hidden sm:block text-right">
                    <div class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500 capitalize dark:text-slate-400">{{ auth()->user()->role }}</div>
                </div>
                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-700">Keluar</button>
                </form>
            </div>
        </header>

        {{-- Mobile nav bar --}}
        <nav class="lg:hidden bg-white border-b border-gray-200 flex gap-1 px-3 py-2 overflow-x-auto dark:bg-slate-800 dark:border-slate-700">
            <a href="{{ route('dashboard') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Dashboard</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.siswa.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('admin.siswa.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Siswa</a>
                <a href="{{ route('admin.guru.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('admin.guru.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Guru</a>
                <a href="{{ route('admin.mapel.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('admin.mapel.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Mapel</a>
                <a href="{{ route('admin.jadwal.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('admin.jadwal.*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Jadwal</a>
                <a href="{{ route('admin.laporan') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('admin.laporan') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Laporan</a>
            @endif
            @if(auth()->user()->isGuru())
                <a href="{{ route('guru.absensi.jadwal') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('guru.absensi.jadwal') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Input</a>
                <a href="{{ route('guru.absensi.riwayat') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('guru.absensi.riwayat') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Riwayat</a>
            @endif
            @if(auth()->user()->isSiswa())
                <a href="{{ route('siswa.riwayat') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('siswa.riwayat') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Riwayat</a>
                <a href="{{ route('siswa.profil') }}" class="px-3 py-1.5 text-xs font-medium rounded-md {{ request()->routeIs('siswa.profil') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">Profil</a>
            @endif
        </nav>

        {{-- Content --}}
        <main class="flex-1 p-4 lg:p-8 lg:pt-6">
            @if(session('success'))
                <div class="mb-4 flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300">
                    {{ session('success') }}
                    <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">&times;</button>
                </div>
            @endif

            @if($errors->any() && !request()->routeIs('login'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/30 dark:text-red-300">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="px-4 lg:px-8 py-4 text-center text-xs text-gray-400 dark:text-slate-500">
            Sistem Absensi Kelas RPL &copy; {{ date('Y') }}
        </footer>
    </div>
</div>

<script>
    function syncThemeIcons() {
        const dark = document.documentElement.classList.contains('dark');
        document.getElementById('theme-icon-light').classList.toggle('hidden', !dark);
        document.getElementById('theme-icon-dark').classList.toggle('hidden', dark);
    }

    function toggleTheme() {
        const dark = !document.documentElement.classList.contains('dark');
        document.documentElement.classList.toggle('dark', dark);
        localStorage.setItem('theme', dark ? 'dark' : 'light');
        syncThemeIcons();
    }

    syncThemeIcons();
</script>

</body>
</html>