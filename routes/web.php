<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/farm/map', [GuestController::class, 'index'])->name('farm.index');
Route::get('/farm/{id}', [GuestController::class, 'show'])->name('farm.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/history', [HistoryController::class, 'index'])->name('user.history.show');
    // 履歴表示ページ
    Route::get('/link/{point_id}', [HistoryController::class, 'show'])->name('user.link');
    // 履歴登録
    Route::post('/links/{point_id}', [HistoryController::class, 'store'])->name('user.links.store');

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('user.farm.index');
        Route::get('/users/{id}/show', 'show')->name('user.farm.show');
        Route::post('/farms/{farm}/favorite', 'toggleFavorite')->name('farms.toggleFavorite');
        Route::get('/favorites', 'favorites')->name('user.favorites');
    });

});

require __DIR__.'/auth.php';
