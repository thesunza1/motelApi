<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MotelController;
use App\Http\Controllers\RoomStatusController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\NotiController;
use Database\Seeders\UserSeeder;
use App\Http\Controllers\RoleController;
use App\Models\User;

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
        Route::get('findUser/{id}', [UserController::class, 'findUser']);
        //role
        Route::get('roles/{role}', [RoleController::class, 'index']);
        //roomStatuses
        Route::get('roomStatuses', [RoomStatusController::class, 'roomStatuses']);
        //roomController
        Route::put('updateRoomStatus/{id}', [RoomController::class, 'updateRoomStatus']);
        //notiController
        Route::post('sendInvite', [NotiController::class, 'sendInvite']);
        //noti coontroller
        Route::get('getAllNoti', [NotiController::class, 'getAllNoti']);
        Route::get('countNoti', [NotiController::class, 'countNoti']);
    }
);
// Route::get('getMotelRoomType/{motelId}', [MotelController::class, 'getMotelRoomType']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()
    ]);
});
