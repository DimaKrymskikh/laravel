<?php

use App\Http\Controllers\Project\Admin\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Project\Auth\Account\UserFilmsController;
use App\Http\Controllers\Project\Auth\Account\UserLogsWeatherController;
use App\Http\Controllers\Project\Auth\Account\UserWeatherController;
use App\Http\Controllers\Project\Auth\HomeController;
use App\Http\Controllers\Project\Auth\Content\CityController;
use App\Http\Controllers\Project\Auth\Content\FilmController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

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
    
    Route::post('admin/create', [AdminController::class, 'create'])->middleware('check.password');
    
    Route::get('/', [HomeController::class, 'index']);
    
    Route::get('cities', [CityController::class, 'index']);
    Route::post('cities/addcity/{city_id}', [CityController::class, 'addCity']);
    Route::delete('cities/removecity/{city_id}', [CityController::class, 'removeCity'])->middleware('check.password');
    
    Route::get(RouteServiceProvider::URL_AUTH_FILMS, [FilmController::class, 'index']);
    
    Route::get(RouteServiceProvider::URL_AUTH_USERFILMS, [UserFilmsController::class, 'create']);
    Route::post(RouteServiceProvider::URL_AUTH_USERFILMS.'/addfilm/{film_id}', [UserFilmsController::class, 'addFilm']);
    Route::delete(RouteServiceProvider::URL_AUTH_USERFILMS.'/removefilm/{film_id}', [UserFilmsController::class, 'removeFilm'])->middleware('check.password');
    Route::get(RouteServiceProvider::URL_AUTH_USERFILMS.'/{film_id}', [UserFilmsController::class, 'show']);
    
    Route::get('userweather', [UserWeatherController::class, 'index']);
    Route::post('userweather/refresh/{city_id}', [UserWeatherController::class, 'refresh']);
    
    Route::get('userlogsweather/{city_id}', [UserLogsWeatherController::class, 'index']);
    
    Route::get('token', [TokenController::class, 'index']);
    Route::post('token', [TokenController::class, 'create']);
});
