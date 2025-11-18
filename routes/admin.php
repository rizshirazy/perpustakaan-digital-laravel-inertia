<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FineSettingController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\PublisherController;
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
});
