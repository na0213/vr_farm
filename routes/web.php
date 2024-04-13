<?php

use App\Http\Controllers\DlshopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\NoteController;

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

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [GuestController::class, 'top']);
Route::get('/farm/map', [GuestController::class, 'index'])->name('farm.index');
Route::get('/farm/{id}', [GuestController::class, 'show'])->name('farm.show');
Route::get('/farm/{farm}/community', [GuestController::class, 'communityIndex'])->name('farm.community');

Route::get('/farm/{farm}/qrs', [DlshopController::class, 'index'])->name('qrs.index');
Route::get('/farm/{farm}/qr/show', [DlshopController::class, 'show'])->name('qrs.show');
Route::post('/farm/{farm}/qr', [DlshopController::class, 'store'])->name('qrs.store');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [UserController::class, 'top'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

    Route::controller(PostController::class)->group(function () {
        Route::get('/community/{farm}', 'index')->name('user.community.index');
        Route::post('/posts', 'store')->name('posts.store');
        Route::delete('/posts/{post}', 'destroy')->name('posts.destroy');
    });

    Route::controller(MypageController::class)->group(function () {
        Route::get('/user/mypage/create', 'create')->name('user.mypage.create');
        Route::post('/user/mypage/store', 'store')->name('user.mypage.store');
        Route::get('/mypage', 'show')->name('user.mypage.show');
        Route::get('/mypage/{mypage}/edit', 'edit')->name('user.mypage.edit');
        Route::put('/mypage/{mypage}', 'update')->name('user.mypage.update');
        Route::delete('/user/mypage/{mypage}', 'destroy')->name('user.mypage.destroy');
        Route::get('/user/{mypage}/public_show', 'publicShow')->name('user.mypage.public_show');
        Route::post('/mypage/{mypage}/follow', 'follow')->name('mypage.follow');
        Route::delete('/mypage/{mypage}/unfollow', 'unfollow')->name('mypage.unfollow');
    });
    
    Route::controller(NoteController::class)->group(function () {
        Route::get('/user/note/create', 'create')->name('user.note.create');
        Route::post('/user/note/store', 'store')->name('user.note.store');
        Route::get('/note/{note}', 'show')->name('user.note.show');
        Route::get('/note/{note}/edit', 'edit')->name('user.note.edit');
        Route::put('/note/{note}', 'update')->name('user.note.update');
        Route::delete('/user/note/{note}', 'destroy')->name('user.note.destroy');
        Route::get('/user/{note}/public', 'publicShow')->name('user.note.public');
    });
});

require __DIR__.'/auth.php';
