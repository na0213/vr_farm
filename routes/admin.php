<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Admin\ConfirmablePasswordController;
use App\Http\Controllers\Auth\Admin\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\Admin\EmailVerificationPromptController;
use App\Http\Controllers\Auth\Admin\NewPasswordController;
use App\Http\Controllers\Auth\Admin\PasswordController;
use App\Http\Controllers\Auth\Admin\PasswordResetLinkController;
use App\Http\Controllers\Auth\Admin\RegisteredUserController;
use App\Http\Controllers\Auth\Admin\VerifyEmailController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\KindController;
use App\Http\Controllers\Backend\KeywordController;
use App\Http\Controllers\Backend\OwnerController;

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
    return view('backend.welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth:admins')->group(function () {

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
        
    Route::controller(AdminController::class)->group(function () {
        Route::get('/dashboard', 'index')->middleware('verified')->name('dashboard');
        });
    
    Route::controller(KindController::class)->group(function () {
        Route::get('/kinds', 'index')->name('backend.kinds.index');
        Route::get('/kinds/create', 'create')->name('backend.kinds.create');
        Route::post('/kinds', 'store')->name('backend.kinds.store');
        Route::get('/kinds/{id}/edit', 'edit')->name('backend.kinds.edit');
        Route::put('/kinds/{id}', 'update')->name('backend.kinds.update');
        Route::delete('/kinds/{id}', 'destroy')->name('backend.kinds.destroy');
        });

    Route::controller(KeywordController::class)->group(function () {
        Route::get('/keywords', 'index')->name('backend.keywords.index');
        Route::get('/keywords/create', 'create')->name('backend.keywords.create');
        Route::post('/keywords', 'store')->name('backend.keywords.store');
        Route::get('/keywords/{id}/edit', 'edit')->name('backend.keywords.edit');
        Route::put('/keywords/{id}', 'update')->name('backend.keywords.update');
        Route::delete('/keywords/{id}', 'destroy')->name('backend.keywords.destroy');
        });

    Route::controller(OwnerController::class)->group(function () {
        Route::get('/owners', 'index')->name('backend.owners.index');
        Route::get('/owners/create', 'create')->name('backend.owners.create');
        Route::post('/owners', 'store')->name('backend.owners.store');
        Route::get('/owners/{id}/show', 'show')->name('backend.owners.show');
        Route::get('/owners/{id}/edit', 'edit')->name('backend.owners.edit');
        Route::put('/owners/{id}', 'update')->name('backend.owners.update');
        Route::delete('/owners/{id}', 'destroy')->name('backend.owners.destroy');
        });
});
