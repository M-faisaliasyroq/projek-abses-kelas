<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('cari');

        $mapel = MataPelajaran::when($search, function ($query, $search) {
            $query->where('nama_mapel', 'like', "%{$search}%")
                ->orWhere('kode_mapel', 'like', "%{$search}%");
        })
            ->orderBy('kode_mapel')
            ->paginate(10);

        return response()->json([
            'data' => $mapel->map(fn (MataPelajaran $m) => $this->payload($m)),
            'pagination' => [
                'current_page' => $mapel->currentPage(),
                'last_page' => $mapel->lastPage(),
                'per_page' => $mapel->perPage(),
                'total' => $mapel->total(),
            ],
        ]);
    }

    public function show(MataPelajaran $mapel): JsonResponse
    {
        return response()->json(['data' => $this->payload($mapel)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:15', 'unique:mata_pelajaran,kode_mapel'],
            'nama_mapel' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $mapel = MataPelajaran::create($data);

        return response()->json([
            'message' => 'Mata pelajaran berhasil ditambahkan.',
            'data' => $this->payload($mapel),
        ], 201);
    }

    public function update(Request $request, MataPelajaran $mapel): JsonResponse
    {
        $data = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:15', 'unique:mata_pelajaran,kode_mapel,'.$mapel->id],
            'nama_mapel' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $mapel->update($data);

        return response()->json([
            'message' => 'Mata pelajaran berhasil diperbarui.',
            'data' => $this->payload($mapel),
        ]);
    }

    public function destroy(MataPelajaran $mapel): JsonResponse
    {
        $mapel->delete();

        return response()->json(['message' => 'Mata pelajaran berhasil dihapus.']);
    }

    private function payload(MataPelajaran $mapel): array
    {
        return [
            'id' => $mapel->id,
            'kode_mapel' => $mapel->kode_mapel,
            'nama_mapel' => $mapel->nama_mapel,
            'deskripsi' => $mapel->deskripsi,
        ];
    }
}
