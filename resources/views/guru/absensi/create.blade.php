@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
<div class="mb-6">
    <a href="{{ route('guru.absensi.jadwal') }}" class="text-sm text-indigo-600 hover:text-indigo-700">&larr; Kembali ke jadwal</a>
    <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100 mt-2">Input Absensi</h2>
    <p class="text-sm text-gray-500 dark:text-slate-400">
        {{ $jadwal->mataPelajaran->nama_mapel }} &middot; Kelas {{ $jadwal->kelas }}
        &middot; {{ ucfirst($jadwal->hari) }} {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
    </p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 p-6">
    <form method="POST" action="{{ route('guru.absensi.store', $jadwal) }}">
        @csrf

        <div class="max-w-xs mb-6">
            <x-input name="tanggal" label="Tanggal Absensi" type="date" :value="old('tanggal', $existing->isNotEmpty() ? $existing->first()->tanggal->format('Y-m-d') : now()->format('Y-m-d'))" required/>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">NIT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Nama Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($siswa as $s)
                        @php
                            $existingRecord = $existing->get($s->id);
                            $oldStatus = old("status.{$s->id}");
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 text-gray-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $s->nit }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-slate-100">{{ $s->user->name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $value => $label)
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="status[{{ $s->id }}]" value="{{ $value }}"
                                                class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                @checked(($oldStatus ?? $existingRecord->status ?? 'hadir') === $value)>
                                            <span class="text-sm text-gray-600 dark:text-slate-300">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="keterangan[{{ $s->id }}]" value="{{ old("keterangan.{$s->id}", $existingRecord->keterangan ?? '') }}"
                                    placeholder="Opsional" class="w-full rounded-md border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">Tidak ada siswa di kelas {{ $jadwal->kelas }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">Simpan Absensi</button>
            <a href="{{ route('guru.absensi.jadwal') }}" class="rounded-lg border border-gray-300 dark:border-slate-600 px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
