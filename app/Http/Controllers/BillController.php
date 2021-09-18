<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomTypeBillResource;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
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
    public function createAllBill(Request $request)
    {
        //get request
        $userId = $request->user()->id;
        $motel = User::find($userId)->motel; // motel from user id
        $roomTypes = $motel->room_types;    //room type

        $statusCode = DB::transaction(function ()  use ($roomTypes, $motel ) {
            $electCost = $motel->elec_cost;
            $waterCost = $motel->water_cost;
            $peopleCost = $motel->people_cost;

            foreach ($roomTypes as $roomType) { // each roomtypes
                $cost = $roomType->cost;
                $rooms = $roomType->had_rooms;
                $elecBegin = 0;
                $waterBegin = 0;
                if (count($rooms) == 0) { // no had room have user  .
                    continue;
                }

                foreach ($rooms as $room) {
                    $tenant = $room->latest_tenant; // get tenant moi nhat
                    $numUser = count($tenant->tenant_users);
                    if($numUser >=2) {
                        $peopleCost *= $numUser;
                    }
                    if($tenant->num_status == 0 ) {
                        return [3,$room] ;
                    }

                    $elecBegin = $tenant->elec_num;
                    $waterBegin = $tenant->water_num;
                    $latest_bill = $tenant->latest_bill;
                    $dateEnd = Carbon::now();
                    $minDate = 0;

                    if ($latest_bill != null ) {
                        $elecBegin =  $latest_bill->elec_end;
                        $waterBegin =  $latest_bill->water_end;
                        $dateEnd =Carbon::parse($latest_bill->date_end) ;
                        if (Carbon::now()->subDays($minDate)->lte($dateEnd)) {
                            // return [2,$room];
                            return [2,$room];
                        }
                    }else {
                        $dateEnd = $tenant->in_date;
                    }
                    $tenant->bills()->create([
                        'date_begin'  =>$dateEnd,
                        'date_end'  => Carbon::now(),
                        'elec_begin'  => $elecBegin,
                        'elec_end'  => $elecBegin,
                        'water_begin'  => $waterBegin,
                        'water_end'  => $waterBegin,
                        'cost'  => $cost,
                        'water_cost'  => $waterCost,
                        'elec_cost'  => $electCost,
                        'people_cost'  => $peopleCost,
                    ]);
                }
            }
            return [1,1] ;
        });
        return response()->json([
            'statusCode' => $statusCode[0],
            'room' => $statusCode[1],
        ]);

    }
}
