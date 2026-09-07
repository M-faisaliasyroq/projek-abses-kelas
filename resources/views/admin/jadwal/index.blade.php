@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Jadwal Mengajar</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400">Atur jadwal guru, mapel, dan kelas</p>
    </div>
    <a href="{{ route('admin.jadwal.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
        + Tambah Jadwal
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Kelas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Hari</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Jam</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Mapel</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Guru</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($jadwal as $j)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <td class="px-4 py-3"><span class="inline-flex rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 px-2.5 py-0.5 text-xs font-medium">{{ $j->kelas }}</span></td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-slate-100 capitalize">{{ $j->hari }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-slate-100">{{ $j->mataPelajaran->nama_mapel }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $j->guru->user->name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.jadwal.edit', $j) }}" class="rounded-md bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300 px-2.5 py-1 text-xs font-medium hover:bg-amber-100 dark:hover:bg-amber-500/25 transition">Edit</a>
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $j) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-300 px-2.5 py-1 text-xs font-medium hover:bg-red-100 dark:hover:bg-red-500/25 transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">Belum ada jadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">
        {{ $jadwal->links() }}
    </div>
</div>
@endsection
