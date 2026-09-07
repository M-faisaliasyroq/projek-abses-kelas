<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        $kelasList = Absensi::query()
            ->join('siswa', 'siswa.id', '=', 'absensi.siswa_id')
            ->distinct()
            ->orderBy('siswa.kelas')
            ->pluck('siswa.kelas');

        $filterMapel = $request->input('mapel');
        $filterKelas = $request->input('kelas');
        $filterDari = $request->input('dari');
        $filterSampai = $request->input('sampai');

        $rows = collect();

        $query = Absensi::query()
            ->with(['siswa.user', 'jadwal.mataPelajaran'])
            ->when($filterMapel, function ($q, $mapel) {
                $q->whereHas('jadwal', fn ($j) => $j->where('mata_pelajaran_id', $mapel));
            })
            ->when($filterKelas, function ($q, $kelas) {
                $q->whereHas('siswa', fn ($s) => $s->where('kelas', $kelas));
            })
            ->when($filterDari, function ($q, $dari) {
                $q->whereDate('tanggal', '>=', $dari);
            })
            ->when($filterSampai, function ($q, $sampai) {
                $q->whereDate('tanggal', '<=', $sampai);
            })
            ->get();

        $rows = $query->groupBy(function (Absensi $a) {
            return $a->siswa_id.'-'.$a->jadwal_id;
        })->map(function ($items) {
            $first = $items->first();

            return [
                'siswa' => $first->siswa,
                'mapel' => $first->jadwal->mataPelajaran,
                'jadwal' => $first->jadwal,
                'hadir' => $items->where('status', 'hadir')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'alpa' => $items->where('status', 'alpa')->count(),
                'total' => $items->count(),
            ];
        })->values();

        return view('admin.laporan.index', compact('mapels', 'kelasList', 'rows', 'filterMapel', 'filterKelas', 'filterDari', 'filterSampai'));
    }
}
