<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\MataPelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $mapels = MataPelajaran::orderBy('nama_mapel')->get(['id', 'nama_mapel']);

        $kelasList = Absensi::query()
            ->join('siswa', 'siswa.id', '=', 'absensi.siswa_id')
            ->distinct()
            ->orderBy('siswa.kelas')
            ->pluck('siswa.kelas');

        $query = Absensi::query()
            ->with(['siswa.user', 'jadwal.mataPelajaran'])
            ->when($request->input('mapel'), function ($q, $mapel) {
                $q->whereHas('jadwal', fn ($j) => $j->where('mata_pelajaran_id', $mapel));
            })
            ->when($request->input('kelas'), function ($q, $kelas) {
                $q->whereHas('siswa', fn ($s) => $s->where('kelas', $kelas));
            })
            ->when($request->input('dari'), fn ($q, $d) => $q->whereDate('tanggal', '>=', $d))
            ->when($request->input('sampai'), fn ($q, $s) => $q->whereDate('tanggal', '<=', $s))
            ->get();

        $rows = $query->groupBy(fn (Absensi $a) => $a->siswa_id.'-'.$a->jadwal_id)
            ->values()
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'siswa_id' => $first->siswa_id,
                    'nama' => $first->siswa->user->name,
                    'nit' => $first->siswa->nit,
                    'kelas' => $first->siswa->kelas,
                    'mapel' => $first->jadwal->mataPelajaran?->nama_mapel,
                    'jadwal_id' => $first->jadwal_id,
                    'hadir' => $items->where('status', 'hadir')->count(),
                    'sakit' => $items->where('status', 'sakit')->count(),
                    'izin' => $items->where('status', 'izin')->count(),
                    'alpa' => $items->where('status', 'alpa')->count(),
                    'total' => $items->count(),
                ];
            });

        return response()->json([
            'data' => $rows,
            'filters' => [
                'mapel' => $mapels,
                'kelas' => $kelasList->values(),
            ],
        ]);
    }
}
