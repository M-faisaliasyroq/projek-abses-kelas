<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function userByRole(string $role): User
    {
        return User::where('role', $role)->first();
    }

    public function test_login_returns_token_and_user(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@smkrpl.test',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('user.role', 'admin');
    }

    public function test_login_rejects_wrong_password(): void
    {
        $this->postJson('/api/v1/login', [
            'email' => 'admin@smkrpl.test',
            'password' => 'salah',
        ])->assertStatus(422);
    }

    public function test_protected_routes_require_token(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
        $this->getJson('/api/v1/dashboard')->assertUnauthorized();
    }

    public function test_token_login_can_access_me_and_logout(): void
    {
        $token = $this->postJson('/api/v1/login', [
            'email' => 'admin@smkrpl.test',
            'password' => 'password',
        ])->json('token');

        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/v1/me', $headers)
            ->assertOk()
            ->assertJsonPath('user.email', 'admin@smkrpl.test');

        $user = User::where('email', 'admin@smkrpl.test')->first();
        $this->assertCount(1, $user->tokens);

        $this->postJson('/api/v1/logout', [], $headers)->assertOk();
        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_dashboard_returns_role_specific_data(): void
    {
        Sanctum::actingAs($this->userByRole('admin'));
        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => ['total_siswa', 'total_guru']]);

        Sanctum::actingAs($this->userByRole('guru'));
        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => ['total_jadwal', 'jadwal_hari_ini']]);

        Sanctum::actingAs($this->userByRole('siswa'));
        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => ['siswa', 'total_hadir']]);
    }

    public function test_admin_siswa_crud(): void
    {
        Sanctum::actingAs($this->userByRole('admin'));

        $this->getJson('/api/v1/admin/siswa')->assertOk();

        $response = $this->postJson('/api/v1/admin/siswa', [
            'name' => 'Api Test Siswa',
            'email' => 'apitest@gmail.com',
            'password' => 'password',
            'nit' => '2425399',
            'nisn' => null,
            'kelas' => 'XI-RPL',
        ])->assertCreated();

        $siswa = Siswa::where('nit', '2425399')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals('Api Test Siswa', $siswa->user->name);
        $this->assertNull($siswa->nisn);

        $this->putJson('/api/v1/admin/siswa/'.$siswa->id, [
            'name' => 'Api Test Siswa 2',
            'email' => 'apitest@gmail.com',
            'password' => null,
            'nit' => '2425399',
            'nisn' => null,
            'kelas' => 'XI-RPL',
        ])->assertOk();

        $this->assertEquals('Api Test Siswa 2', $siswa->fresh()->user->name);

        $this->deleteJson('/api/v1/admin/siswa/'.$siswa->id)->assertOk();
        $this->assertDatabaseMissing('users', ['email' => 'apitest@gmail.com']);
    }

    public function test_admin_guru_mapel_jadwal_and_laporan(): void
    {
        Sanctum::actingAs($this->userByRole('admin'));

        $this->getJson('/api/v1/admin/guru')->assertOk();
        $this->getJson('/api/v1/admin/mapel')->assertOk();
        $this->getJson('/api/v1/admin/jadwal')->assertOk();
        $this->getJson('/api/v1/admin/jadwal-options')
            ->assertOk()
            ->assertJsonStructure(['guru', 'mapel']);
        $this->getJson('/api/v1/admin/laporan')->assertOk();

        $this->postJson('/api/v1/admin/guru', [
            'name' => 'Guru Api',
            'email' => 'guruapi@gmail.com',
            'password' => 'password',
            'nip' => '998877',
        ])->assertCreated();

        $this->postJson('/api/v1/admin/mapel', [
            'kode_mapel' => 'X-TEST',
            'nama_mapel' => 'Mapel Test',
        ])->assertCreated();

        $jadwal = Jadwal::with('guru')->first();
        $this->postJson('/api/v1/admin/jadwal', [
            'guru_id' => $jadwal->guru_id,
            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
            'kelas' => 'XI-RPL-A',
            'hari' => 'jumat',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:30',
        ])->assertCreated();

        $this->deleteJson('/api/v1/admin/guru/'.$jadwal->guru_id)->assertOk();
    }

    public function test_role_isolation(): void
    {
        Sanctum::actingAs($this->userByRole('siswa'));
        $this->getJson('/api/v1/admin/siswa')->assertForbidden();
        $this->getJson('/api/v1/guru/jadwal')->assertForbidden();

        Sanctum::actingAs($this->userByRole('guru'));
        $this->getJson('/api/v1/admin/siswa')->assertForbidden();
    }

    public function test_guru_absensi_flow(): void
    {
        $guruUser = Guru::with('user')->first()->user;

        Sanctum::actingAs($guruUser);

        $this->getJson('/api/v1/guru/jadwal')->assertOk();
        $this->getJson('/api/v1/guru/riwayat')->assertOk();

        $jadwal = $guruUser->guru->jadwal()->first();
        $response = $this->getJson('/api/v1/guru/absensi/'.$jadwal->id)->assertOk();
        $lyodSiswa = $response->json('data.siswa.0');
        $this->assertNotNull($lyodSiswa);

        $this->postJson('/api/v1/guru/absensi/'.$jadwal->id, [
            'tanggal' => today()->toDateString(),
            'status' => [
                $lyodSiswa['id'] => 'hadir',
            ],
        ])->assertOk();

        $this->assertDatabaseHas('absensi', [
            'siswa_id' => $lyodSiswa['id'],
            'jadwal_id' => $jadwal->id,
        ]);
    }

    public function test_siswa_riwayat_and_profil(): void
    {
        $siswa = Siswa::with('user')->first();

        Sanctum::actingAs($siswa->user);

        $this->getJson('/api/v1/siswa/profil')
            ->assertOk()
            ->assertJsonPath('data.email', $siswa->user->email)
            ->assertJsonPath('data.nit', $siswa->nit);

        $this->getJson('/api/v1/siswa/riwayat')
            ->assertOk()
            ->assertJsonStructure(['data', 'stats', 'mapel']);
    }
}
