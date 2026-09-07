@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Profil Saya</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">Informasi data diri Anda sebagai siswa</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden max-w-2xl">
    <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-10 text center">
        <div class="flex flex-col items-center">
            <div class="w-20 h-20 rounded-full bg-white/20 text-white flex items-center justify-center text-3xl font-bold mb-3">
                {{ strtoupper(substr($siswa->user->name, 0, 1)) }}
            </div>
            <div class="text-white text-lg font-semibold">{{ $siswa->user->name }}</div>
            <div class="text-indigo-100 text-sm">Kelas {{ $siswa->kelas }} &middot; NIT {{ $siswa->nit }}</div>
        </div>
    </div>

    <div class="divide-y divide-gray-100 dark:divide-slate-700">
        <div class="flex px-6 py-4">
            <div class="w-40 text-sm text-gray-500 dark:text-slate-400 shrink-0">Email</div>
            <div class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ $siswa->user->email }}</div>
        </div>
        <div class="flex px-6 py-4">
            <div class="w-40 text-sm text-gray-500 dark:text-slate-400 shrink-0">NIT</div>
            <div class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ $siswa->nit }}</div>
        </div>
        <div class="flex px-6 py-4">
            <div class="w-40 text-sm text-gray-500 dark:text-slate-400 shrink-0">NISN</div>
            <div class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ $siswa->nisn ?? '-' }}</div>
        </div>
        <div class="flex px-6 py-4">
            <div class="w-40 text-sm text-gray-500 dark:text-slate-400 shrink-0">Kelas</div>
            <div class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ $siswa->kelas }}</div>
        </div>
        <div class="flex px-6 py-4">
            <div class="w-40 text-sm text-gray-500 dark:text-slate-400 shrink-0">Alamat</div>
            <div class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ $siswa->alamat ?? '-' }}</div>
        </div>
        <div class="flex px-6 py-4">
            <div class="w-40 text-sm text-gray-500 dark:text-slate-400 shrink-0">No. Telepon</div>
            <div class="text-sm font-medium text-gray-800 dark:text-slate-100">{{ $siswa->no_telp ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection
