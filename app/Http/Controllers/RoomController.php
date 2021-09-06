<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    //
    public function updateRoomStatus(Request $request)
    {

        $statusCode = 1; //0 : error-new status == 2 //1 : oke
        $id = $request->id;
        $roomStatusId = $request->roomStatusId;
        $room = Room::find($id);
        if ($roomStatusId ==2) {
            $statusCode=0;
        } else {
            $room->room_status_id = $roomStatusId;
            $room->save();
        };
        return response()->json([
            'statusCode' => $statusCode,
        ]);
    }
}
