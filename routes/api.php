
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RiddleController;
use App\Http\Controllers\LogicController;
use App\Http\Controllers\EnduranceController;

Route::post('/login', [UserController::class,'login']);
Route::post('/register', [UserController::class,'register']);

// Riddles API
Route::get('/riddles', [RiddleController::class, 'index']);
Route::get('/riddles/{id}', [RiddleController::class, 'show']);
Route::post('/riddles', [RiddleController::class, 'store']);
// Generate a new riddle (AI or fallback)
Route::match(['get', 'post'], '/riddles/generate', [RiddleController::class, 'generate']);
Route::put('/riddles/{id}', [RiddleController::class, 'update']);
Route::delete('/riddles/{id}', [RiddleController::class, 'destroy']);

// Logic Questions API
Route::match(['get', 'post'], '/logic/generate', [LogicController::class, 'generate']);

// Endurance API (50 mixed riddle/logic questions)
Route::match(['get', 'post'], '/endurance/generate', [EnduranceController::class, 'generate']);