@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Laporan Absensi</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">Rekap kehadiran siswa per mata pelajaran</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-5 mb-6">
    <form method="GET" action="{{ route('admin.laporan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <x-select name="mapel" label="Mata Pelajaran" :options="$mapels->pluck('nama_mapel', 'id')" :selected="$filterMapel" placeholder="Semua mapel"/>
        <x-select name="kelas" label="Kelas" :options="$kelasList->combine($kelasList)" :selected="$filterKelas" placeholder="Semua kelas"/>
        <x-input name="dari" label="Dari Tanggal" type="date" :value="$filterDari"/>
        <x-input name="sampai" label="Sampai Tanggal" type="date" :value="$filterSampai"/>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">Filter</button>
            <a href="{{ route('admin.laporan') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">NIT</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Kelas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Mapel</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 dark:text-green-400 uppercase">Hadir</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase">Sakit</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase">Izin</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-600 dark:text-red-400 uppercase">Alpa</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">% Hadir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-gray-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $row['siswa']->nit }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-slate-100">{{ $row['siswa']->user->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $row['siswa']->kelas }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $row['mapel']->nama_mapel }}</td>
                        <td class="px-4 py-3 text-center text-green-600 dark:text-green-400 font-medium">{{ $row['hadir'] }}</td>
                        <td class="px-4 py-3 text-center text-amber-600 dark:text-amber-400 font-medium">{{ $row['sakit'] }}</td>
                        <td class="px-4 py-3 text-center text-indigo-600 dark:text-indigo-400 font-medium">{{ $row['izin'] }}</td>
                        <td class="px-4 py-3 text-center text-red-600 dark:text-red-400 font-medium">{{ $row['alpa'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-slate-300">{{ $row['total'] }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($row['total'] > 0)
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ ($row['hadir'] / $row['total']) >= 0.8 ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300' : 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300' }}">
                                    {{ round(($row['hadir'] / $row['total']) * 100) }}%
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-slate-500">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            Tidak ada data absensi. Gunakan filter di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
