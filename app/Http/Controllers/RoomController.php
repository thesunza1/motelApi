<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use App\Models\RoomStatus;
use App\Models\RoomType;
use App\Models\TenantUser;
use App\Models\User;
use Carbon\Carbon;

class RoomController extends Controller
{
    //
    public function updateRoomStatus(Request $request)
    {

        $statusCode = 1; //0 : error-new status == 2 //1 : oke
        $id = $request->id;
        $roomStatusId = $request->roomStatusId;
        $room = Room::find($id);
        $roomType = $room->room_type ;
        if ($roomStatusId == 2) {
            $statusCode = 0;
        } else {
            $room->room_status_id = $roomStatusId;
            $room->save();
            $numRoom=  PostController::checkPost($roomType->id);
        };
        return response()->json([
            'statusCode' => $statusCode,
            'num' => $numRoom,
        ]);
    }
    public function getNotiRoom($roomId)
    {
        $room = Room::find($roomId);
        $roomType = $room->room_type;
        $motel = $roomType->motel;
        return response()->json([
            'room' => $room,
        ]);
    }

    public function intoRoom(Request $request)
    {
        $room = Room::find($request->roomId);
        $userId = $request->user()->id;

        $userHaveRoom = $request->user()->have_room;
        $roomStatus = $room->room_status_id;
        $roomType = $room->room_type;
        if ($userHaveRoom == 1) {
            return response()->json([
                'statusCode' => 0,
            ]); //user have motel ,
        }
        if ($roomStatus == 1) {
            DB::transaction(function () use ($room, $userId ,$roomType) {
                $user = User::find($userId);
                $tenant = $room->tenants()->create([
                    'created_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);
                $tenant->tenant_users()->create([
                    'user_id' => $userId,
                    'created_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);
                $room->room_status_id = 2;
                $room->save();
                $user->have_room = 1;
                $user->save();

                PostController::checkPost($roomType->id) ;
            });
            return response()->json([
                'statusCode' => 1,
            ]); //oke  ,
        } else if ($roomStatus == 2) {
            DB::transaction(function () use ($room, $userId) {
                $tenant = $room->tenants()->where('status', 0)->first(); // loi ngay day
                $tenantId = $tenant->id;
                TenantUser::insert([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $user = User::find($userId);
                $user->have_room  = 1;
                $user->save();
            });
            return response()->json([
                'statusCode' => 1,
            ]); //oke  ,
        } else {
            return response()->json([
                'statusCode' => 2,
            ]); // room is disable  ,
        }
    }
    public function outRoom(Request $request)
    {
        $user = User::find($request->user()->id);
        $tenant = $user->latest_tenant_user->tenant;
        $tenantUsers = $tenant->tenant_users;
        $bills = $tenant->bills;
        $room = $tenant->room;
        $roomType = $room->room_type;
        foreach ($bills as $bill) {
            if ($bill->status == 0) {
                return response()->json([
                    'statusCode' => 0,
                ]);
            }
        }
        DB::transaction(function () use ($user, $tenant, $room, $tenantUsers ,$roomType) {
            $numTenantUsers = count($tenantUsers);
            if ($numTenantUsers == 1) {
                $tenant->status = 1;
                $tenant->save();
                $room->room_status_id = 1;
                $room->save();
            } else if ($numTenantUsers > 1) {
                $user->latest_tenant_user()->delete() ;
            }
            $user->have_room = 0;
            $user->save();

            PostController::checkPost($roomType->id) ;
        });
        return response()->json([
            'statusCode' => 1
        ]);
    }
    public function adminOutRoom(Request $request){
        $user = User::find($request->userId);
        $tenant = $user->latest_tenant_user->tenant;
        $tenantUsers = $tenant->tenant_users;
        $room = $tenant->room;
        DB::transaction(function () use ($user, $tenant, $room, $tenantUsers) {
            $numTenantUsers = count($tenantUsers);
            if ($numTenantUsers == 1) {
                $tenant->status = 1;
                $tenant->save();
                $room->room_status_id = 1;
                $room->save();
            } else if ($numTenantUsers > 1) {
                $user->latest_tenant_user()->delete() ;
            }
            $user->have_room = 0;
            $user->save();
        });
        return response()->json([
            'statusCode' => 1
        ]);
    }
}
