<?php

use App\Http\Controllers\API\messageController;
use App\Http\Controllers\API\resepController;
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

Route::get('tes', [messageController::class,'index']);
Route::post('send', [messageController::class,'store']);
Route::get('reseps', [resepController::class,'index']);
Route::get('reseps/{slug}', [resepController::class,'show']);
Route::post('reseps', [resepController::class,'addResep']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
