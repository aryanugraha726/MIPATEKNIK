<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PPICController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SubprojectController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\MaterialRequestController;
use App\Http\Controllers\ManagerApprovalController;
use App\Http\Controllers\ShippingAddressController;
use App\Http\Controllers\POController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Dashboard Utama — Semua role bisa akses
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =============================================
    // MENU PPIC (role: ADMIN, PPIC)
    // Dashboard PPIC, Project CRUD, Subproject CRUD, Tugas CRUD
    // =============================================
    Route::middleware('role:ADMIN,PPIC')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::resource('subprojects', SubprojectController::class)->except(['index']);
        Route::resource('tugas', TugasController::class)->except(['index', 'show']);
    });

    // =============================================
    // MENU PURCHASING (role: ADMIN, PURCHASING)
    // Stock opname, transaksi masuk/keluar, riwayat
    // =============================================
    Route::middleware('role:ADMIN,PURCHASING')->group(function () {
        Route::resource('barang', App\Http\Controllers\BarangController::class)->except(['show']);
        Route::get('/stok', [StockController::class, 'index'])->name('stock.index');
        Route::get('/stok/export', [StockController::class, 'export'])->name('stock.export');
        Route::get('/barang/next-id/{id_kategori}', [App\Http\Controllers\BarangController::class, 'getNextId'])->name('barang.nextId');
        Route::get('/transaksi/baru', [TransactionController::class, 'create'])->name('transaksi.create');
        Route::post('/transaksi/baru', [TransactionController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/po-details/{no_po}', [TransactionController::class, 'getPoDetails'])->name('transaksi.poDetails')->where('no_po', '.*');
        Route::post('/transaksi/barang-ajax', [App\Http\Controllers\BarangController::class, 'storeAjax'])->name('barang.storeAjax');
        Route::get('/transaksi/antrean', [TransactionController::class, 'pendingList'])->name('transaksi.antrean');
        Route::get('/riwayat/masuk', [TransactionController::class, 'historyMasuk'])->name('transaksi.masuk');
        Route::get('/riwayat/keluar', [TransactionController::class, 'historyKeluar'])->name('transaksi.keluar');
        Route::get('/riwayat/keluar/barang/{id}', [TransactionController::class, 'detailBarangKeluar'])->name('transaksi.keluar.barang');
        Route::get('/riwayat/keluar/project/{id}', [TransactionController::class, 'detailProjectKeluar'])->name('transaksi.keluar.project');
    });

    // =============================================
    // MENU KARYAWAN (role: ADMIN, KARYAWAN, PURCHASING)
    // =============================================
    Route::middleware('role:ADMIN,KARYAWAN,PURCHASING')->group(function () {
        Route::get('/my-projects', [PPICController::class, 'myProjects'])->name('karyawan.projects');
        Route::get('/my-projects/{id}/gantt', [PPICController::class, 'myGantt'])->name('karyawan.gantt');

        Route::resource('material-requests', MaterialRequestController::class);
        Route::post('/material-requests/{id}/submit', [MaterialRequestController::class, 'submit'])->name('material-requests.submit');
        Route::post('/material-requests/{id}/add-detail', [MaterialRequestController::class, 'addDetail'])->name('material-requests.addDetail');
        Route::delete('/material-requests/detail/{id}', [MaterialRequestController::class, 'removeDetail'])->name('material-requests.removeDetail');

        // Routing untuk Shipping Address
        Route::resource('shipping-address', ShippingAddressController::class);

        // Routing untuk Purchase Order
        // ⚠️ Route statis harus didaftarkan SEBELUM resource route yang memakai wildcard
        Route::get('/po/export-pdf', [POController::class, 'exportPdf'])->name('po.exportPdf');
        Route::resource('po', POController::class)->parameters(['po' => 'no_po'])->where(['no_po' => '.*']);
        Route::post('/po/{no_po}/add-detail', [POController::class, 'addDetail'])->name('po.add-detail')->where('no_po', '.*');
        Route::delete('/po/detail/{id}', [POController::class, 'removeDetail'])->name('po.remove-detail');
        Route::post('/po/{no_po}/submit', [POController::class, 'submit'])->name('po.submit')->where('no_po', '.*');
        Route::post('/po/{no_po}/approve', [POController::class, 'approve'])->name('po.approve')->where('no_po', '.*');
        Route::post('/po/{no_po}/reject', [POController::class, 'reject'])->name('po.reject')->where('no_po', '.*');
    });

    // =============================================
    // MENU MANAGER (role: ADMIN, MANAGEMENT)
    // =============================================
    Route::middleware('role:ADMIN,MANAGEMENT')->group(function () {
        Route::get('/manager-approval', [ManagerApprovalController::class, 'index'])->name('manager_approval.index');
        Route::post('/manager-approval/{no_nota}/approve', [ManagerApprovalController::class, 'approve'])->name('manager_approval.approve');
        Route::post('/manager-approval/{no_nota}/reject', [ManagerApprovalController::class, 'reject'])->name('manager_approval.reject');
    });

    // =============================================
    // TOGGLE STATUS TUGAS (role: ADMIN, PPIC, KARYAWAN)
    // =============================================
    Route::middleware('role:ADMIN,PPIC,KARYAWAN')->group(function () {
        Route::post('/subprojects/{id}/toggle-status', [SubprojectController::class, 'toggleStatus'])->name('subprojects.toggle-status');
        Route::post('/tugas/{id}/toggle-status', [TugasController::class, 'toggleStatus'])->name('tugas.toggle-status');
    });

    // =============================================
    // MENU DIREKTUR UTAMA (role: ADMIN, DIREKTUR UTAMA)
    // Stock opname (read-only) dan daftar proyek (read-only)
    // =============================================
    Route::middleware('role:ADMIN,DIREKTUR UTAMA')->group(function () {
        Route::get('/direktur/stok', [StockController::class, 'index'])->name('direktur.stock');
    });

    // Gantt Chart & Dashboard PPIC accessible by ADMIN, PPIC, DIREKTUR UTAMA
    Route::middleware('role:ADMIN,PPIC,DIREKTUR UTAMA')->group(function () {
        Route::get('/ppic/dashboard', [PPICController::class, 'index'])->name('ppic.index');
        Route::get('/projects/{id}/gantt', [PPICController::class, 'gantt'])->name('ppic.gantt');
    });

    // =============================================
    // MASTER DATA (role: ADMIN saja)
    // =============================================
    Route::middleware('role:ADMIN')->group(function () {
        Route::get('/master-data', [MasterDataController::class, 'index'])->name('master-data.index');
        Route::resource('divisi', DivisiController::class)->except(['show']);
        Route::resource('karyawan', KaryawanController::class)->except(['show']);
        Route::resource('management', ManagementController::class)->except(['show']);
        Route::resource('vendor', VendorController::class)->except(['show']);
        Route::resource('kategori', KategoriBarangController::class)->except(['show']);
        Route::resource('satuan', SatuanController::class)->except(['show']);
        Route::resource('users', UserController::class);
    });

});
