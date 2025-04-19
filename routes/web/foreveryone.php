<?php

use App\Http\Controllers\Project\Admin\Content\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('languages/getJson', [LanguageController::class, 'getJson']);
