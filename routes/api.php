<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MotelController;
use Database\Seeders\UserSeeder;
use App\Http\Controllers\RoleController;

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
// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

//User
Route::post('/login', [UserController::class, 'login']);
Route::post('userRegister', [UserController::class, 'userRegister']);
Route::post('motelRegister', [UserController::class, 'motelRegister']);
Route::get('/ooo', function () {
    return response('sldkfjs', 200);
});
//Motel
Route::middleware(['auth:sanctum'])->group(
    function () {
        Route::get('getMotelRoomType', [MotelController::class, 'getMotelRoomType']);

    }
);
// Route::get('getMotelRoomType/{motelId}', [MotelController::class, 'getMotelRoomType']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()
    ]);
});

//role
Route::get('roles/{role}', [RoleController::class, 'index']);
