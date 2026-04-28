<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\GuruMapelController;
use App\Http\Controllers\Admin\HistoryJadwalController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RuangController;
use App\Http\Controllers\Admin\WaktuController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Landing\JadwalPreviewController;
use App\Http\Controllers\Landing\LandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('admin');
// });

Route::group([], function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');
    Route::get('/tentang', [LandingController::class, 'tentang'])->name('tentang');
    Route::get('/fitur', [LandingController::class, 'fitur'])->name('fitur');
    Route::get('/statistik', [LandingController::class, 'statistik'])->name('statistik');
    Route::get('/kontak', [LandingController::class, 'kontak'])->name('kontak');
    Route::get('/jadwal-preview', [JadwalPreviewController::class, 'index'])->name('jadwal.preview');
    Route::get('/api/jadwal-full', [JadwalPreviewController::class, 'getFullJadwal'])->name('api.jadwal.full');
    Route::get('/api/jadwal-by-kelas', [JadwalPreviewController::class, 'getJadwalByKelas'])->name('api.jadwal.by-kelas');
    Route::get('/api/jadwal-by-guru', [JadwalPreviewController::class, 'getJadwalByGuru'])->name('api.jadwal.by-guru');
});

Route::group([], function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::group([], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard/bentrok', [DashboardController::class, 'getBentrokData'])->name('api.dashboard.bentrok');
        Route::get('/api/dashboard/beban-guru', [DashboardController::class, 'getBebanGuruData'])->name('api.dashboard.beban-guru');
    });

    Route::group([], function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::group([], function () {
        Route::get('/guru', [GuruController::class, 'index'])->name('guru');
        Route::get('/guru/create', [GuruController::class, 'create'])->name('guru.create');
        Route::post('/guru/store', [GuruController::class, 'store'])->name('guru.store');
        Route::get('/guru/edit/{id}', [GuruController::class, 'edit'])->name('guru.edit');
        Route::put('/guru/update/{id}', [GuruController::class, 'update'])->name('guru.update');
        Route::delete('/guru/delete/{id}', [GuruController::class, 'delete'])->name('guru.delete');
        // Route::get('/guru/export', [GuruController::class, 'export'])->name('guru.export');
        // Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
    });

    Route::group([], function () {
        Route::get('/mapel', [MapelController::class, 'index'])->name('mapel');
        Route::get('/mapel/create', [MapelController::class, 'create'])->name('mapel.create');
        Route::post('/mapel/store', [MapelController::class, 'store'])->name('mapel.store');
        Route::get('/mapel/edit/{id}', [MapelController::class, 'edit'])->name('mapel.edit');
        Route::put('/mapel/update/{id}', [MapelController::class, 'update'])->name('mapel.update');
        Route::delete('/mapel/delete/{id}', [MapelController::class, 'delete'])->name('mapel.delete');
    });

    Route::group([], function () {
        Route::get('/ruang', [RuangController::class, 'index'])->name('ruang');
        Route::get('/ruang/create', [RuangController::class, 'create'])->name('ruang.create');
        Route::post('/ruang/store', [RuangController::class, 'store'])->name('ruang.store');
        Route::get('/ruang/edit/{id}', [RuangController::class, 'edit'])->name('ruang.edit');
        Route::put('/ruang/update/{id}', [RuangController::class, 'update'])->name('ruang.update');
        Route::delete('/ruang/delete/{id}', [RuangController::class, 'delete'])->name('ruang.delete');
    });

    Route::group([], function () {
        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas');
        Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/kelas/store', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/edit/{id}', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/update/{id}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/delete/{id}', [KelasController::class, 'delete'])->name('kelas.delete');
    });

    Route::group([], function () {
        Route::get('/waktu', [WaktuController::class, 'index'])->name('waktu');
        Route::get('/waktu/create', [WaktuController::class, 'create'])->name('waktu.create');
        Route::post('/waktu/store', [WaktuController::class, 'store'])->name('waktu.store');
        Route::get('/waktu/edit/{id}', [WaktuController::class, 'edit'])->name('waktu.edit');
        Route::put('/waktu/update/{id}', [WaktuController::class, 'update'])->name('waktu.update');
        Route::delete('/waktu/delete/{id}', [WaktuController::class, 'delete'])->name('waktu.delete');
    });

    Route::group([], function () {
        Route::get('/guru_mapel', [GuruMapelController::class, 'index'])->name('guru_mapel');
        Route::get('/guru_mapel/create', [GuruMapelController::class, 'create'])->name('guru_mapel.create');
        Route::post('/guru_mapel/store', [GuruMapelController::class, 'store'])->name('guru_mapel.store');
        Route::get('/guru_mapel/edit/{id}', [GuruMapelController::class, 'edit'])->name('guru_mapel.edit');
        Route::put('/guru_mapel/update/{id}', [GuruMapelController::class, 'update'])->name('guru_mapel.update');
        Route::delete('/guru_mapel/delete/{id}', [GuruMapelController::class, 'delete'])->name('guru_mapel.delete');
    });

    Route::group([], function () {
        Route::get('/generate-jadwal', [JadwalController::class, 'index'])->name('generate-jadwal');
        Route::get('/generate-jadwal/run', [JadwalController::class, 'generate'])->name('generate-jadwal.run');
        Route::post('/generate-jadwal/simpan', [JadwalController::class, 'simpan'])->name('generate-jadwal.simpan');
        Route::get('/get-guru-mapel-options', [JadwalController::class, 'getGuruMapelOptions'])->name('get.guru.mapel');
        Route::post('/generate-jadwal/update-cell', [JadwalController::class, 'updateCell'])->name('generate-jadwal.update-cell');
    });

    Route::group([], function () {
        Route::get('/history-jadwal', [HistoryJadwalController::class, 'index'])->name('history.jadwal.index');
        Route::get('/history-jadwal/{id}', [HistoryJadwalController::class, 'show'])->name('history.jadwal.show');
        Route::delete('/history-jadwal/{id}', [HistoryJadwalController::class, 'destroy'])->name('history.jadwal.destroy');
        Route::post('/history-jadwal/{id}/update-cell', [HistoryJadwalController::class, 'updateCell'])->name('history.jadwal.update-cell');
        Route::post('/history-jadwal/{id}/update-master', [HistoryJadwalController::class, 'updateMaster'])->name('history.jadwal.update-master');
        Route::post('/history-jadwal/{id}/save-changes', [HistoryJadwalController::class, 'saveChanges'])->name('history.jadwal.save-changes');
    });
});
