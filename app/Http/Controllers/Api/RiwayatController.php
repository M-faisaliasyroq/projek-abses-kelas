<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $siswa = Auth::user()->siswa;

        $absensi = Absensi::with(['jadwal.mataPelajaran', 'jadwal.guru.user'])
            ->where('siswa_id', $siswa->id)
            ->when($request->input('mapel'), function ($q, $mapel) {
                $q->whereHas('jadwal', fn ($j) => $j->where('mata_pelajaran_id', $mapel));
            })
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('bulan'), function ($q, $bulan) {
                $q->whereYear('tanggal', now()->year)
                    ->whereMonth('tanggal', $bulan);
            })
            ->orderByDesc('tanggal')
            ->paginate(15);

        $stats = Absensi::where('siswa_id', $siswa->id)
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status = "hadir" then 1 else 0 end) as hadir')
            ->selectRaw('sum(case when status = "sakit" then 1 else 0 end) as sakit')
            ->selectRaw('sum(case when status = "izin" then 1 else 0 end) as izin')
            ->selectRaw('sum(case when status = "alpa" then 1 else 0 end) as alpa')
            ->first();

        $mapelList = Absensi::with('jadwal.mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->get()
            ->pluck('jadwal.mataPelajaran')
            ->unique('id')
            ->values();

        return response()->json([
            'data' => $absensi->map(fn (Absensi $a) => [
                'id' => $a->id,
                'tanggal' => $a->tanggal->toDateString(),
                'status' => $a->status,
                'keterangan' => $a->keterangan,
                'jadwal' => [
                    'id' => $a->jadwal->id,
                    'jam_mulai' => $a->jadwal->jam_mulai,
                    'jam_selesai' => $a->jadwal->jam_selesai,
                    'mapel' => [
                        'id' => $a->jadwal->mataPelajaran?->id,
                        'nama_mapel' => $a->jadwal->mataPelajaran?->nama_mapel,
                        'kode_mapel' => $a->jadwal->mataPelajaran?->kode_mapel,
                    ],
                    'guru' => [
                        'id' => $a->jadwal->guru?->id,
                        'name' => $a->jadwal->guru?->user->name,
                    ],
                ],
            ]),
            'pagination' => [
                'current_page' => $absensi->currentPage(),
                'last_page' => $absensi->lastPage(),
                'per_page' => $absensi->perPage(),
                'total' => $absensi->total(),
            ],
            'stats' => [
                'total' => (int) $stats?->total,
                'hadir' => (int) $stats?->hadir,
                'sakit' => (int) $stats?->sakit,
                'izin' => (int) $stats?->izin,
                'alpa' => (int) $stats?->alpa,
            ],
            'mapel' => $mapelList->map(fn ($m) => [
                'id' => $m->id,
                'nama_mapel' => $m->nama_mapel,
            ]),
        ]);
    }
}
