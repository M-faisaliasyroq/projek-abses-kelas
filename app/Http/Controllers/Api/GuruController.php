<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('cari');

        $guru = Guru::with('user')
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('nip', 'like', "%{$search}%");
            })
            ->orderBy('nip')
            ->paginate(10);

        return response()->json([
            'data' => $guru->map(fn (Guru $g) => $this->payload($g)),
            'pagination' => [
                'current_page' => $guru->currentPage(),
                'last_page' => $guru->lastPage(),
                'per_page' => $guru->perPage(),
                'total' => $guru->total(),
            ],
        ]);
    }

    public function show(Guru $guru): JsonResponse
    {
        return response()->json(['data' => $this->payload($guru->load('user'))]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'nip' => ['required', 'string', 'max:20', 'unique:guru,nip'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'guru',
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => $data['nip'],
            'alamat' => $data['alamat'] ?? null,
            'no_telp' => $data['no_telp'] ?? null,
        ]);

        return response()->json([
            'message' => 'Data guru berhasil ditambahkan.',
            'data' => $this->payload($guru->load('user')),
        ], 201);
    }

    public function update(Request $request, Guru $guru): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$guru->user_id],
            'password' => ['nullable', 'string', 'min:6'],
            'nip' => ['required', 'string', 'max:20', 'unique:guru,nip,'.$guru->id],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $guru->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => ($data['password'] ?? null) ?: $guru->user->password,
        ]);

        $guru->update([
            'nip' => $data['nip'],
            'alamat' => $data['alamat'] ?? null,
            'no_telp' => $data['no_telp'] ?? null,
        ]);

        return response()->json([
            'message' => 'Data guru berhasil diperbarui.',
            'data' => $this->payload($guru->load('user')),
        ]);
    }

    public function destroy(Guru $guru): JsonResponse
    {
        $guru->user->delete();

        return response()->json(['message' => 'Data guru berhasil dihapus.']);
    }

    private function payload(Guru $guru): array
    {
        return [
            'id' => $guru->id,
            'nip' => $guru->nip,
            'alamat' => $guru->alamat,
            'no_telp' => $guru->no_telp,
            'name' => $guru->user->name,
            'email' => $guru->user->email,
        ];
    }
}
