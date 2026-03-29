<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserInvoiceController;
use App\Http\Controllers\ProfitExportController;

Route::get('/', function () {
    return redirect()->route('register');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Redirect dashboard based on role
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'moderator' => redirect()->route('moderator.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    })->name('dashboard');

    // ============================
    // ADMIN
    // ============================
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');

        Route::post('/admin/users/{id}/role', [AdminController::class, 'updateRole'])
            ->name('admin.users.role');

        Route::get('/admin/profit/export', [ProfitExportController::class, 'adminExport'])
            ->name('admin.profit.export');
    });

    // ============================
    // MODERATOR
    // ============================
    Route::middleware('role:moderator')->group(function () {
        Route::get('/moderator/dashboard', [ModeratorController::class, 'dashboard'])
            ->name('moderator.dashboard');

        Route::get('/moderator/appointments', [AppointmentController::class, 'moderatorIndex'])
            ->name('moderator.appointments.index');

        Route::patch('/moderator/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
            ->name('moderator.appointments.status');

        Route::get('/moderator/calendar', [AppointmentController::class, 'calendar'])
            ->name('moderator.calendar');

        Route::get('/moderator/invoices/{invoice}/pdf', [AppointmentController::class, 'downloadInvoicePdf'])
            ->name('moderator.invoices.pdf');
        
        Route::get('/moderator/profit/export', [ProfitExportController::class, 'moderatorExport'])
            ->name('moderator.profit.export');
    });

    // ============================
    // USER
    // ============================
    Route::middleware('role:user')->group(function () {

        Route::get('/user/dashboard', [UserController::class, 'dashboard'])
            ->name('user.dashboard');

        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

        Route::post('/appointments', [AppointmentController::class, 'store'])
            ->name('appointments.store');

        // ✅ USER INVOICES (ONE controller, correct names)
        Route::get('/user/invoices', [UserInvoiceController::class, 'index'])
            ->name('user.invoices.index');

        Route::get('/user/invoices/{invoice}', [UserInvoiceController::class, 'show'])
            ->name('user.invoices.show');

        Route::get('/user/invoices/{invoice}/pdf', [UserInvoiceController::class, 'downloadPdf'])
            ->name('user.invoices.pdf');
    });

});
