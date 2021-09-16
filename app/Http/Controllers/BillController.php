<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomTypeBillResource ;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BillController extends Controller
{
    //
    public function getBillAllRoom(Request $request)
    {
        $roomType = User::find($request->user()->id)->motel->room_types;
        $roomType = $roomType->loadMissing('had_rooms.latest_tenant.bills')->loadMissing('had_rooms.latest_tenant.no_bills')->loadMissing('had_rooms.latest_tenant.num_bills');
        //$roomType = $roomType->loadMissing('had_rooms.latest_tenant.bills')->loadMissing('had_rooms.latest_tenant.no_bills');

        return response()->json([
            'allBillRoom' => RoomTypeBillResource::collection($roomType),
            'statusCode' => 1,
        ]);
    }
    public function createAllBill(Request $request) {
        //get request
        $userId = $request->user()->id ;
        $motel = User::find($userId)->motel;
        $roomType = $motel->room_types;

        DB::transaction(function ()  use ($roomType,$motel) {

        });
    }
}
