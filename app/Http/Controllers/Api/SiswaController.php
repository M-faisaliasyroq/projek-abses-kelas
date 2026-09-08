<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('cari');

        $siswa = Siswa::with('user')
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('nit', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%");
            })
            ->orderBy('kelas')
            ->orderBy('nit')
            ->paginate(10);

        return response()->json([
            'data' => $siswa->map(fn (Siswa $s) => $this->payload($s)),
            'pagination' => [
                'current_page' => $siswa->currentPage(),
                'last_page' => $siswa->lastPage(),
                'per_page' => $siswa->perPage(),
                'total' => $siswa->total(),
            ],
        ]);
    }

    public function show(Siswa $siswa): JsonResponse
    {
        return response()->json(['data' => $this->payload($siswa->load('user'))]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'nit' => ['required', 'string', 'max:20', 'unique:siswa,nit'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:siswa,nisn'],
            'kelas' => ['required', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'siswa',
        ]);

        $siswa = Siswa::create([
            'user_id' => $user->id,
            'nit' => $data['nit'],
            'nisn' => $data['nisn'] ?? null,
            'kelas' => $data['kelas'],
            'alamat' => $data['alamat'] ?? null,
            'no_telp' => $data['no_telp'] ?? null,
        ]);

        return response()->json([
            'message' => 'Data siswa berhasil ditambahkan.',
            'data' => $this->payload($siswa->load('user')),
        ], 201);
    }

    public function update(Request $request, Siswa $siswa): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$siswa->user_id],
            'password' => ['nullable', 'string', 'min:6'],
            'nit' => ['required', 'string', 'max:20', 'unique:siswa,nit,'.$siswa->id],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:siswa,nisn,'.$siswa->id],
            'kelas' => ['required', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $siswa->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => ($data['password'] ?? null) ?: $siswa->user->password,
        ]);

        $siswa->update([
            'nit' => $data['nit'],
            'nisn' => $data['nisn'] ?? null,
            'kelas' => $data['kelas'],
            'alamat' => $data['alamat'] ?? null,
            'no_telp' => $data['no_telp'] ?? null,
        ]);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => $this->payload($siswa->load('user')),
        ]);
    }

    public function destroy(Siswa $siswa): JsonResponse
    {
        $siswa->user->delete();

        return response()->json(['message' => 'Data siswa berhasil dihapus.']);
    }

    private function payload(Siswa $siswa): array
    {
        return [
            'id' => $siswa->id,
            'nit' => $siswa->nit,
            'nisn' => $siswa->nisn,
            'kelas' => $siswa->kelas,
            'alamat' => $siswa->alamat,
            'no_telp' => $siswa->no_telp,
            'name' => $siswa->user->name,
            'email' => $siswa->user->email,
        ];
    }
}
