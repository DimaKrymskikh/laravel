<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Dvdrental\CommonController;
use App\Http\Controllers\Dvdrental\AccountController;
use App\Http\Controllers\Dvdrental\FilmCardController;

use Illuminate\Support\Facades\Route;

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
//    Route::get('verify-email', [VerifyEmailController::class, 'notice'])
//                ->name('verification.notice');
    
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('verify-email', [VerifyEmailController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');
    
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
    
    Route::delete('register', [RegisteredUserController::class, 'remove'])->middleware('check.password');
    
    Route::get('account', [AccountController::class, 'create'])
                ->name('account');
    
    Route::post('account/addfilm/{film_id}', [AccountController::class, 'addFilm']);
    
    Route::delete('account/removefilm/{film_id}', [AccountController::class, 'removeFilm'])->middleware('check.password');
    
    Route::get('filmcard/{film_id}', [FilmCardController::class, 'create'])
                ->name('filmcard');
});

/**
 * Просмотр писем и оповещений в браузере
 */
//Route::get('/notification', function () {
// 
//    return (new \Illuminate\Auth\Notifications\VerifyEmail())
//                ->toMail(new class {
//                    public function getKey() {
//                        return 'aaa';
//                    }
//                    public function getEmailForVerification() {
//                        return 'bbb';
//                    }
//                });
//});

//Route::get('/notification', function () {
//    $title = \App\Models\Dvd\Film::select('title')
//            ->where('id', 28)
//            ->first()->title;
//    $login = \App\Models\User::select('login')
//            ->where('id', 9)
//            ->first()->login;
//
//    return (new \App\Notifications\Dvdrental\AddFilmNotification())
//                ->toMail((object) [
//                    'login' => $login,
//                    'title' => $title
//                ]);
//});
