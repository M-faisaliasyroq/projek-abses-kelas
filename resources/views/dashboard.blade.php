@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $role = auth()->user()->role;
@endphp

@if($role === 'admin')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="Total Siswa" :value="$totalSiswa" icon="users" color="indigo"/>
        <x-stat-card label="Total Guru" :value="$totalGuru" icon="academic-cap" color="green"/>
        <x-stat-card label="Mata Pelajaran" :value="$totalMapel" icon="book-open" color="amber"/>
        <x-stat-card label="Jadwal Aktif" :value="$totalJadwal" icon="calendar-days" color="rose"/>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
        <h3 class="font-semibold text-gray-800 dark:text-slate-100 mb-1">Selamat datang, {{ auth()->user()->name }}!</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">
            Anda login sebagai <span class="font-medium text-gray-700 dark:text-slate-200">Administrator</span>.
            Gunakan menu di samping untuk mengelola data siswa, guru, mata pelajaran, jadwal, dan melihat laporan.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <a href="{{ route('admin.siswa.index') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-indigo-300 hover:shadow-sm transition">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300 flex items-center justify-center"><x-heroicon.users class="w-5 h-5"/></span>
                    <div class="font-medium text-gray-800 dark:text-slate-100">Kelola Siswa</div>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400">Tambah, ubah, dan hapus data siswa.</p>
            </a>
            <a href="{{ route('admin.jadwal.index') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-5 hover:border-indigo-300 hover:shadow-sm transition">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300 flex items-center justify-center"><x-heroicon.calendar-days class="w-5 h-5"/></span>
                    <div class="font-medium text-gray-800 dark:text-slate-100">Atur Jadwal</div>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400">Buat jadwal mengajar per mata pelajaran dan kelas.</p>
            </a>
        </div>
    </div>
@elseif($role === 'guru')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <x-stat-card label="Total Jadwal" :value="$totalJadwal" icon="calendar-days" color="indigo"/>
        <x-stat-card label="Jadwal Hari Ini" :value="$jadwalHariIni->count()" icon="clock" color="amber"/>
        <x-stat-card label="Total Absensi Terisi" :value="$totalAbsensi" icon="finger-print" color="green"/>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 dark:text-slate-100">Jadwal Mengajar Hari Ini</h3>
            <a href="{{ route('guru.absensi.jadwal') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Lihat semua &rarr;</a>
        </div>

        @if($jadwalHariIni->isEmpty())
            <p class="text-sm text-gray-500 dark:text-slate-400">Tidak ada jadwal mengajar hari ini.</p>
        @else
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @foreach($jadwalHariIni as $jadwal)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-slate-100">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">Kelas {{ $jadwal->kelas }} &middot; {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</div>
                        </div>
                        @if($sudahAbsenHariIni->contains($jadwal->id))
                            <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300 px-3 py-1 text-xs font-medium">Sudah diisi</span>
                        @else
                            <a href="{{ route('guru.absensi.create', $jadwal) }}" class="inline-flex items-center rounded-lg bg-indigo-600 text-white px-3 py-1.5 text-xs font-medium hover:bg-indigo-700 transition">Input Absensi</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@else
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="Hadir" :value="$totalHadir" icon="check" color="green"/>
        <x-stat-card label="Sakit" :value="$totalSakit" icon="heart" color="amber"/>
        <x-stat-card label="Izin" :value="$totalIzin" icon="document" color="indigo"/>
        <x-stat-card label="Alpa" :value="$totalAlpa" icon="x" color="rose"/>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
        <h3 class="font-semibold text-gray-800 dark:text-slate-100 mb-1">Halo, {{ auth()->user()->name }}!</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">
            Kelas <span class="font-medium text-gray-700 dark:text-slate-200">{{ $siswa->kelas ?? '-' }}</span> &middot; NIT {{ $siswa->nit ?? '-' }}
        </p>
        <a href="{{ route('siswa.riwayat') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700 transition">
            <x-heroicon.clock class="w-4 h-4"/> Lihat Riwayat Absensi
        </a>
    </div>
@endif
@endsection
