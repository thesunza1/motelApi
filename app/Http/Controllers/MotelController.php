<?php

namespace App\Http\Controllers;

use App\Http\Resources\MotelImgResource;
use App\Models\Motel;
use App\Http\Resources\MotelResource;
use App\Http\Resources\RoomTypeResource;
use App\Models\MotelImg;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotelController extends Controller
{

    //get motels for motel
    public function getMotels(Request $request)
    {
        $motels = $request->user()->motels;
        $motelsRelation = $motels->loadMissing(['room_types.rooms.room_status'])->loadMissing('user');
        $motelArr = MotelResource::collection($motelsRelation);
        return response()->json([
            'motels' => $motelArr,
            'statusCode' => 1,
        ]);
    }
    //get a motel - roomtype - rooms - room_status ;
    public function getMotelRoomType(Request $request)
    {
        // $motelId = $request->user()->motel->id;
        $motelId = $request->motelId;
        $motel = Motel::find($motelId)->loadMissing(['room_types.rooms.room_status']);
        return (new MotelResource($motel));
    }
    //get : get infor share motel
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
    //get a motel
    public static function getMotel($userId)
    {
        $user = User::find($userId);
        $motel = $user->motel;
        return $motel;
    }
    //get all motel
    public function updateMotelInfor(Request $request)
    {
        $motel = Motel::find($request->motelId);
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
        $email = $request->email;

        $motel = User::where('email',$email)->first()->motel;
        if ($motel != null) {
            $motel->user;
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

    public function adminGetMotel(Request $request)
    {
        $motel = Motel::find($request->motelId);
        $motelRelation = $motel->loadMissing('user'); //get motel-user
        $roomTypes = $motel->room_types;
        $roomTypeRelation = $roomTypes->loadMissing('had_rooms.latest_tenant.tenant_users.user');
        $roomTypeImgRelation = $roomTypes->loadMissing('img_details');
        $motelImgs = $motel->motel_imgs;
        $publicMotelImgRelation = $motelImgs->loadMissing('img_details');
        // collection model ;
        $arrMotel = new MotelResource($motelRelation);
        $arrRoomTypes = RoomTypeResource::collection($roomTypeRelation);
        $arrRoomTypeImgs = RoomTypeResource::collection($roomTypeImgRelation);
        $arrPublicImgs =  MotelImgResource::collection($publicMotelImgRelation);

        return response()->json([
            'statusCode' => 1,
            'motel' => $arrMotel,
            'roomTypes' => $arrRoomTypes,
            'roomTypeImgs' => $arrRoomTypeImgs,
            'publicImgs' => $arrPublicImgs,
        ]);
    }

    public function adminDeleteMotel(Request $request)
    {
        $motelId = $request->motelId;

        //
        $motel = Motel::find($motelId);
        $roomTypes = $motel->room_types;

        DB::transaction(function () use ($motel, $roomTypes) {
            foreach ($roomTypes as $roomType) {
                $rooms = $roomType->had_rooms;
                foreach ($rooms as $room) {
                    $tenantUsers = $room->latest_tenant->tenant_users;
                    foreach ($tenantUsers as $tenantUser) {
                        $user = $tenantUser->user;
                        $user->have_room = 0;
                        $user->save();
                    }
                }
            }
            $motel->user()->delete();
        });
        return response()->json([
            'statusCode' => 1,
        ]);
    }
}
