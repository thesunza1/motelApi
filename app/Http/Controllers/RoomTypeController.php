<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Http\Request;

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
}
