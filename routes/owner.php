<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Owner\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Owner\ConfirmablePasswordController;
use App\Http\Controllers\Auth\Owner\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\Owner\EmailVerificationPromptController;
use App\Http\Controllers\Auth\Owner\NewPasswordController;
use App\Http\Controllers\Auth\Owner\PasswordController;
use App\Http\Controllers\Auth\Owner\PasswordResetLinkController;
use App\Http\Controllers\Auth\Owner\RegisteredUserController;
use App\Http\Controllers\Auth\Owner\VerifyEmailController;
use App\Http\Controllers\Backend\MasterprofileController;
use App\Http\Controllers\Backend\MasterController;
use App\Http\Controllers\Backend\OwnerpostController;

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
    return view('backend.ownerwelcome');
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

Route::middleware('auth:owners')->group(function () {

    Route::get('/profile', [MasterprofileController::class, 'edit'])->name('backend.masters.profile.edit');
    Route::patch('/profile', [MasterprofileController::class, 'update'])->name('backend.masters.profile.update');
    Route::delete('/profile', [MasterprofileController::class, 'destroy'])->name('backend.masters.profile.destroy');

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
        
    Route::controller(MasterController::class)->group(function () {
        Route::get('/ownerdashboard', 'index')->middleware('verified')->name('ownerdashboard');
        Route::get('/owners/{id}/show', 'show')->name('backend.masters.show');
        Route::get('/owners/{id}/edit', 'edit')->name('backend.masters.edit');
        Route::put('/owners/{id}', 'update')->name('backend.masters.update');
        Route::get('/farm/{farm}/posts', 'posts')->name('backend.masters.posts');
    });

    Route::controller(OwnerpostController::class)->group(function () {
        Route::post('/ownerposts/store', 'store')->name('ownerposts.store');
        Route::delete('/ownerposts/{ownerpost}', 'destroy')->name('ownerposts.destroy');
        Route::get('/ownerposts/{mypage}/usershow', 'usershow')->name('ownerposts.usershow');
    });

});
