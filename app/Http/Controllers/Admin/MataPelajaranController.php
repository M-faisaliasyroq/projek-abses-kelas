<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('cari');

        $mapel = MataPelajaran::when($search, function ($query, $search) {
            $query->where('nama_mapel', 'like', "%{$search}%")
                ->orWhere('kode_mapel', 'like', "%{$search}%");
        })
            ->orderBy('kode_mapel')
            ->paginate(10)
            ->withQueryString();

        return view('admin.mapel.index', compact('mapel', 'search'));
    }

    public function create(): View
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:15', 'unique:mata_pelajaran,kode_mapel'],
            'nama_mapel' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        MataPelajaran::create($data);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mapel): View
    {
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, MataPelajaran $mapel): RedirectResponse
    {
        $data = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:15', 'unique:mata_pelajaran,kode_mapel,'.$mapel->id],
            'nama_mapel' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $mapel->update($data);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mapel): RedirectResponse
    {
        $mapel->delete();

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
