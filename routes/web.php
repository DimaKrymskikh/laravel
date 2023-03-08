<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Dvdrental\CommonController;
use App\Http\Controllers\Dvdrental\AccountController;
use App\Http\Controllers\Dvdrental\FilmCardController;

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

Route::get('/', [CommonController::class, 'home'])->name('home');
Route::get('/catalog', [CommonController::class, 'catalog'])->name('catalog');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
    
    Route::get('account', [AccountController::class, 'create'])
                ->name('account');
    
    Route::post('account/addfilm/{film_id}', [AccountController::class, 'addFilm']);
    
    Route::delete('account/removefilm/{film_id}', [AccountController::class, 'removeFilm']);
    
    Route::get('filmcard/{film_id}', [FilmCardController::class, 'create'])
                ->name('filmcard');
});
