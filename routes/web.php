<?php

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Siswa\ProfilController;
use App\Http\Controllers\Siswa\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('siswa', SiswaController::class)->except(['show']);
        Route::resource('guru', GuruController::class)->except(['show']);
        Route::resource('mapel', MataPelajaranController::class)->except(['show']);
        Route::resource('jadwal', JadwalController::class)->except(['show']);
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
    });

    Route::prefix('guru')->name('guru.')->middleware('role:guru')->group(function () {
        Route::get('jadwal', [AbsensiController::class, 'jadwal'])->name('absensi.jadwal');
        Route::get('absensi/{jadwal}/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('absensi/{jadwal}', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    });

    Route::prefix('siswa')->name('siswa.')->middleware('role:siswa')->group(function () {
        Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat');
        Route::get('profil', [ProfilController::class, 'show'])->name('profil');
    });
});
