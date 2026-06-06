<?php

use App\Http\Controllers\CorrespondenceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/verify/{uuid}', [CorrespondenceController::class, 'verify'])
    ->name('correspondences.verify');

Route::get('/dashboard', function () {
    return redirect()->route('correspondences.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/correspondences', [CorrespondenceController::class, 'index'])
        ->name('correspondences.index');

    Route::get('/correspondences/{correspondence}', [CorrespondenceController::class, 'show'])
        ->name('correspondences.show');

    Route::get('/correspondences/{correspondence}/download', [CorrespondenceController::class, 'download'])
        ->name('correspondences.download');

    Route::middleware('role:creator,checker')->group(function () {
        Route::get('/correspondences/create/new', [CorrespondenceController::class, 'create'])
            ->name('correspondences.create');

        Route::post('/correspondences', [CorrespondenceController::class, 'store'])
            ->name('correspondences.store');
    });

    Route::post('/correspondences/{correspondence}/approve', [CorrespondenceController::class, 'approve'])
        ->middleware('role:checker')
        ->name('correspondences.approve');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
