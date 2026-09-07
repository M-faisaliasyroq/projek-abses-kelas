@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Tambah Siswa</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">Lengkapi data diri siswa baru</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.siswa.store') }}">
        @csrf

        <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">Akun Login</h3>
        <x-input name="name" label="Nama Lengkap" required placeholder="Contoh: Ahmad Pratama"/>
        <x-input name="email" label="Email" type="email" required placeholder="nama@smkrpl.test"/>
        <x-input name="password" label="Password" type="password" required placeholder="Minimal 6 karakter"/>

        <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mt-6 mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">Data Sekolah</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
            <x-input name="nit" label="NIT" required placeholder="2425280"/>
            <x-input name="nisn" label="NISN (kosongkan jika tidak ada)" placeholder="008..."/>
        </div>
        <x-select name="kelas" label="Kelas" :options="['X-RPL' => 'X-RPL', 'XI-RPL' => 'XI-RPL', 'XII-RPL' => 'XII-RPL']" placeholder="Pilih kelas" required/>
        <x-textarea name="alamat" label="Alamat" rows="2"/>
        <x-input name="no_telp" label="No. Telepon" placeholder="08xxxxxxxxxx"/>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">Simpan</button>
            <a href="{{ route('admin.siswa.index') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
