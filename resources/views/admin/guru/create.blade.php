@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Tambah Guru</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">Lengkapi data guru baru</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.guru.store') }}">
        @csrf

        <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">Akun Login</h3>
        <x-input name="name" label="Nama Lengkap" required placeholder="Contoh: Budi Santoso, S.Kom"/>
        <x-input name="email" label="Email" type="email" required placeholder="nama@smkrpl.test"/>
        <x-input name="password" label="Password" type="password" required placeholder="Minimal 6 karakter"/>

        <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mt-6 mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">Data Kepegawaian</h3>
        <x-input name="nip" label="NIP" required placeholder="NI P001"/>
        <x-textarea name="alamat" label="Alamat" rows="2"/>
        <x-input name="no_telp" label="No. Telepon" placeholder="08xxxxxxxxxx"/>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">Simpan</button>
            <a href="{{ route('admin.guru.index') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
