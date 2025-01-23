<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferAndEarnController; 


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile-refer', [ProfileController::class, 'profilerefer'])->name('profile.refer');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/sync-user/{id}', [ReferAndEarnController::class, 'syncUserToUnocuePro'])
    ->name('sync.user');
});
Route::post('/referrals/store', [ReferralController::class, 'store'])->name('referrals.store');

require __DIR__.'/auth.php';
