<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $data = match ($user->role) {
            'admin' => $this->adminData(),
            'guru' => $this->guruData(),
            default => $this->siswaData(),
        };

        return response()->json(['data' => $data]);
    }

    private function adminData(): array
    {
        return [
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
            'total_mapel' => MataPelajaran::count(),
            'total_jadwal' => Jadwal::count(),
            'absensi_hari_ini' => Absensi::whereDate('tanggal', today())->count(),
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
            'total_jadwal' => $guru ? $guru->jadwal()->count() : 0,
            'total_absensi' => $guru
                ? Absensi::whereIn('jadwal_id', $guru->jadwal()->pluck('jadwal.id'))->count()
                : 0,
            'jadwal_hari_ini' => $jadwalHariIni->map(fn (Jadwal $j) => $this->jadwalPayload($j)),
            'sudah_absen_id' => $sudahAbsenHariIni->values(),
        ];
    }

    private function siswaData(): array
    {
        $siswa = Auth::user()->siswa;

        $absensi = $siswa
            ? Absensi::where('siswa_id', $siswa->id)->get()
            : collect();

        return [
            'siswa' => $siswa ? [
                'id' => $siswa->id,
                'nit' => $siswa->nit,
                'nisn' => $siswa->nisn,
                'kelas' => $siswa->kelas,
                'nama' => $siswa->user->name,
                'email' => $siswa->user->email,
            ] : null,
            'total_hadir' => $absensi->where('status', 'hadir')->count(),
            'total_sakit' => $absensi->where('status', 'sakit')->count(),
            'total_izin' => $absensi->where('status', 'izin')->count(),
            'total_alpa' => $absensi->where('status', 'alpa')->count(),
        ];
    }

    private function jadwalPayload(Jadwal $jadwal): array
    {
        return [
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
