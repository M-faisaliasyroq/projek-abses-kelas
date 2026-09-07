@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Mata Pelajaran</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400">Kelola mata pelajaran RPL</p>
    </div>
    <a href="{{ route('admin.mapel.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
        + Tambah Mapel
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-slate-700">
        <form method="GET" action="{{ route('admin.mapel.index') }}" class="flex gap-2">
            <input type="text" name="cari" value="{{ $search }}" placeholder="Cari kode atau nama mapel..."
                class="flex-1 rounded-lg border-gray-300 dark:border-slate-600 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Nama Mapel</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Deskripsi</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($mapel as $m)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-gray-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300 px-2.5 py-0.5 text-xs font-medium">{{ $m->kode_mapel }}</span></td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-slate-100">{{ $m->nama_mapel }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300 max-w-xs truncate">{{ $m->deskripsi ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.mapel.edit', $m) }}" class="rounded-md bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300 px-2.5 py-1 text-xs font-medium hover:bg-amber-100 dark:hover:bg-amber-500/25 transition">Edit</a>
                                <form method="POST" action="{{ route('admin.mapel.destroy', $m) }}" onsubmit="return confirm('Hapus mapel {{ addslashes($m->nama_mapel) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-300 px-2.5 py-1 text-xs font-medium hover:bg-red-100 dark:hover:bg-red-500/25 transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">Belum ada mata pelajaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">
        {{ $mapel->links() }}
    </div>
</div>
@endsection
