<?php

use App\Http\Controllers\Api\FilmsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/films-list', [FilmsController::class, 'getFilms']);
Route::middleware('auth:sanctum')->get('/film-card/{film_id}', [FilmsController::class, 'getFilm']);
