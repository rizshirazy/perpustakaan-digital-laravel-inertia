<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FineController;
use App\Http\Controllers\Admin\FineSettingController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\ReturnBookController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('categories', CategoryController::class)->names('categories');
    Route::resource('publishers', PublisherController::class)->names('publishers');
    Route::resource('books', BookController::class)->names('books');
    Route::resource('users', UserController::class)->names('users');

    Route::controller(FineSettingController::class)->prefix('fine-settings')->name('fine-settings.')->group(function () {
        Route::get('/update', 'edit')->name('edit');
        Route::put('/update', 'update')->name('update');
    });

    Route::resource('loans', LoanController::class)->names('loans');

    Route::controller(ReturnBookController::class)->prefix('return-books')->name('return-books.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{loan:loan_code}/create', 'create')->name('create');
        Route::post('/{loan:loan_code}/create', 'store')->name('store');
        Route::put('/{returnBook:return_book_code}/approve', 'approve')->name('approve');
    });

    Route::controller(FineController::class)->prefix('fines')->name('fines.')->group(function () {
        Route::get('/fines/{returnBook:return_code}', 'show')->name('show');
    });
});
