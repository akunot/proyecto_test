<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\flaskController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\emailController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware(['auth:api', 'role:admin'])->group(function () {
    /*Route::post('/logout', [AuthController::class, 'logout']);*/

    Route::post('/email', [emailController::class, 'enviarCorreo']);

    /*Route::get('/comments', [flaskController::class, 'index']);
    Route::post('/comments', [flaskController::class, 'store']);
    Route::get('/comments/{id}', [flaskController::class, 'show']);*/
    Route::put('/comments/{id}', [flaskController::class, 'update']);
    Route::delete('/comments/{id}', [flaskController::class, 'destroy']);

    
    /*Route::post('/notfication', [TwilioController::class, 'notification'])->name('send-sms');
    Route::get('/user', function (){
        return 3; 
    }); */
});

Route::middleware(['auth:api', 'role:user|admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/comments', [flaskController::class, 'index']);
    Route::post('/comments', [flaskController::class, 'store']);
    Route::get('/comments/{id}', [flaskController::class, 'show']);
});


