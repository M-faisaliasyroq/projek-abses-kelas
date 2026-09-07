@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Input Absensi</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400">Pilih jadwal mengajar untuk mengisi absensi</p>
    </div>
    <div class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 px-3 py-2 text-sm text-gray-600 dark:text-slate-300">
        Tanggal hari ini: <span class="font-medium text-gray-800 dark:text-slate-100">{{ now()->translatedFormat('l, d F Y') }}</span>
    </div>
</div>

@php
    $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach($days as $day)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 dark:text-slate-100 capitalize">{{ $day }}</h3>
                @if($day === strtolower(now()->translatedFormat('l')))
                    <span class="inline-flex rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 px-2.5 py-0.5 text-xs font-medium">Hari ini</span>
                @endif
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @php
                    $today = $jadwal->where('hari', $day);
                @endphp
                @forelse($today as $j)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-medium text-gray-800 dark:text-slate-100 truncate">{{ $j->mataPelajaran->nama_mapel }}</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">Kelas {{ $j->kelas }} &middot; {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</div>
                        </div>
                        @if($sudahAbsen->contains($j->id))
                            <span class="shrink-0 inline-flex items-center rounded-full bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300 px-3 py-1 text-xs font-medium">Selesai</span>
                        @else
                            <a href="{{ route('guru.absensi.create', $j) }}" class="shrink-0 inline-flex items-center rounded-lg bg-indigo-600 text-white px-3 py-1.5 text-xs font-medium hover:bg-indigo-700 transition">Input</a>
                        @endif
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-gray-400 dark:text-slate-500">Tidak ada jadwal.</p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection
