@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-slate-100">Data Guru</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400">Kelola data guru pengampu mapel</p>
    </div>
    <a href="{{ route('admin.guru.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
        + Tambah Guru
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-slate-700">
        <form method="GET" action="{{ route('admin.guru.index') }}" class="flex gap-2">
            <input type="text" name="cari" value="{{ $search }}" placeholder="Cari nama, NIP, atau email..."
                class="flex-1 rounded-lg border-gray-300 dark:border-slate-600 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">NIP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">No. Telp</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-300 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($guru as $g)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-gray-500 dark:text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-slate-100">{{ $g->user->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $g->nip }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $g->user->email }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-slate-300">{{ $g->no_telp ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.guru.edit', $g) }}" class="rounded-md bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300 px-2.5 py-1 text-xs font-medium hover:bg-amber-100 dark:hover:bg-amber-500/25 transition">Edit</a>
                                <form method="POST" action="{{ route('admin.guru.destroy', $g) }}" onsubmit="return confirm('Hapus guru {{ addslashes($g->user->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-300 px-2.5 py-1 text-xs font-medium hover:bg-red-100 dark:hover:bg-red-500/25 transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">Belum ada data guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">
        {{ $guru->links() }}
    </div>
</div>
@endsection
