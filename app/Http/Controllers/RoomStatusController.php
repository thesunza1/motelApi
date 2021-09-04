<?php

namespace App\Http\Controllers;

use App\Models\RoomStatus;
use App\Http\Resources\RoomStatusResource;
use Illuminate\Http\Request;

class RoomStatusController extends Controller
{
    //
    public function roomStatuses() {
       $roomStatuses = RoomStatus::all() ;
       return RoomStatusResource::collection($roomStatuses)->response();
    }
}
