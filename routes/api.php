<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\CommentController;
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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantRoomEquipController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\UploadController;
use App\Models\TenantRoomEquip;
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
        //motel controller
        Route::get('getMotelRoomType/{motelId}', [MotelController::class, 'getMotelRoomType']);
        Route::get('getInfoShareMotel', [MotelController::class, 'getInfoShareMotel']);
        Route::get('getMotels', [MotelController::class, 'getMotels']);
        Route::post('updateMotelInfor', [MotelController::class, 'updateMotelInfor']);
        Route::post('updateMotelImg', [MotelController::class, 'updateMotelImg']);
        Route::Post('findMotel', [MotelController::class, 'findMotel']);
        Route::Post('deleteMotel', [MotelController::class, 'adminDeleteMotel']);
        Route::post('motelRegisterOne', [UserController::class, 'motelRegisterOne']);

        //user controller
        Route::get('findUser/{email}', [UserController::class, 'findUser']);
        Route::post('logoutAllDevice', [UserController::class, 'logoutAllDevice']);
        Route::get('getUser', [UserController::class, 'getUser']);  // -> user (response);
        Route::post('updateUP', [UserController::class, 'updateUP']);
        Route::post('updateAccount', [UserController::class, 'updateAccount']);
        Route::put('updateUser/{userId}', [UserController::class, 'updateUser']);
        Route::put('deleteUser/{userId}', [UserController::class, 'deleteUser']);
        //role
        Route::get('roles/{role}', [RoleController::class, 'index']);
        //roomStatuses
        Route::get('roomStatuses', [RoomStatusController::class, 'roomStatuses']);
        //roomController
        Route::put('updateRoomStatus/{id}', [RoomController::class, 'updateRoomStatus']);
        Route::post('intoRoom', [RoomController::class, 'intoRoom']);
        Route::post('outRoom', [RoomController::class, 'outRoom']);
        Route::post('adminOutRoom', [RoomController::class, 'adminOutRoom']);
        Route::get('getRoom/{roomId}', [RoomController::class, 'getRoom']);
        //noticontroller
        Route::post('isReadNoti',[NotiController::class,'isReadNoti']);
        Route::get('countIntoNoti',[NotiController::class,'countIntoNoti']);
        Route::post('sendReject',[NotiController::class, 'sendReject']);
        Route::post('changeIntoStatus',[NotiController::class, 'changeIntoStatus']);
        Route::post('getRoomInto',[NotiController::class, 'getRoomInto']);
        Route::get('getIntoNoti',[NotiController::class, 'getIntoNoti']);
        Route::post('sendInvite', [NotiController::class, 'sendInvite']);
        Route::get('getAllNoti', [NotiController::class, 'getAllNoti']);
        Route::get('getAllOutbox', [NotiController::class, 'getAllOutbox']);
        Route::get('countNoti', [NotiController::class, 'countNoti']);
        Route::post('sendReport', [NotiController::class, 'sendReport']);
        Route::post('findNoti', [NotiController::class, 'findNoti']);
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
        Route::get('getRoomTypeImgs/{motelId}', [RoomTypeController::class, 'getRoomTypeImgs']);
        Route::post('addNumRoom', [RoomTypeController::class, 'addNumRoom']);
        Route::post('updateRoomTypeContent', [RoomTypeController::class, 'updateRoomTypeContent']);
        Route::post('createRoomType', [RoomTypeController::class, 'createRoomType']);
        Route::post('deleteRoomType', [RoomTypeController::class, 'deleteRoomType']);
        //tenant room equip controller
        Route::post('eqCreate' , [TenantRoomEquipController::class, 'eqCreate']);
        Route::get('getAllTRE/{motelId}/{order?}/{from?}/{to?}',[TenantRoomEquipController::class,'getAllTRE']);
        Route::post('getAllTRE',[TenantRoomEquipController::class,'getAllTRE']);
        Route::get('getTenantRoomEquips', [TenantRoomEquipController::class, 'getTenantRoomEquips']);
        Route::post('deleteTenantRoomEquip', [TenantRoomEquipController::class, 'deleteTenantRoomEquip']);
        Route::post('createTenantRoomEquips', [TenantRoomEquipController::class, 'createTenantRoomEquips']);
        Route::post('tREStatus', [TenantRoomEquipController::class, 'tREStatus']);
        //tenant user controller
        Route::get('getTenantUsers/{tenantId}', [TenantUserController::class, 'getTenantUsers']);
        Route::get('getInfoShare', [TenantUserController::class, 'getInfoShare']);
        Route::get('changeInfoShare/{tenant_user_id}', [TenantUserController::class, 'changeInfoShare']);

        //postController
        Route::get('getPostConpound', [PostController::class, 'getPostConpound']);
        Route::post('createPostUser', [PostController::class, 'createPostUser']);
        Route::post('changeStatusConpound', [PostController::class, 'changeStatusConpound']);
        Route::post('deleteConpound', [PostController::class, 'deleteConpound']);
        Route::post('sendIntoNoti', [PostController::class, 'sendIntoNoti']);
        Route::post('sendIntoNotiRoom', [PostController::class, 'sendIntoNotiRoom']);
        Route::post('changeStatusPost', [PostController::class, 'changeStatusPost']);
        Route::get('getPostMotel/{motelId}', [PostController::class, 'getPostMotel']);
        Route::post('createPostMotel', [PostController::class, 'createPostMotel']);
        //billController
        Route::get('getBillAllRoom/{motelId}', [BillController::class, 'getBillAllRoom']);
        Route::post('createAllBill/{motelId}', [BillController::class, 'createAllBill']);
        Route::post('createSomeBill', [BillController::class, 'createSomeBill']);
        Route::post('updateBillNum', [BillController::class, 'updateBillNum']);
        Route::post('updateBillStatus', [BillController::class, 'updateBillStatus']);
        Route::get('sendNotiBill/{bill_id}', [BillController::class, 'sendNotiBill']);
        Route::get('sendAllNotiBill/{motel_id}', [BillController::class, 'sendAllNotiBill']);
        Route::get('getBillOwnRoom', [BillController::class, 'getBillOwnRoom']);
        Route::post('sendBillYes', [BillController::class, 'sendBillYes']);
        Route::post('sendBillError', [BillController::class, 'sendBillError']);
        //motelImgController
        Route::get('getMotelImgs/{motelId}', [MotelImgController::class, 'getMotelImgs']);
        //imgDetailcontroller

        //commentController
        Route::post('createComment', [CommentController::class, 'createComment']);
        //uploadController
        Route::post('uploadImg', [UploadController::class, 'uploadImg']);
        Route::post('uploadRoomImg', [UploadController::class, 'uploadRoomImg']);
    }
);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()
    ]);
});

Route::post('uploadImgTenantRoomEquip', [TenantRoomEquipController::class, 'uploadImgTenantRoomEquip']);
Route::get('getNotiRoom/{roomId}', [RoomController::class, 'getNotiRoom']);
Route::get('image/{filename}', [ImgDetailController::class, 'image']);
Route::get('detailPost/{post_id}', [PostController::class, 'detailPost']);
Route::get('getMapMotels', [MotelController::class, 'getMapMotels']);
Route::post('findTinh', [MotelController::class, 'findTinh']);
Route::post('getPostMotels', [MotelController::class, 'getPostMotels']);

Route::get('getAllComment/{post_id}', [CommentController::class, 'getAllComment']);

//postController
Route::get('getPost', [PostController::class, 'getPost']);
Route::post('searchPost', [PostController::class, 'searchPost']);

//user controller
Route::get('getAllUser', [UserController::class, 'getAllUser']);
//motel controller
Route::get('getAllMotel', [MotelController::class, 'getAllMotel']);
Route::get('adminGetMotel/{motelId}', [MotelController::class, 'adminGetMotel']);


