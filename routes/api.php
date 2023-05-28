<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([

    'middleware' => 'auth.jwt',
    'prefix' => 'auth'

], function () {
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::get('getprofile', [AuthController::class,'getprofile']);
    Route::get('getfriend', [AuthController::class,'getfriend']);
    Route::post('upload', [AuthController::class,'upload']);
    Route::post('delete', [AuthController::class,'delete_image']);
    Route::post('editprofile', [AuthController::class,'editprofile']);
    Route::post('editpassword', [AuthController::class,'editpassword']);
    Route::post('sendNotification', [App\Http\Controllers\UserController::class,'sendNotification']);
});

Route::post('auth/login', [AuthController::class,'login']);
Route::post('auth/register', [AuthController::class,'register']);
Route::get('auth/getschools', [AuthController::class,'getschools']);