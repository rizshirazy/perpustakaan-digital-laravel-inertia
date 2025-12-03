<?php

use App\Http\Controllers\BookFrontController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::controller(DashboardController::class)->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', 'index')->name('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
    });

    Route::controller(BookFrontController::class)->middleware('role:member')->prefix('books')->name('front-books.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{book:slug}', 'show')->name('show');
    });
});


require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
