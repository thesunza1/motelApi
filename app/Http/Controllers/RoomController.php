<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    //
    public function updateRoomStatus (Request $request) {
        $id = $request->id ;
        $roomStatusId = $request->roomStatusId ;

        $room = Room::find($id);
        $room->room_status_id = $roomStatusId;
        $room->save() ;
        return response()->json([
            'statusCode' => 1 ,
        ]);
    }
}
