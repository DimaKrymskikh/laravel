<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dvdrental\CommonController;

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
