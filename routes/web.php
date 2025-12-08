<?php

use App\Http\Controllers\BookFrontController;
use App\Http\Controllers\CategoryFrontController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FineFrontController;
use App\Http\Controllers\LoanFrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnBookFrontController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', 'login');

Route::controller(DashboardController::class)->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
    });

    Route::controller(BookFrontController::class)->middleware('role:member')->prefix('books')->name('front.books.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{book:slug}', 'show')->name('show');
    });

    Route::controller(CategoryFrontController::class)->middleware('role:member')->prefix('categories')->name('front.categories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{category:slug}', 'show')->name('show');
    });

    Route::controller(LoanFrontController::class)->middleware('role:member')->prefix('loans')->name('front.loans.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{loan:loan_code}/detail', 'show')->name('show');
        Route::post('/{book:slug}/create', 'store')->name('store');
    });

    Route::controller(ReturnBookFrontController::class)->middleware('role:member')->prefix('return-books')->name('front.return-books.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{returnBook:return_code}/detail', 'show')->name('show');
        Route::post('/{book:slug}/create/{loan:loan_code}', 'store')->name('store');
    });

    Route::get('fines', FineFrontController::class)->middleware('role:member')->name('front.fines.index');
});


require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
