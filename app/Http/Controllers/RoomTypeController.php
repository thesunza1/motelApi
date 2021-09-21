<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\MotelController;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;

class RoomTypeController extends Controller
{
    //
    public function getRoomTypeUser(Request $request) {
        $userId = $request->user()->id ;
        $tenantUser = TenantUser::where('user_id' , $userId)->first() ;
        $tenant_id = $tenantUser->tenant_id ;
        $roomType = Tenant::find($tenant_id)->room->room_type;
        $roomType->motel;
        return response()->json([
            'statusCode' => 1 ,
            'roomType' => $roomType,
        ]);
    }

    public function getRoomTypeImgs(Request $request) {
        $motel = MotelController::getMotel($request->user()->id);
        $roomTypes = $motel->room_types;
        $roomTypesImgs = $roomTypes->loadMissing('img_details');
        $array = RoomTypeResource::collection($roomTypesImgs);

        return response()->json([
            'statusCode' => 1 ,
            'roomTypeImg' => $array  ,
        ]);
    }

}
