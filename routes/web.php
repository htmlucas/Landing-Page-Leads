<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/about','About', ['user' => 'John Doe'])->name('about');

Route::inertia('/form', 'Auth/Form')->name('form');

Route::post('/register', [AuthController::class, 'register']);