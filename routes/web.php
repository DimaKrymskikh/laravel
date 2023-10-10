<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

use App\Http\Controllers\Dvdrental\AccountController;
use App\Http\Controllers\Dvdrental\FilmCardController;

use App\Http\Controllers\Project\Admin\AdminController;
use App\Http\Controllers\Project\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Project\Admin\Content\CityController as AdminCityController;
use App\Http\Controllers\Project\Admin\TimezoneController;

use App\Http\Controllers\Project\Auth\HomeController;
use App\Http\Controllers\Project\Auth\Content\CityController;
use App\Http\Controllers\Project\Auth\Content\FilmController;

use App\Http\Controllers\Project\Guest\Content\CityController as GuestCityController;
use App\Http\Controllers\Project\Guest\Content\FilmController as GuestFilmController;
use App\Http\Controllers\Project\Guest\HomeController as GuestHomeController;

use App\Http\Controllers\OpenWeather\WeatherController;

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
    
    Route::get('guest', [GuestHomeController::class, 'index']);
    Route::get('guest/cities', [GuestCityController::class, 'index']);
    Route::get('guest/films', [GuestFilmController::class, 'index']);
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
    
    Route::post('account/getting-token', [AccountController::class, 'getToken']);
    
    Route::post('account/addfilm/{film_id}', [AccountController::class, 'addFilm']);
    
    Route::delete('account/removefilm/{film_id}', [AccountController::class, 'removeFilm'])->middleware('check.password');
    
    Route::get('filmcard/{film_id}', [FilmCardController::class, 'create'])
                ->name('filmcard');
    
    Route::get('weather', [WeatherController::class, 'index']);
    
    Route::post('admin/create', [AdminController::class, 'create'])->middleware('check.password');
    
    Route::get('/', [HomeController::class, 'index']);
    Route::get('cities', [CityController::class, 'index']);
    Route::post('cities/addcity/{city_id}', [CityController::class, 'addCity']);
    Route::get('films', [FilmController::class, 'index']);
});

Route::middleware(['auth', 'all.action'])->group(function () {
    Route::get('admin', [AdminHomeController::class, 'index']);
    Route::post('admin/destroy', [AdminController::class, 'destroy'])->middleware('check.password');
    
    Route::resource('admin/cities', AdminCityController::class)->only([
        'index', 'store', 'update'
    ]);
    Route::delete('admin/cities/{id}', [AdminCityController::class, 'destroy'])->middleware('check.password');
    Route::put('admin/cities/{city_id}/timezone/{timezone_id}', [AdminCityController::class, 'setTimezone']);
    
    Route::get('admin/timezone', [TimezoneController::class, 'index']);
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
