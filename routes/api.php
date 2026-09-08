<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\MataPelajaranController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\RiwayatController;
use App\Http\Controllers\Api\SiswaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::apiResource('siswa', SiswaController::class);
            Route::apiResource('guru', GuruController::class);
            Route::apiResource('mapel', MataPelajaranController::class);
            Route::apiResource('jadwal', JadwalController::class);
            Route::get('jadwal-options', [JadwalController::class, 'options']);
            Route::get('laporan', [LaporanController::class, 'index']);
        });

        Route::middleware('role:guru')->prefix('guru')->group(function () {
            Route::get('jadwal', [AbsensiController::class, 'jadwal']);
            Route::get('absensi/{jadwal}', [AbsensiController::class, 'create']);
            Route::post('absensi/{jadwal}', [AbsensiController::class, 'store']);
            Route::get('riwayat', [AbsensiController::class, 'riwayat']);
        });

        Route::middleware('role:siswa')->prefix('siswa')->group(function () {
            Route::get('riwayat', [RiwayatController::class, 'index']);
            Route::get('profil', [ProfilController::class, 'show']);
        });
    });
});
