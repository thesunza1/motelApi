<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use App\Http\Resources\MotelResource;
use App\Http\Resources\RoomTypeResource;
use App\Models\MotelImg;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\Request;

class MotelController extends Controller
{
    //
    public function getMotelRoomType(Request $request)
    {
        $motelId = $request->user()->motel->id;
        $motel = Motel::find($motelId)->loadMissing(['room_types.rooms.room_status']);
        return (new MotelResource($motel));
    }
    public function getInfoShareMotel(Request $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId);
        $tenant_user = $user->latest_tenant_user;
        $motel = $tenant_user->tenant->room->room_type->motel;
        $room_types = $motel->room_types;
        $room_typesLoad = $room_types->loadMissing('rooms.latest_tenant.infor_tenant_users.user');

        $room_typesArr  = RoomTypeResource::collection($room_typesLoad);

        return response()->json([
            'room_type_share' => $room_typesArr,
            'statusCode' => 1,
        ]);
    }
    public static function getMotel($userId)
    {
        $user = User::find($userId);
        $motel = $user->motel;
        return $motel;
    }
    public function updateMotelInfor(Request $request)
    {
        $motel = $request->user()->motel;
        $motel->update([
            'name' => $request->names,
            'address' => $request->address,
            'camera' => $request->camera,
            'phone_number' => $request->phone_number,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'open' => $request->open,
            'closed' => $request->closed,
            'parking' => $request->parking,
        ]);
        $motel->save();

        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function updateMotelImg(Request $request)
    {
        $motelImg = MotelImg::find($request->detailImgId);
        if ($motelImg->img_type_id == 1) {
            $motel = $motelImg->motel;
            $motel->update([
                'content' => $request->motelContent,
            ]);
            $motel->save();
        };
        $motelImg->place = ($request->place) == null ? '' : $request->place;
        $motelImg->save();

        return response()->json([
            'statusCode' => 1,
            'content' => $request->content,
            'place' => $request->place,
        ]);
    }

    public function getAllMotel(Request $request)
    {
        $motels = Motel::with('user')->paginate(10);
        return response()->json([
            'statusCode' => 1,
            'motels' => $motels,
        ]);
    }

    public function findMotel(Request $request)
    {
        $motelId = $request->motelId;
        $userId = $request->userId;


        if (!$motelId) {
            $motel = User::find($userId)->motel;
            if ($motel != null) {
                $motel->user ;
                return response()->json([
                    'statusCode' => 1,
                    'motel' => $motel,
                ]);
            } else {
                return response()->json([
                    'statusCode' => 2,
                ]);
            }
        } else {
            $motel = Motel::find($motelId);
            if ($motel != null ) {
                $motel->user ;
                return response()->json([
                    'statusCode' => 1,
                    'motel' => $motel,
                ]);
            } else {
                return response()->json([
                    'statusCode' => 2,
                ]);
            }
        }
    }
}
