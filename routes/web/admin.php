<?php

use App\Http\Controllers\Project\Admin\AdminController;
use App\Http\Controllers\Project\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Project\Admin\Content\ActorController;
use App\Http\Controllers\Project\Admin\Content\CityController as AdminCityController;
use App\Http\Controllers\Project\Admin\Content\FilmActorController;
use App\Http\Controllers\Project\Admin\Content\FilmController as AdminFilmController;
use App\Http\Controllers\Project\Admin\Content\LanguageController;
use App\Http\Controllers\Project\Admin\TimezoneController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'all.action'])->group(function () {
    Route::get('admin', [AdminHomeController::class, 'index']);
    Route::post('admin/destroy', [AdminController::class, 'destroy'])->middleware('check.password');
    
    Route::resource(RouteServiceProvider::URL_ADMIN_CITIES, AdminCityController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::put(RouteServiceProvider::URL_ADMIN_CITIES.'/{city_id}/timezone/{timezone_id}', [AdminCityController::class, 'setTimezone']);
    
    Route::get(RouteServiceProvider::URL_ADMIN_TIMEZONE, [TimezoneController::class, 'index']);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_LANGUAGES, LanguageController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::get(RouteServiceProvider::URL_ADMIN_LANGUAGES.'/getJson', [LanguageController::class, 'getJson']);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_ACTORS, ActorController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_FILMS, AdminFilmController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::get(RouteServiceProvider::URL_ADMIN_FILMS.'/getActorsList/{film_id}', [AdminFilmController::class, 'getActorsList']);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_FILMS.'/actors', FilmActorController::class)->only([
        'index', 'store', 'destroy'
    ]);
});
