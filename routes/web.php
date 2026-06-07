<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ToolController as AdminToolController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ToolController as UserToolController;
use App\Http\Controllers\User\LoanRequestController;
use Illuminate\Support\Facades\Route;

// Public route

Route::get('/', function () {

    if (auth()->check()) {

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Public tool view route (for QR codes)
Route::get('/tool/{tool}', [UserToolController::class, 'show'])->name('tool.show');

// Auth routes
require __DIR__.'/auth.php';

// User routes (Student/Staff) - require auth and approval
Route::middleware(['auth', App\Http\Middleware\ApprovedUserMiddleware::class])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tools', [UserToolController::class, 'index'])->name('tools.index');
    Route::post('/loan-request', [LoanRequestController::class, 'store'])->name('loan.request');
    Route::get('/my-loans', [LoanRequestController::class, 'index'])->name('loans.my');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', App\Http\Middleware\AdminMiddleware::class])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Tools management
    Route::get('tools/import-template', [AdminToolController::class, 'downloadImportTemplate'])->name('tools.import.template');
    Route::post('tools/import', [AdminToolController::class, 'import'])->name('tools.import');
    Route::get('tools/download-all-qrs', [AdminToolController::class, 'downloadAllQrs'])->name('tools.download-all-qrs');
    Route::resource('tools', AdminToolController::class);
    Route::get('tools/{tool}/qr-show', [AdminToolController::class, 'showQr'])->name('tools.qr.show');
    Route::get('tools/{tool}/qr', [AdminToolController::class, 'downloadQr'])->name('tools.qr');
    
    // Loan requests management
    Route::get('loans', [AdminLoanController::class, 'index'])->name('loans.index');
    Route::get('loans/data', [AdminLoanController::class, 'getData'])->name('loans.data');
    Route::post('loans/{loan}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [AdminLoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/return', [AdminLoanController::class, 'return'])->name('loans.return');
    
    // User management
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/data', [AdminUserController::class, 'getData'])->name('users.data');
    Route::post('users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    
    // Settings management
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/{type}/import', [AdminSettingsController::class, 'import'])->name('settings.import');
    Route::post('settings/majors', [AdminSettingsController::class, 'storeMajor'])->name('settings.majors.store');
    Route::delete('settings/majors/{major}', [AdminSettingsController::class, 'deleteMajor'])->name('settings.majors.delete');
    Route::post('settings/levels', [AdminSettingsController::class, 'storeLevel'])->name('settings.levels.store');
    Route::delete('settings/levels/{level}', [AdminSettingsController::class, 'deleteLevel'])->name('settings.levels.delete');
    Route::post('settings/departments', [AdminSettingsController::class, 'storeDepartment'])->name('settings.departments.store');
    Route::delete('settings/departments/{department}', [AdminSettingsController::class, 'deleteDepartment'])->name('settings.departments.delete');
    Route::post('settings/tool-types', [AdminSettingsController::class, 'storeToolType'])->name('settings.tool-types.store');
    Route::delete('settings/tool-types/{toolType}', [AdminSettingsController::class, 'deleteToolType'])->name('settings.tool-types.delete');
    Route::post('settings/halls', [AdminSettingsController::class, 'storeHall'])->name('settings.halls.store');
    Route::delete('settings/halls/{hall}', [AdminSettingsController::class, 'deleteHall'])->name('settings.halls.delete');
    Route::post('settings/loan-detail-keys', [AdminSettingsController::class, 'storeLoanDetailKey'])->name('settings.loan-detail-keys.store');
    Route::delete('settings/loan-detail-keys/{loanDetailKey}', [AdminSettingsController::class, 'deleteLoanDetailKey'])->name('settings.loan-detail-keys.delete');
});
