<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseReportController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\GoogleLoginController;

require __DIR__.'/auth.php';

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [ExpenseReportController::class, 'adminDashboard'])
        ->name('admin.dashboard');
    Route::put('/expense-reports/{report}/status', [ExpenseReportController::class, 'updateStatus'])
        ->name('expense-reports.update-status');
    Route::get('/admin/analytics', [ExpenseReportController::class, 'analytics'])
        ->name('admin.analytics');
});

// Regular user routes
Route::middleware(['auth', 'verified', 'prevent-admin'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Regular user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ExpenseController::class, 'getDashboardData'])->name('dashboard');
    
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

  

    Route::post('/expenses/submit-report', [ExpenseReportController::class, 'store'])
        ->name('expenses.submit-report');
        
    Route::get('/admin/dashboard', [ExpenseReportController::class, 'adminDashboard'])
        ->middleware(['admin'])
        ->name('admin.dashboard');

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/calendar', function () {
        return Inertia::render('Calendar/Index');
    })->name('calendar.index');
});

// Google auth routes
Route::middleware('guest')->group(function () {
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])
        ->name('auth.google');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');
});















