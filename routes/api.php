<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AppointmentStatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::post('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('/users/search', [UserController::class, 'search']);
Route::post('/users/{id}/change-role', [UserController::class, 'changeRole']);
Route::delete('/users', [UserController::class, 'bulkDelete']);

Route::get('/appointments', [AppointmentController::class, 'index']);
Route::get('/appointment-status', [AppointmentStatusController::class, 'getStatusWithCount']);
