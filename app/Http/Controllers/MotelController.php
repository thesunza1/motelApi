<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use App\Http\Resources\MotelResource;
use App\Http\Resources\RoomTypeResource;
use App\Models\User;
use Illuminate\Http\Request;

class MotelController extends Controller
{
    //
    public function getMotelRoomType(Request $request) {
        $motelId= $request->user()->motel->id;
        $motel = Motel::find($motelId)->loadMissing(['room_types.rooms.room_status']);
        return (new MotelResource($motel));
    }
    public function getInfoShareMotel(Request $request)  {
        $userId = $request->user()->id ;
        $user = User::find($userId)   ;
        $tenant_user = $user->latest_tenant_user ;
        $motel = $tenant_user->tenant->room->room_type->motel ;
        $room_types = $motel->room_types ;
        $room_typesLoad = $room_types->loadMissing('rooms.latest_tenant.infor_tenant_users.user');

        $room_typesArr  = RoomTypeResource::collection($room_typesLoad);

        return response()->json([
            'room_type_share' => $room_typesArr,
            'statusCode' => 1 ,
        ]);
    }
    public static function getMotel($userId) {
        $user = User::find($userId);
        $motel = $user->motel;
        return $motel;
    }

}
