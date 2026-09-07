<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(Request $request): View
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
            ->paginate(15)
            ->withQueryString();

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
            ->unique('id');

        return view('siswa.riwayat.index', compact('siswa', 'absensi', 'stats', 'mapelList'));
    }
}
