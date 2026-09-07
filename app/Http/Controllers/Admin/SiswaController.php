<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
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
            ->paginate(10)
            ->withQueryString();

        return view('admin.siswa.index', compact('siswa', 'search'));
    }

    public function create(): View
    {
        return view('admin.siswa.create');
    }

    public function store(Request $request): RedirectResponse
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

        Siswa::create([
            'user_id' => $user->id,
            'nit' => $data['nit'],
            'nisn' => $data['nisn'] ?: null,
            'kelas' => $data['kelas'],
            'alamat' => $data['alamat'],
            'no_telp' => $data['no_telp'],
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa): View
    {
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
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
            'password' => $data['password'] ?: $siswa->user->password,
        ]);

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->user->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
