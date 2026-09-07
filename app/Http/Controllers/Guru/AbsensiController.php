<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function jadwal(): View
    {
        $guru = Auth::user()->guru;

        $jadwal = $guru
            ? $guru->jadwal()->with('mataPelajaran')->orderBy('hari')->orderBy('jam_mulai')->get()
            : collect();

        $sudahAbsen = Absensi::whereIn('jadwal_id', $jadwal->pluck('id'))
            ->whereDate('tanggal', today())
            ->distinct()
            ->pluck('jadwal_id');

        return view('guru.absensi.jadwal', compact('jadwal', 'sudahAbsen'));
    }

    public function create(Jadwal $jadwal): View
    {
        $this->ensureMilik($jadwal);

        $siswa = Siswa::with('user')
            ->where('kelas', $jadwal->kelas)
            ->orderBy('nit')
            ->get();

        $existing = Absensi::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', today())
            ->get()
            ->keyBy('siswa_id');

        return view('guru.absensi.create', compact('jadwal', 'siswa', 'existing'));
    }

    public function store(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $this->ensureMilik($jadwal);

        $request->validate([
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['nullable', 'in:hadir,izin,sakit,alpa'],
        ]);

        $kelasSiswa = Siswa::where('kelas', $jadwal->kelas)->pluck('id');

        foreach ($request->input('status') as $siswaId => $status) {
            if (! $status || ! $kelasSiswa->contains($siswaId)) {
                continue;
            }

            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $request->date('tanggal'),
                ],
                [
                    'status' => $status,
                    'keterangan' => $request->input("keterangan.{$siswaId}"),
                ]
            );
        }

        return redirect()->route('guru.absensi.jadwal')
            ->with('success', 'Absensi berhasil disimpan.');
    }

    public function riwayat(Request $request): View
    {
        $guru = Auth::user()->guru;

        $absensi = Absensi::with(['siswa.user', 'jadwal.mataPelajaran'])
            ->whereIn('jadwal_id', $guru ? $guru->jadwal()->pluck('jadwal.id') : [])
            ->when($request->input('mapel'), function ($q, $mapel) {
                $q->whereHas('jadwal', fn ($j) => $j->where('mata_pelajaran_id', $mapel));
            })
            ->when($request->input('dari'), fn ($q, $d) => $q->whereDate('tanggal', '>=', $d))
            ->when($request->input('sampai'), fn ($q, $s) => $q->whereDate('tanggal', '<=', $s))
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        $mapel = $guru->jadwal()->with('mataPelajaran')->get()->map(fn ($j) => $j->mataPelajaran)->unique('id');

        return view('guru.absensi.riwayat', compact('absensi', 'mapel'));
    }

    private function ensureMilik(Jadwal $jadwal): void
    {
        $guru = Auth::user()->guru;

        abort_unless($guru && $jadwal->guru_id === $guru->id, 403, 'Anda tidak mengampu jadwal ini.');
    }
}
