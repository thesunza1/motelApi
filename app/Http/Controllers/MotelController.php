<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use App\Http\Resources\MotelResource;
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

}
