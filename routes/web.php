<?php

use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\PendingUserController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/{district}/start-election', [DashboardController::class, 'startElection'])->name('dashboard.start-election');
        Route::post('/dashboard/{district}/pause-election', [DashboardController::class, 'pauseElection'])->name('dashboard.pause-election');
        Route::post('/dashboard/{district}/resume-election', [DashboardController::class, 'resumeElection'])->name('dashboard.resume-election');
        Route::delete('/dashboard/{district}', [DashboardController::class, 'destroyElection'])->name('dashboard.destroy-election');
        Route::post('/dashboard/archives/{archive}/restore', [DashboardController::class, 'restoreElection'])->name('dashboard.restore-election');
        Route::delete('/dashboard/deleted-candidates/{deletedCandidate}', [DashboardController::class, 'destroyDeletedCandidate'])->name('dashboard.destroy-deleted-candidate');
        Route::resource('candidates', CandidateController::class)->except(['show']);
        Route::get('/districts/create', [DistrictController::class, 'create'])->name('districts.create');
        Route::post('/districts', [DistrictController::class, 'store'])->name('districts.store');
        Route::delete('/districts/hard-delete', [DistrictController::class, 'hardDelete'])->name('districts.hard-delete');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
        Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
        Route::put('/admins/{admin}', [AdminManagementController::class, 'update'])->name('admins.update');
        Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroy'])->name('admins.destroy');

        Route::get('/pending-users', [PendingUserController::class, 'index'])->name('pending-users.index');
        Route::put('/pending-users/{user}', [PendingUserController::class, 'update'])->name('pending-users.update');
        Route::delete('/pending-users/{user}', [PendingUserController::class, 'destroy'])->name('pending-users.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{type}/{format}', [ReportController::class, 'export'])->name('reports.export');
    });

    Route::middleware('approved')->group(function () {
        Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');
        Route::post('/vote', [VoteController::class, 'store'])->name('vote.store');
    });
});
