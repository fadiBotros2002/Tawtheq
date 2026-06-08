<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])
    ->whereIn('locale', ['ar', 'en'])
    ->name('locale.switch');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('documents.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/stream', [DocumentController::class, 'stream'])->name('documents.stream');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

Route::get('/{document_name}/{doctype}/{category}/{date}/{sequence}', [DocumentController::class, 'verify'])
    ->where([
        'document_name' => '[a-z][a-z0-9-]*',
        'doctype' => 'inbound|outbound',
        'category' => '[a-z][a-z0-9-]*',
        'date' => '\d{8}',
        'sequence' => '\d{4}',
    ])
    ->name('documents.verify');

Route::get('/{document_name}/{doctype}/{category}/{date}/{sequence}/file', [DocumentController::class, 'verifyStream'])
    ->where([
        'document_name' => '[a-z][a-z0-9-]*',
        'doctype' => 'inbound|outbound',
        'category' => '[a-z][a-z0-9-]*',
        'date' => '\d{8}',
        'sequence' => '\d{4}',
    ])
    ->name('documents.verify.stream');
