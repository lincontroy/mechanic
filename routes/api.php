<?php

use Illuminate\Http\Request;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\RequestController;
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

Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class,'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/update_loc', [AuthController::class,'update_loc']);
    Route::post('/get_mechanic', [AuthController::class,'get_mechanic']);
    Route::post('/create_request', [RequestController::class,'store']);
   

});
