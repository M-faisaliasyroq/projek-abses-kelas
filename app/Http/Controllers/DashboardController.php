<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $data = match ($user->role) {
            'admin' => $this->adminData(),
            'guru' => $this->guruData(),
            default => $this->siswaData(),
        };

        return view('dashboard', $data);
    }

    private function adminData(): array
    {
        return [
            'totalSiswa' => Siswa::count(),
            'totalGuru' => Guru::count(),
            'totalMapel' => MataPelajaran::count(),
            'totalJadwal' => Jadwal::count(),
            'absensiHariIni' => Absensi::whereDate('tanggal', today())->count(),
        ];
    }

    private function guruData(): array
    {
        $guru = Auth::user()->guru;

        $jadwalHariIni = $guru
            ? $guru->jadwal()->with('mataPelajaran')->where('hari', $this->dayToday())->get()
            : collect();

        $sudahAbsenHariIni = $jadwalHariIni->filter(function (Jadwal $jadwal) {
            return Absensi::where('jadwal_id', $jadwal->id)->whereDate('tanggal', today())->exists();
        })->pluck('id');

        return [
            'totalJadwal' => $guru ? $guru->jadwal()->count() : 0,
            'jadwalHariIni' => $jadwalHariIni,
            'sudahAbsenHariIni' => $sudahAbsenHariIni,
            'totalAbsensi' => $guru
                ? Absensi::whereIn('jadwal_id', $guru->jadwal()->pluck('jadwal.id'))->count()
                : 0,
        ];
    }

    private function siswaData(): array
    {
        $siswa = Auth::user()->siswa;

        $absensi = $siswa
            ? Absensi::where('siswa_id', $siswa->id)->get()
            : collect();

        return [
            'siswa' => $siswa,
            'totalHadir' => $absensi->where('status', 'hadir')->count(),
            'totalSakit' => $absensi->where('status', 'sakit')->count(),
            'totalIzin' => $absensi->where('status', 'izin')->count(),
            'totalAlpa' => $absensi->where('status', 'alpa')->count(),
        ];
    }

    private function dayToday(): string
    {
        $days = [
            'Monday' => 'senin',
            'Tuesday' => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday' => 'kamis',
            'Friday' => 'jumat',
            'Saturday' => 'sabtu',
            'Sunday' => 'minggu',
        ];

        return $days[now()->format('l')];
    }
}
