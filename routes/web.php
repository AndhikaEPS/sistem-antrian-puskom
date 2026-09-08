<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OfficerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\IdentifyController;
use App\Http\Controllers\Mahasiswa\QueueController;
use App\Http\Controllers\Petugas\CallController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing.index')->name('landing');
Route::get('/display', [DisplayController::class, 'index'])->name('display.index');
Route::get('/display/poll', [DisplayController::class, 'poll'])->name('display.poll');

Route::get('/ambil-antrian/mahasiswa', [IdentifyController::class, 'showMahasiswa'])->name('identify.mahasiswa');
Route::post('/ambil-antrian/mahasiswa', [IdentifyController::class, 'identifyMahasiswa'])->name('identify.mahasiswa.submit');
Route::get('/ambil-antrian/dosen', [IdentifyController::class, 'showDosen'])->name('identify.dosen');
Route::post('/ambil-antrian/dosen', [IdentifyController::class, 'identifyDosen'])->name('identify.dosen.submit');
Route::get('/ambil-antrian/pengunjung', [IdentifyController::class, 'showPengunjung'])->name('identify.pengunjung');
Route::post('/ambil-antrian/pengunjung', [IdentifyController::class, 'identifyPengunjung'])->name('identify.pengunjung.submit');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'update'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:mahasiswa,dosen,pengunjung'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [QueueController::class, 'dashboard'])->name('dashboard');
    Route::get('/layanan', [QueueController::class, 'services'])->name('services');
    Route::post('/layanan/{service}/ambil', [QueueController::class, 'store'])->name('queue.store');
    Route::get('/antrian/{queue}', [QueueController::class, 'show'])->name('queue.show');
    Route::get('/antrian/{queue}/status', [QueueController::class, 'status'])->name('queue.status');
    Route::post('/antrian/{queue}/batal', [QueueController::class, 'cancel'])->name('queue.cancel');
    Route::post('/antrian/{queue}/rating', [QueueController::class, 'rate'])->name('queue.rate');
    Route::get('/riwayat', [QueueController::class, 'history'])->name('history');
});

Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [CallController::class, 'dashboard'])->name('dashboard');
    Route::post('/pengaturan-loket', [CallController::class, 'setService'])->name('set-service');
    Route::post('/panggil-berikutnya', [CallController::class, 'callNext'])->name('call-next');
    Route::post('/antrian/{queue}/panggil-ulang', [CallController::class, 'recall'])->name('recall');
    Route::post('/antrian/{queue}/mulai', [CallController::class, 'startServing'])->name('start');
    Route::post('/antrian/{queue}/selesai', [CallController::class, 'complete'])->name('complete');
    Route::post('/antrian/{queue}/lewati', [CallController::class, 'skip'])->name('skip');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('/officers', [OfficerController::class, 'index'])->name('officers.index');
    Route::put('/officers/{officer}', [OfficerController::class, 'update'])->name('officers.update');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
});

Route::get('/dashboard', function () {
    return match (auth()->user()?->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'petugas' => redirect()->route('petugas.dashboard'),
        default => redirect()->route('mahasiswa.dashboard'),
    };
})->middleware('auth')->name('dashboard');
