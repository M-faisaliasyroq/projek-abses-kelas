<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(): View
    {
        $jadwal = Jadwal::with(['guru.user', 'mataPelajaran'])
            ->orderBy('kelas')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(15);

        return view('admin.jadwal.index', compact('jadwal'));
    }

    public function create(): View
    {
        $guru = Guru::with('user')->orderBy('id')->get();
        $mapel = MataPelajaran::orderBy('kode_mapel')->get();

        return view('admin.jadwal.create', compact('guru', 'mapel'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:guru,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'kelas' => ['required', 'string', 'max:15'],
            'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        Jadwal::create($data);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal): View
    {
        $guru = Guru::with('user')->orderBy('id')->get();
        $mapel = MataPelajaran::orderBy('kode_mapel')->get();

        return view('admin.jadwal.edit', compact('jadwal', 'guru', 'mapel'));
    }

    public function update(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:guru,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'kelas' => ['required', 'string', 'max:15'],
            'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $jadwal->update($data);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
