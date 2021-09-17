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
        $motel = User::find($userId)->motel; // motel from user id
        $roomTypes = $motel->room_types;    //room type

        DB::transaction(function ()  use ($roomTypes,$motel) {
            $electCost = $motel->elec_cost;
            $waterCost = $motel->waterCost;
            $peopleCost = $motel->peopleCost;

            foreach($roomTypes as $roomType) { // each roomtypes
                $cost = $roomType->cost ;
                $rooms = $roomType->had_rooms;
                $elecBegin = 0 ;
                $waterBegin = 0 ;
                foreach($rooms as $room) {
                    $tenant= $room->latest_tenant;
                    if( $tenant->num_status == 0 ) {
                        return response()->json([
                            'room'  => $room ,
                            'statusCode' => 0,
                        ]);
                    }
                    $elecBegin = $tenant->elec_num ;
                    $waterBegin = $tenant->water_num ;
                    $latest_bill = $tenant->latest_bill ;
                    if(count($latest_bill) > 0 ) {
                       $elecBegin =  $latest_bill->elec_end ;
                       $waterBegin =  $latest_bill->water_end ;
                    }

                }

            }
        });
    }
}
