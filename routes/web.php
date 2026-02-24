<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Home')->name('home');

Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('admin')->group(function () {
    Route::get('/admin/audit',[AuditController::class, 'index'])->name('admin.audit');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::inertia('/about','About', ['user' => 'John Doe'])->name('about');

    //Route::inertia('/campaign','Campaign')->name('campaign');
    Route::get('/campaign', [CampaignController::class, 'index'])->name('campaign');
    Route::post('subscribe', [LeadsController::class, 'store'])->name('subscribe')->middleware('throttle:5,1');

    Route::inertia('/form', 'Auth/Form')->name('form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});



