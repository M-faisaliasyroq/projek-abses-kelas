@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Edit Siswa</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">{{ $siswa->user->name }} &middot; NIT {{ $siswa->nit }}</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}">
        @csrf
        @method('PUT')

        <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">Akun Login</h3>
        <x-input name="name" label="Nama Lengkap" required :value="$siswa->user->name"/>
        <x-input name="email" label="Email" type="email" required :value="$siswa->user->email"/>
        <x-input name="password" label="Password Baru (kosongkan jika tidak diganti)" type="password" placeholder="Minimal 6 karakter"/>

        <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mt-6 mb-4 pb-3 border-b border-gray-100 dark:border-slate-700">Data Sekolah</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
            <x-input name="nit" label="NIT" required :value="$siswa->nit"/>
            <x-input name="nisn" label="NISN (kosongkan jika tidak ada)" :value="$siswa->nisn"/>
        </div>
        <x-select name="kelas" label="Kelas" :options="['X-RPL' => 'X-RPL', 'XI-RPL' => 'XI-RPL', 'XII-RPL' => 'XII-RPL']" :selected="$siswa->kelas" required/>
        <x-textarea name="alamat" label="Alamat" rows="2" :value="$siswa->alamat"/>
        <x-input name="no_telp" label="No. Telepon" :value="$siswa->no_telp"/>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">Simpan Perubahan</button>
            <a href="{{ route('admin.siswa.index') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
