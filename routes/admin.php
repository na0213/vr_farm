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
use App\Http\Controllers\Backend\FarmController;
use App\Http\Controllers\Backend\AnimalController;
use App\Http\Controllers\Backend\StoreController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\PointController;

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

    Route::controller(FarmController::class)->group(function () {
        Route::get('/farms', 'index')->name('backend.farms.index');
        Route::get('/farms/create/{owner}', 'create')->name('backend.farms.create');
        Route::post('/farms/{owner}', 'store')->name('backend.farms.store');
        Route::get('/farms/{id}/show', 'show')->name('backend.farms.show');
        Route::get('/farms/{id}/edit', 'edit')->name('backend.farms.edit');
        Route::put('/farms/{id}', 'update')->name('backend.farms.update');
        Route::delete('/farms/{id}', 'destroy')->name('backend.farms.destroy');
        Route::get('/farms/{id}/images', 'images')->name('backend.farms.images');
        Route::post('/farms/{id}/images', 'storeImages')->name('backend.farms.storeImages');
        Route::get('/farms/{farmId}/edit-images', 'editImages')->name('admin.backend.farms.editImages');
        Route::post('/farms/{farmId}/update-image/{imageId}', 'updateImage')->name('backend.farms.updateImage');
        Route::delete('/farms/{farmId}/delete-image/{imageId}', 'deleteImage')->name('backend.farms.deleteImage');

        });

    Route::controller(AnimalController::class)->group(function () {
        Route::get('/animals/create/{farm}', 'create')->name('backend.animals.create');
        Route::post('/animals/{farm}', 'store')->name('backend.animals.store');
        Route::get('/animals/{id}/show', 'show')->name('backend.animals.show');
        Route::get('/animals/{id}/edit', 'edit')->name('backend.animals.edit');
        Route::put('/animals/{id}', 'update')->name('backend.animals.update');
        Route::delete('/animals/{id}', 'destroy')->name('backend.animals.destroy');
        });

    Route::controller(ProductController::class)->group(function () {
        Route::get('/products/create/{farm}', 'create')->name('backend.products.create');
        Route::post('/products/{farm}', 'store')->name('backend.products.store');
        Route::get('/products/{id}/show', 'show')->name('backend.products.show');
        Route::get('/products/{id}/edit', 'edit')->name('backend.products.edit');
        Route::put('/products/{id}', 'update')->name('backend.products.update');
        Route::delete('/products/{id}', 'destroy')->name('backend.products.destroy');
        });

    Route::controller(StoreController::class)->group(function () {
        Route::get('/stores/create/{farm}', 'create')->name('backend.stores.create');
        Route::post('/stores/{farm}', 'store')->name('backend.stores.store');
        Route::get('/stores/{id}/show', 'show')->name('backend.stores.show');
        Route::get('/stores/{id}/edit', 'edit')->name('backend.stores.edit');
        Route::put('/stores/{id}', 'update')->name('backend.stores.update');
        Route::delete('/stores/{id}', 'destroy')->name('backend.stores.destroy');
        });

    Route::controller(PointController::class)->group(function () {
        Route::get('/points/create/{farm}', 'create')->name('backend.points.create');
        Route::post('/points/{farm}', 'store')->name('backend.points.store');
        Route::get('/points/{id}/show', 'show')->name('backend.points.show');
        Route::get('/points/{id}/edit', 'edit')->name('backend.points.edit');
        Route::put('/points/{id}', 'update')->name('backend.points.update');
        Route::delete('/points/{id}', 'destroy')->name('backend.points.destroy');
        });
});
