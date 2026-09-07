@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Riwayat Absensi</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">Riwayat absensi yang Anda isi</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-5 mb-6">
    <form method="GET" action="{{ route('guru.absensi.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <x-select name="mapel" label="Mata Pelajaran" :options="$mapel->mapWithKeys(fn($m) => [$m->id => $m->nama_mapel])" :selected="request('mapel')" placeholder="Semua mapel"/>
        <x-input name="dari" label="Dari Tanggal" type="date" :value="request('dari')"/>
        <x-input name="sampai" label="Sampai Tanggal" type="date" :value="request('sampai')"/>
        <div class="flex items-end gap-2 sm:col-span-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">Filter</button>
            <a href="{{ route('guru.absensi.riwayat') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Mapel</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Kelas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Siswa</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">NIT</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($absensi as $a)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $a->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-slate-100">{{ $a->jadwal->mataPelajaran->nama_mapel }}</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 px-2.5 py-0.5 text-xs font-medium">{{ $a->jadwal->kelas }}</span></td>
                        <td class="px-4 py-3 text-gray-800 dark:text-slate-100">{{ $a->siswa->user->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $a->siswa->nit }}</td>
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
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">Belum ada data absensi.</td>
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
