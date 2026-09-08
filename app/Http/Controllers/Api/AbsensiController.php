<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function jadwal(): JsonResponse
    {
        $guru = Auth::user()->guru;

        $jadwal = $guru
            ? $guru->jadwal()->with('mataPelajaran')->orderBy('hari')->orderBy('jam_mulai')->get()
            : collect();

        $sudahAbsen = Absensi::whereIn('jadwal_id', $jadwal->pluck('id'))
            ->whereDate('tanggal', today())
            ->distinct()
            ->pluck('jadwal_id');

        return response()->json([
            'data' => $jadwal->map(function (Jadwal $j) use ($sudahAbsen) {
                return [
                    'id' => $j->id,
                    'kelas' => $j->kelas,
                    'hari' => $j->hari,
                    'jam_mulai' => $j->jam_mulai,
                    'jam_selesai' => $j->jam_selesai,
                    'mapel' => $j->mataPelajaran ? [
                        'id' => $j->mataPelajaran->id,
                        'kode_mapel' => $j->mataPelajaran->kode_mapel,
                        'nama_mapel' => $j->mataPelajaran->nama_mapel,
                    ] : null,
                    'sudah_absen' => $sudahAbsen->contains($j->id),
                ];
            }),
        ]);
    }

    public function create(Jadwal $jadwal): JsonResponse
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

        return response()->json([
            'data' => [
                'jadwal' => [
                    'id' => $jadwal->id,
                    'kelas' => $jadwal->kelas,
                    'hari' => $jadwal->hari,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'mapel' => $jadwal->mataPelajaran ? [
                        'id' => $jadwal->mataPelajaran->id,
                        'kode_mapel' => $jadwal->mataPelajaran->kode_mapel,
                        'nama_mapel' => $jadwal->mataPelajaran->nama_mapel,
                    ] : null,
                    'tanggal_hari_ini' => today()->toDateString(),
                ],
                'siswa' => $siswa->map(fn (Siswa $s) => [
                    'id' => $s->id,
                    'nit' => $s->nit,
                    'name' => $s->user->name,
                    'status' => $existing->has($s->id) ? $existing->get($s->id)->status : null,
                    'keterangan' => $existing->has($s->id) ? $existing->get($s->id)->keterangan : null,
                ]),
            ],
        ]);
    }

    public function store(Request $request, Jadwal $jadwal): JsonResponse
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

        return response()->json(['message' => 'Absensi berhasil disimpan.']);
    }

    public function riwayat(Request $request): JsonResponse
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
            ->paginate(15);

        $mapel = $guru ? $guru->jadwal()->with('mataPelajaran')->get()->map(fn ($j) => $j->mataPelajaran)->unique('id')->values() : collect();

        return response()->json([
            'data' => $absensi->map(fn (Absensi $a) => [
                'id' => $a->id,
                'tanggal' => $a->tanggal->toDateString(),
                'status' => $a->status,
                'keterangan' => $a->keterangan,
                'siswa' => [
                    'id' => $a->siswa->id,
                    'nit' => $a->siswa->nit,
                    'name' => $a->siswa->user->name,
                ],
                'jadwal' => [
                    'id' => $a->jadwal->id,
                    'jam_mulai' => $a->jadwal->jam_mulai,
                    'jam_selesai' => $a->jadwal->jam_selesai,
                    'mapel' => $a->jadwal->mataPelajaran ? $a->jadwal->mataPelajaran->nama_mapel : null,
                ],
            ]),
            'pagination' => [
                'current_page' => $absensi->currentPage(),
                'last_page' => $absensi->lastPage(),
                'per_page' => $absensi->perPage(),
                'total' => $absensi->total(),
            ],
            'mapel' => $mapel->map(fn ($m) => [
                'id' => $m->id,
                'nama_mapel' => $m->nama_mapel,
            ]),
        ]);
    }

    private function ensureMilik(Jadwal $jadwal): void
    {
        $guru = Auth::user()->guru;

        abort_unless($guru && $jadwal->guru_id === $guru->id, 403, 'Anda tidak mengampu jadwal ini.');
    }
}
