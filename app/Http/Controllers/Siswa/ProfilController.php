<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        $siswa = auth()->user()->siswa;

        return view('siswa.profil', compact('siswa'));
    }
}
