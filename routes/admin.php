<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::controller(CategoryController::class)->prefix('categories')->name('categories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{category}/edit', 'edit')->name('edit');
        Route::put('/{category}', 'update')->name('update');
        Route::delete('/{category}', 'destroy')->name('destroy');
    });

    Route::controller(PublisherController::class)->prefix('publishers')->name('publishers.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{publisher}/edit', 'edit')->name('edit');
        Route::put('/{publisher}', 'update')->name('update');
        Route::delete('/{publisher}', 'destroy')->name('destroy');
    });

    Route::resource('books', AdminBookController::class)->names('books');
});
