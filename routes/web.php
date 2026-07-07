<?php

use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SitemapController;

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

Route::get('/', [GuestController::class, 'top'])->name('index');
Route::get('/farm/map', [GuestController::class, 'index'])->name('farm.index');
Route::get('/farm/{id}', [GuestController::class, 'show'])->name('farm.show');
Route::get('/products', [GuestController::class, 'products'])->name('products.index');
Route::get('/animal-welfare', [GuestController::class, 'welfare'])->name('welfare.index');
Route::get('/article/{id}', [GuestController::class, 'showArticle'])->name('article.show');
Route::get('/about', [GuestController::class, 'about'])->name('about.index');
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('contact', [ContactController::class, 'formTop'])->name('contact.form');
Route::post('contact/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');
Route::post('contact/send', [ContactController::class, 'SendProcess'])->name('contact.send');
