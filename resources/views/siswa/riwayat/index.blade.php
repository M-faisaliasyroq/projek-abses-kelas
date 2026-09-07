@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Riwayat Absensi Saya</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">{{ $siswa->user->name }} &middot; Kelas {{ $siswa->kelas }}</p>
</div>

@php
    $total = $stats->total ?? 0;
    $hadir = $stats->hadir ?? 0;
    $persen = $total > 0 ? round(($hadir / $total) * 100) : 0;
@endphp

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <x-stat-card label="Hadir" :value="$hadir" icon="check" color="green"/>
    <x-stat-card label="Sakit" :value="$stats->sakit ?? 0" icon="heart" color="amber"/>
    <x-stat-card label="Izin" :value="$stats->izin ?? 0" icon="document" color="indigo"/>
    <x-stat-card label="Alpa" :value="$stats->alpa ?? 0" icon="x" color="rose"/>
    <x-stat-card label="Kehadiran" value="{{ $persen }}%" icon="chart-bar" color="indigo"/>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-5 mb-6">
    <form method="GET" action="{{ route('siswa.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <x-select name="mapel" label="Mata Pelajaran" :options="$mapelList->pluck('nama_mapel', 'id')" :selected="request('mapel')" placeholder="Semua mapel"/>
        <x-select name="status" label="Status" :options="['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa']" :selected="request('status')" placeholder="Semua status"/>
        <x-select name="bulan" label="Bulan" :options="[1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember']" :selected="request('bulan')" placeholder="Semua bulan"/>
        <div class="flex items-end gap-2 sm:col-span-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">Filter</button>
            <a href="{{ route('siswa.riwayat') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Mata Pelajaran</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Guru</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Jam</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($absensi as $a)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $a->tanggal->translatedFormat('l, d M Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-slate-100">{{ $a->jadwal->mataPelajaran->nama_mapel }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $a->jadwal->guru?->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ substr($a->jadwal->jam_mulai, 0, 5) }} - {{ substr($a->jadwal->jam_selesai, 0, 5) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $a->status === 'hadir' ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300' : '' }}
                                {{ $a->status === 'sakit' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' : '' }}
                                {{ $a->status === 'izin' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300' : '' }}
                                {{ $a->status === 'alpa' ? 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300' : '' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-slate-400">{{ $a->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">Belum ada riwayat absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">
        {{ $absensi->links() }}
    </div>
</div>
@endsection
