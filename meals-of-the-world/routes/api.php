<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\MealController;

Route::get('/meals', [MealController::class, 'index'])->middleware('api');

