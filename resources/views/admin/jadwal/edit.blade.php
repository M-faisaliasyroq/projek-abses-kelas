@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Edit Jadwal</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">{{ $jadwal->kelas }} &middot; {{ $jadwal->mataPelajaran->nama_mapel }}</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.jadwal.update', $jadwal) }}">
        @csrf
        @method('PUT')

        <x-select name="guru_id" label="Guru" :options="$guru->mapWithKeys(fn($g) => [$g->id => $g->user->name])" :selected="$jadwal->guru_id" required/>
        <x-select name="mata_pelajaran_id" label="Mata Pelajaran" :options="$mapel->mapWithKeys(fn($m) => [$m->id => $m->kode_mapel.' - '.$m->nama_mapel])" :selected="$jadwal->mata_pelajaran_id" required/>
        <x-select name="kelas" label="Kelas" :options="['X-RPL' => 'X-RPL', 'XI-RPL' => 'XI-RPL', 'XII-RPL' => 'XII-RPL']" :selected="$jadwal->kelas" required/>

        <x-select name="hari" label="Hari" :options="['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat']" :selected="$jadwal->hari" required/>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
            <x-input name="jam_mulai" label="Jam Mulai" type="time" :value="substr($jadwal->jam_mulai, 0, 5)" required/>
            <x-input name="jam_selesai" label="Jam Selesai" type="time" :value="substr($jadwal->jam_selesai, 0, 5)" required/>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">Simpan Perubahan</button>
            <a href="{{ route('admin.jadwal.index') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-5 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
