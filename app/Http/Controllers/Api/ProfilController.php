<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function show(): JsonResponse
    {
        $siswa = Auth::user()->siswa;

        return response()->json([
            'data' => [
                'id' => $siswa->id,
                'nit' => $siswa->nit,
                'nisn' => $siswa->nisn,
                'kelas' => $siswa->kelas,
                'alamat' => $siswa->alamat,
                'no_telp' => $siswa->no_telp,
                'name' => $siswa->user->name,
                'email' => $siswa->user->email,
            ],
        ]);
    }
}
