<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->clearData();
        $this->seedUsers();
        $this->seedMataPelajaran();
        $this->seedGuru();
        $this->seedSiswa();
        $this->seedJadwal();
        $this->seedAbsensi();
    }

    private function clearData(): void
    {
        Absensi::query()->delete();
        Jadwal::query()->delete();
        Siswa::query()->delete();
        Guru::query()->delete();
        MataPelajaran::query()->delete();
        User::where('role', '!=', 'admin')->delete();
        User::where('role', 'admin')->delete();
    }

    private function seedUsers(): void
    {
        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@smkrpl.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }

    private function seedMataPelajaran(): void
    {
        $mapelList = [
            ['RPL-01', 'Pemrograman Web', 'Belajar HTML, CSS, JavaScript, dan PHP'],
            ['RPL-02', 'Basis Data', 'Belajar SQL, MySQL, dan manajemen database'],
            ['RPL-03', 'Pemrograman Berorientasi Objek', 'Belajar Java dan konsep OOP'],
            ['RPL-04', 'Pemrograman Mobile', 'Belajar Flutter dan pengembangan aplikasi Android'],
            ['RPL-05', 'Jaringan Komputer', 'Belajar dasar-dasar jaringan dan konfigurasi'],
            ['RPL-06', 'Desain Grafis', 'Belajar UI/UX dan desain antarmuka aplikasi'],
        ];

        foreach ($mapelList as [$kode, $nama, $deskripsi]) {
            MataPelajaran::create([
                'kode_mapel' => $kode,
                'nama_mapel' => $nama,
                'deskripsi' => $deskripsi,
            ]);
        }
    }

    private function seedGuru(): void
    {
        $guruList = [
            ['Budi Santoso, S.Kom', 'budi@smkrpl.test', 'NI P001', 'Pemrograman Web'],
            ['Siti Rahayu, M.Kom', 'siti@smkrpl.test', 'NI P002', 'Basis Data'],
            ['Andi Wijaya, S.T', 'andi@smkrpl.test', 'NI P003', 'Pemrograman Berorientasi Objek'],
            ['Dewi Lestari, S.Kom', 'dewi@smkrpl.test', 'NI P004', 'Pemrograman Mobile'],
            ['Rudi Hartono, S.T', 'rudi@smkrpl.test', 'NI P005', 'Jaringan Komputer'],
            ['Nina Marlina, S.Ds', 'nina@smkrpl.test', 'NI P006', 'Desain Grafis'],
        ];

        foreach ($guruList as [$name, $email, $nip, $mapel]) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => 'password',
                'role' => 'guru',
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => $nip,
                'alamat' => null,
                'no_telp' => null,
            ]);
        }
    }

    private function seedSiswa(): void
    {
        $siswaList = [
            ['AISYAH NUR AZIZAH', '0089144490', '2425280', 'aisyah.azizah@gmail.com'],
            ['ANDIKA TEGAR PRIBADI', '0083507249', '2425281', 'andika.pribadi@gmail.com'],
            ['ANDRA AHMAD SYAIDIN', '0096304543', '2425282', 'andra.syaidin@gmail.com'],
            ['ANDRA RAMADHAN', '0095766658', '2425283', 'andra.ramadhan@gmail.com'],
            ['ANGGA IMAM ALFAHRI', '0098885414', '2425284', 'angga.alfahri@gmail.com'],
            ['AZIZAH NUR ROHMAH', '0089943893', '2425285', 'azizah.rohmah@gmail.com'],
            ['BILKIS ANGSIANO SAPUTRA', '0086151009', '2425286', 'bilkis.saputra@gmail.com'],
            ['DEA ANANDA PUTRI', '0095122025', '2425288', 'dea.putri@gmail.com'],
            ['DIKA ANGGARA', '0081218638', '2425289', 'dika.anggara@gmail.com'],
            ['EZY MUHAMAD IKBAL', '0076957561', '2425291', 'ezy.ikbal@gmail.com'],
            ['FATHIR AZHAR SYAHPUTRA', '0099770769', '2425292', 'fathir.syahputra@gmail.com'],
            ['FRENKY JULLYAN SUKMANA', '0098207238', '2425293', 'frenky.sukmana@gmail.com'],
            ['GALANG AGUNG MUNGGARAN', '0098076197', '2425294', 'galang.munggaran@gmail.com'],
            ['HAGYA MUTIARA ASSIDIQI', '0083772153', '2425295', 'hagya.assidiqi@gmail.com'],
            ['HARYANTO', '0086881909', '2425296', 'haryanto@gmail.com'],
            ['KEYLA CAESAR KALIS', '0089483659', '2425297', 'keyla.kalis@gmail.com'],
            ['KEYLA OILIVIA WAHYU', '0097249732', '2425298', 'keyla.wahyu@gmail.com'],
            ['KEYMAL WIJAYA', '0085715652', '2425299', 'keymal.wijaya@gmail.com'],
            ['KIBBY KAWENA KAZAN', '0096749975', '2425300', 'kibby.kazan@gmail.com'],
            ['LAYSHA KAMILA PUTRI', '0092433044', '2425301', 'laysha.putri@gmail.com'],
            ['LUCKY LORILASARUS YARONA', null, '2425302', 'lucky.yarona@gmail.com'],
            ['M FAIS ALI ASYROQ', '0088565270', '2425303', 'fais.asyroq@gmail.com'],
            ['M. NATHAN JATNIKA', '0103402343', '2425305', 'nathan.jatnika@gmail.com'],
            ['MUHAMMAD ALIF FAKHRI ZAIN', '0097178636', '2425307', 'alif.zain@gmail.com'],
            ['NURHASANAH', '0098156724', '2425308', 'nurhasanah@gmail.com'],
            ['RIANA NUR\'AENI', '0091529189', '2425309', 'riana.nuraeni@gmail.com'],
            ['RISDAYATI', '0098532609', '2425310', 'risdayati@gmail.com'],
            ['RIZKY LARENDRA', '0087817058', '2425311', 'rizky.larendra@gmail.com'],
            ['VIO ZERGI SELJA', '0098735005', '2425313', 'vio.selja@gmail.com'],
            ['WAHYU YOGA SATRIA', '0092693837', '2425314', 'wahyu.satria@gmail.com'],
            ['WILDI MADYA PAKSI', '0091228545', '2425315', 'wildi.paksi@gmail.com'],
            ['ZICKY ROMANSYAH', '0086608901', '2425316', 'zicky.romansyah@gmail.com'],
        ];

        foreach ($siswaList as $i => [$nama, $nisn, $nit, $email]) {
            $no = $i + 1;

            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => 'password',
                'role' => 'siswa',
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'nit' => $nit,
                'nisn' => $nisn,
                'kelas' => 'XI-RPL',
                'alamat' => 'Jl. Contoh No. '.$no,
                'no_telp' => '08'.rand(1000000000, 9999999999),
            ]);
        }
    }

    private function seedJadwal(): void
    {
        $guruIds = Guru::pluck('id')->toArray();
        $mapelIds = MataPelajaran::pluck('id')->toArray();

        $jadwalList = [
            ['senin', '07:00', '08:40', 0, 0],
            ['senin', '10:20', '12:00', 1, 1],
            ['selasa', '07:00', '08:40', 2, 2],
            ['selasa', '10:20', '12:00', 3, 3],
            ['rabu', '07:00', '08:40', 4, 4],
            ['rabu', '08:40', '10:20', 5, 5],
            ['kamis', '07:00', '08:40', 0, 2],
            ['kamis', '10:20', '12:00', 1, 3],
            ['jumat', '07:00', '08:40', 2, 4],
            ['jumat', '08:40', '10:20', 3, 5],
        ];

        foreach ($jadwalList as [$hari, $mulai, $selesai, $guruIdx, $mapelIdx]) {
            Jadwal::create([
                'guru_id' => $guruIds[$guruIdx],
                'mata_pelajaran_id' => $mapelIds[$mapelIdx],
                'kelas' => 'XI-RPL',
                'hari' => $hari,
                'jam_mulai' => $mulai,
                'jam_selesai' => $selesai,
            ]);
        }
    }

    private function seedAbsensi(): void
    {
        $siswaByKelas = Siswa::all()->groupBy('kelas');

        foreach (Jadwal::all() as $jadwal) {
            if (! $siswaByKelas->has($jadwal->kelas)) {
                continue;
            }

            $siswas = $siswaByKelas->get($jadwal->kelas);
            $tanggalHari = $this->hariToTanggal($jadwal->hari);

            foreach ($siswas as $siswa) {
                $random = rand(0, 100);
                $status = 'hadir';

                if ($random < 5) {
                    $status = 'sakit';
                } elseif ($random < 10) {
                    $status = 'izin';
                } elseif ($random < 15) {
                    $status = 'alpa';
                }

                Absensi::create([
                    'siswa_id' => $siswa->id,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $tanggalHari,
                    'status' => $status,
                    'keterangan' => $status === 'hadir' ? null : 'Catatan '.$status,
                ]);
            }
        }
    }

    private function hariToTanggal(string $hari): string
    {
        $index = [
            'senin' => 0,
            'selasa' => 1,
            'rabu' => 2,
            'kamis' => 3,
            'jumat' => 4,
        ];

        return now()->startOfWeek()->addDays($index[$hari])->toDateString();
    }
}
