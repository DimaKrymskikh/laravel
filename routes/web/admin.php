<?php

use App\Http\Controllers\Project\Admin\AdminController;
use App\Http\Controllers\Project\Admin\HomeController;
use App\Http\Controllers\Project\Admin\Content\Quizzes\QuizAnswerController;
use App\Http\Controllers\Project\Admin\Content\Quizzes\QuizController;
use App\Http\Controllers\Project\Admin\Content\Quizzes\QuizItemController;
use App\Http\Controllers\Project\Admin\Content\ActorController;
use App\Http\Controllers\Project\Admin\Content\CityController;
use App\Http\Controllers\Project\Admin\Content\FilmActorController;
use App\Http\Controllers\Project\Admin\Content\FilmController;
use App\Http\Controllers\Project\Admin\Content\LanguageController;
use App\Http\Controllers\Project\Admin\TimezoneController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'all.action'])->group(function () {
    Route::get('admin', [HomeController::class, 'index']);
    Route::post('admin/destroy', [AdminController::class, 'destroy'])->middleware('check.password');
    
    Route::resource(RouteServiceProvider::URL_ADMIN_CITIES, CityController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::put(RouteServiceProvider::URL_ADMIN_CITIES.'/{city_id}/timezone/{timezone_id}', [CityController::class, 'setTimezone']);
    
    Route::get(RouteServiceProvider::URL_ADMIN_TIMEZONE, [TimezoneController::class, 'index']);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_LANGUAGES, LanguageController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_ACTORS, ActorController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_FILMS, FilmController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::get(RouteServiceProvider::URL_ADMIN_FILMS.'/getActorsList/{film_id}', [FilmController::class, 'getActorsList']);
    
    Route::resource(RouteServiceProvider::URL_ADMIN_FILMS.'/actors', FilmActorController::class)->only([
        'index', 'store', 'destroy'
    ]);
    
    Route::name('admin.')->group(function () {
        Route::resource('admin/quizzes', QuizController::class)->only([
            'index', 'store', 'show', 'update'
        ]);
        Route::put('admin/quizzes/{id}/set_status', [QuizController::class, 'setFinalStatus'])->name('quizzes.set_status');
        Route::put('admin/quizzes/{id}/cancel_status', [QuizController::class, 'cancelFinalStatus'])->name('quizzes.cancel_status');
        
        Route::resource('admin/quiz_items', QuizItemController::class)->only([
            'store', 'show', 'update'
        ]);
        Route::put('admin/quiz_items/{id}/set_status', [QuizItemController::class, 'setFinalStatus'])->name('quiz_items.set_status');
        Route::put('admin/quiz_items/{id}/cancel_status', [QuizItemController::class, 'cancelFinalStatus'])->name('quiz_items.cancel_status');
        
        Route::resource('admin/quiz_answers', QuizAnswerController::class)->only([
            'store', 'show', 'update', 'destroy'
        ]);
    });
});
