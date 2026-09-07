@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Edit Mata Pelajaran</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">{{ $mapel->kode_mapel }} - {{ $mapel->nama_mapel }}</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.mapel.update', $mapel) }}">
        @csrf
        @method('PUT')

        <x-input name="kode_mapel" label="Kode Mapel" required :value="$mapel->kode_mapel"/>
        <x-input name="nama_mapel" label="Nama Mata Pelajaran" required :value="$mapel->nama_mapel"/>
        <x-textarea name="deskripsi" label="Deskripsi" rows="3" :value="$mapel->deskripsi"/>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">Simpan Perubahan</button>
            <a href="{{ route('admin.mapel.index') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
