<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function studentOf(string $kelas): Siswa
    {
        return Siswa::with('user')->where('kelas', $kelas)->first();
    }

    private function guruUser(): User
    {
        return Guru::with('user')->first()->user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/siswa')->assertRedirect('/login');
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $this->post('/login', [
            'email' => 'admin@smkrpl.test',
            'password' => 'password',
        ])->assertRedirect('/dashboard');
    }

    public function test_admin_can_access_all_admin_pages(): void
    {
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/siswa')->assertOk();
        $this->actingAs($admin)->get('/admin/siswa/create')->assertOk();
        $this->actingAs($admin)->get('/admin/guru')->assertOk();
        $this->actingAs($admin)->get('/admin/guru/create')->assertOk();
        $this->actingAs($admin)->get('/admin/mapel')->assertOk();
        $this->actingAs($admin)->get('/admin/mapel/create')->assertOk();
        $this->actingAs($admin)->get('/admin/jadwal')->assertOk();
        $this->actingAs($admin)->get('/admin/jadwal/create')->assertOk();
        $this->actingAs($admin)->get('/admin/laporan')->assertOk();
    }

    public function test_role_middleware_blocks_admin_pages_for_guru(): void
    {
        $this->actingAs($this->guruUser())->get('/admin/siswa')->assertForbidden();
    }

    public function test_guru_can_access_guru_pages(): void
    {
        $guru = $this->guruUser();

        $this->actingAs($guru)->get('/dashboard')->assertOk();
        $this->actingAs($guru)->get('/guru/jadwal')->assertOk();
        $this->actingAs($guru)->get('/guru/riwayat')->assertOk();
    }

    public function test_guru_can_open_absensi_form_for_own_jadwal(): void
    {
        $guru = $this->guruUser();
        $jadwal = $guru->guru->jadwal()->first();

        $this->actingAs($guru)->get("/guru/absensi/{$jadwal->id}/create")->assertOk();
    }

    public function test_guru_cannot_open_absensi_form_for_others_jadwal(): void
    {
        $guru = $this->guruUser();
        $other = Jadwal::where('guru_id', '!=', $guru->guru->id)->first();

        $this->actingAs($guru)->get("/guru/absensi/{$other->id}/create")->assertForbidden();
    }

    public function test_guru_can_submit_absensi(): void
    {
        $guru = $this->guruUser();
        $jadwal = $guru->guru->jadwal()->first();
        $siswaKelas = Siswa::where('kelas', $jadwal->kelas)->get();

        $status = [];
        foreach ($siswaKelas as $s) {
            $status[$s->id] = 'hadir';
        }

        $response = $this->actingAs($guru)->post("/guru/absensi/{$jadwal->id}", [
            'tanggal' => today()->toDateString(),
            'status' => $status,
        ]);

        $response->assertRedirect('/guru/jadwal');
        $this->assertTrue(
            Absensi::where('jadwal_id', $jadwal->id)
                ->whereDate('tanggal', today())
                ->where('status', 'hadir')
                ->exists()
        );
    }

    public function test_siswa_can_access_his_pages(): void
    {
        $siswa = $this->studentOf('XI-RPL');

        $this->actingAs($siswa->user)->get('/dashboard')->assertOk();
        $this->actingAs($siswa->user)->get('/siswa/riwayat')->assertOk();
        $this->actingAs($siswa->user)->get('/siswa/profil')->assertOk();
    }

    public function test_siswa_cannot_access_admin_pages(): void
    {
        $siswa = $this->studentOf('XI-RPL');

        $this->actingAs($siswa->user)->get('/admin/mapel')->assertForbidden();
    }

    public function test_admin_can_create_siswa_with_user_account(): void
    {
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)->post('/admin/siswa', [
            'name' => 'Test Siswa',
            'email' => 'test.siswa@smkrpl.test',
            'password' => 'rahasia123',
            'nit' => '2425317',
            'nisn' => '0099999001',
            'kelas' => 'X-RPL',
            'alamat' => 'Jl. Uji',
            'no_telp' => '081234567890',
        ])->assertRedirect(route('admin.siswa.index'));

        $this->assertDatabaseHas('users', ['email' => 'test.siswa@smkrpl.test', 'role' => 'siswa']);
        $this->assertDatabaseHas('siswa', ['nit' => '2425317']);
    }

    public function test_siswa_stats_are_computed(): void
    {
        $siswa = $this->studentOf('XI-RPL');

        $this->actingAs($siswa->user)->get('/siswa/riwayat')
            ->assertOk()
            ->assertSee('Kehadiran');
    }
}
