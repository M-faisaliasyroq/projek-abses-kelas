<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\MataPelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('cari');

        $jadwal = Jadwal::with(['guru.user', 'mataPelajaran'])
            ->when($search, function ($query, $search) {
                $query->where('kelas', 'like', "%{$search}%")
                    ->orWhereHas('guru.user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('mataPelajaran', fn ($q) => $q->where('nama_mapel', 'like', "%{$search}%"));
            })
            ->orderBy('kelas')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(15);

        return response()->json([
            'data' => $jadwal->map(fn (Jadwal $j) => $this->payload($j)),
            'pagination' => [
                'current_page' => $jadwal->currentPage(),
                'last_page' => $jadwal->lastPage(),
                'per_page' => $jadwal->perPage(),
                'total' => $jadwal->total(),
            ],
        ]);
    }

    public function show(Jadwal $jadwal): JsonResponse
    {
        return response()->json(['data' => $this->payload($jadwal->load(['guru.user', 'mataPelajaran']))]);
    }

    public function options(): JsonResponse
    {
        return response()->json([
            'guru' => Guru::with('user')->orderBy('id')->get()->map(fn (Guru $g) => [
                'id' => $g->id,
                'name' => $g->user->name,
            ]),
            'mapel' => MataPelajaran::orderBy('kode_mapel')->get(['id', 'kode_mapel', 'nama_mapel']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:guru,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'kelas' => ['required', 'string', 'max:15'],
            'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $jadwal = Jadwal::create($data);

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan.',
            'data' => $this->payload($jadwal->load(['guru.user', 'mataPelajaran'])),
        ], 201);
    }

    public function update(Request $request, Jadwal $jadwal): JsonResponse
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:guru,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'kelas' => ['required', 'string', 'max:15'],
            'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $jadwal->update($data);

        return response()->json([
            'message' => 'Jadwal berhasil diperbarui.',
            'data' => $this->payload($jadwal->load(['guru.user', 'mataPelajaran'])),
        ]);
    }

    public function destroy(Jadwal $jadwal): JsonResponse
    {
        $jadwal->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }

    private function payload(Jadwal $jadwal): array
    {
        return [
            'id' => $jadwal->id,
            'kelas' => $jadwal->kelas,
            'hari' => $jadwal->hari,
            'jam_mulai' => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
            'guru_id' => $jadwal->guru_id,
            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
            'guru' => $jadwal->guru ? [
                'id' => $jadwal->guru->id,
                'name' => $jadwal->guru->user->name,
            ] : null,
            'mapel' => $jadwal->mataPelajaran ? $jadwal->mataPelajaran->toArray() : null,
        ];
    }
}
