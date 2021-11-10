<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\UserController;

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

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->prefix('admin')->group(function () {
    Route::get('send-invitation/{email}', [UserController::class, 'sendInvitation']);
});

Route::middleware(['auth:api'])->prefix('users')->group(function () {
    Route::post('creater-username-password', [UserController::class, 'createUsernamePassword']);
    Route::post('confirm-pin', [UserController::class, 'confirmUserPin']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::post('/logout', [UserController::class, 'logout']);
});