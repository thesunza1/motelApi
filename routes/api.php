<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ImgDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MotelController;
use App\Http\Controllers\MotelImgController;
use App\Http\Controllers\RoomStatusController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\NotiController;
use App\Http\Controllers\NotiTypeController;
use App\Http\Controllers\PostController;
use Database\Seeders\UserSeeder;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantRoomEquipController;
use App\Http\Controllers\TenantUserController;
use App\Models\TenantRoomEquip;
use App\Models\User;
use Illuminate\Routing\Router;

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
        Route::post('intoRoom', [RoomController::class, 'intoRoom']);
        //notiController
        Route::post('sendInvite', [NotiController::class, 'sendInvite']);
        //noti coontroller
        Route::get('getAllNoti', [NotiController::class, 'getAllNoti']);
        Route::get('countNoti', [NotiController::class, 'countNoti']);
        //notitype controller
        Route::get('notiType', [NotiTypeController::class, 'notiType']);
        Route::post('sendNoti', [NotiController::class, 'sendNoti']);
        Route::get('isSeen/{notiId}', [NotiController::class, 'isSeen']);
        //tenant Controller
        Route::get('getTenant', [TenantController::class, 'getTenant']);
        Route::get('getNumRoom', [TenantController::class, 'getNumRoom']);
        Route::post('updateNumRoom', [TenantController::class, 'UpdateNumRoom']);
        Route::get('getTenantUser/{room_id}', [TenantController::class, 'getTenantUser']);
        Route::get('confirmEq/{tenant_id}', [TenantController::class, 'confirmEq']);
        Route::get('confirmNum/{tenant_id}', [TenantController::class, 'confirmNum']);

        //RoomType Controller
        Route::get('getRoomTypeUser', [RoomTypeController::class, 'getRoomTypeUser']);
        Route::get('getRoomTypeImgs', [RoomTypeController::class, 'getRoomTypeImgs']);
        //tenant room equip controller
        Route::get('getTenantRoomEquips', [TenantRoomEquipController::class, 'getTenantRoomEquips']);
        Route::post('deleteTenantRoomEquip', [TenantRoomEquipController::class, 'deleteTenantRoomEquip']);
        Route::post('createTenantRoomEquips', [TenantRoomEquipController::class, 'createTenantRoomEquips']);
        //tenant user controller
        Route::get('getTenantUsers/{tenantId}', [TenantUserController::class, 'getTenantUsers']);
        Route::get('getInfoShare', [TenantUserController::class, 'getInfoShare']);
        Route::get('changeInfoShare/{tenant_user_id}', [TenantUserController::class, 'changeInfoShare']);

        //postController
        Route::get('getPostConpound', [PostController::class, 'getPostConpound']);
        Route::post('createPostUser', [PostController::class, 'createPostUser']);
        Route::post('changeStatusConpound', [PostController::class, 'changeStatusConpound']);
        Route::post('deleteConpound', [PostController::class, 'deleteConpound']);
        //billController
        Route::get('getBillAllRoom', [BillController::class, 'getBillAllRoom']);
        Route::post('createAllBill', [BillController::class, 'createAllBill']);
        Route::post('createSomeBill', [BillController::class, 'createSomeBill']);
        Route::post('updateBillNum', [BillController::class, 'updateBillNum']);
        Route::post('updateBillStatus', [BillController::class, 'updateBillStatus']);
        Route::get('sendNotiBill/{bill_id}', [BillController::class, 'sendNotiBill']);
        Route::get('sendAllNotiBill/{motel_id}', [BillController::class, 'sendAllNotiBill']);
        Route::get('getBillOwnRoom', [BillController::class, 'getBillOwnRoom']);
        Route::post('sendBillYes', [BillController::class, 'sendBillYes']);
        Route::post('sendBillError', [BillController::class, 'sendBillError']);
        //motelImgController
        Route::get('getMotelImgs', [MotelImgController::class, 'getMotelImgs']);
        //imgDetailcontroller
    }
);
// Route::get('getMotelRoomType/{motelId}', [MotelController::class, 'getMotelRoomType']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()
    ]);
});

Route::get('getNotiRoom/{roomId}', [RoomController::class, 'getNotiRoom']);

Route::get('image/{filename}', [ImgDetailController::class, 'image']);
