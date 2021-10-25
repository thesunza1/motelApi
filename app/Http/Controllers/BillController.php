<?php

namespace App\Http\Controllers;

use App\Http\Resources\BillResource;
use App\Http\Resources\RoomTypeBillResource;
use App\Models\Bill;
use App\Models\Motel;
use App\Models\Noti;
use App\Models\Room;
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
        // $roomType = User::find($request->user()->id)->motel->room_types;
        $roomType = Motel::find($request->motelId)->room_types;
        $roomType = $roomType->loadMissing('had_rooms.latest_tenant.bills')->loadMissing('had_rooms.latest_tenant.no_bills')->loadMissing('had_rooms.latest_tenant.num_bills');
        //$roomType = $roomType->loadMissing('had_rooms.latest_tenant.bills')->loadMissing('had_rooms.latest_tenant.no_bills');
        $bill = RoomTypeBillResource::collection($roomType);
        return response()->json([
            'allBillRoom' => $bill,
            'statusCode' => 1,
        ]);
    }
    public function createAllBill(Request $request)
    {
        //get request
        // $userId = $request->user()->id;
        $motel = Motel::find($request->motelId); // motel from user id
        $roomTypes = $motel->room_types;    //room type

        $statusCode = DB::transaction(function ()  use ($roomTypes, $motel) {
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
                    if ($numUser >= 2) {
                        $peopleCost *= $numUser;
                    }
                    if ($tenant->num_status == 0) {
                        return [3, $room];
                    }

                    $elecBegin = $tenant->elec_num;
                    $waterBegin = $tenant->water_num;
                    $latest_bill = $tenant->latest_bill;
                    $dateEnd = Carbon::now();
                    $minDate = 0;

                    if ($latest_bill != null) {
                        $elecBegin =  $latest_bill->elec_end;
                        $waterBegin =  $latest_bill->water_end;
                        $dateEnd = Carbon::parse($latest_bill->date_end);
                        if (Carbon::now()->subDays($minDate)->lte($dateEnd)) {
                            // return [2,$room];
                            return [2, $room];
                        }
                    } else {
                        $dateEnd = $tenant->in_date;
                    }
                    $tenant->bills()->create([
                        'date_begin'  => $dateEnd,
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
            return [1, 1];
        });
        return response()->json([
            'statusCode' => $statusCode[0],
            'room' => $statusCode[1],
        ]);
    }
    public function createSomeBill(Request $request)
    {
        //get request
        $someRoom = $request->rooms;
        $userId = $request->user()->id;
        // $motel = User::find($userId)->motel; // motel from user id
        $motel= Motel::find($request->motelId);
        $roomTypes = $motel->room_types;    //room type

        $statusCode = DB::transaction(function ()  use ($roomTypes, $motel,$someRoom) {
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
                    $inArr = in_array($room->id, $someRoom);
                    if($inArr ==null ){
                        continue;
                    }
                    $tenant = $room->latest_tenant; // get tenant moi nhat
                    $numUser = count($tenant->tenant_users);
                    if ($numUser >= 2) {
                        $peopleCost *= $numUser;
                    }
                    if ($tenant->num_status == 0) {
                        return [3, $room];
                    }

                    $elecBegin = $tenant->elec_num;
                    $waterBegin = $tenant->water_num;
                    $latest_bill = $tenant->latest_bill;
                    $dateEnd = Carbon::now();
                    $minDate = 0;

                    if ($latest_bill != null) {
                        $elecBegin =  $latest_bill->elec_end;
                        $waterBegin =  $latest_bill->water_end;
                        $dateEnd = Carbon::parse($latest_bill->date_end);
                        if (Carbon::now()->subDays($minDate)->lte($dateEnd)) {
                            // return [2,$room];
                            return [2, $room];
                        }
                    } else {
                        $dateEnd = $tenant->in_date;
                    }
                    $tenant->bills()->create([
                        'date_begin'  => $dateEnd,
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
            return [1, 1];
        });
        return response()->json([
            'statusCode' => $statusCode[0],
            'room' => $statusCode[1],
        ]);
    }
    public function updateBillNum(Request $request)
    {
        $billId = $request->bill_id;
        $waterEnd = $request->water_end;
        $elecEnd = $request->elec_end;

        $bill = Bill::find($billId);
        $bill->elec_end = $elecEnd;
        $bill->water_end = $waterEnd;
        $bill->save();

        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function updateBillStatus(Request $request)
    {
        $billId = $request->bill_id;

        $bill = Bill::find($billId);
        $bill->status = 1;
        $bill->save();

        return response()->json([
            'statusCode' => 1,
        ]);
    }

    public function sendNotiBill(Request $request)
    {
        $billId = $request->bill_id;
        $userId = $request->user()->id;
        $bill = Bill::find($billId);
        $tenantUsers = $bill->tenant->tenant_users;
        DB::transaction(function () use ($userId, $tenantUsers, $bill) {
            foreach ($tenantUsers as $tenantUser) {
                Noti::insert([
                    'title' => 'thanh toán tiền phòng ',
                    'sender_id' => $userId,
                    'receiver_id' => $tenantUser->user_id,
                    'content' => "thanh toán bill từ ngày : $bill->date_begin tới ngày $bill->date_end",
                    'noti_type_id' => 4,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        });

        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function sendAllNotiBill(Request $request)
    {
        $motelId = $request->motel_id;
        $userId = $request->user()->id;
        $motel = Motel::find($motelId);
        $roomTypes = $motel->room_types;
        DB::transaction(function () use ($roomTypes, $userId) {
            foreach ($roomTypes as $roomType) {
                $had_rooms = $roomType->had_rooms;
                foreach ($had_rooms as $had_room) {
                    $tenantUsers = $had_room->latest_tenant->tenant_users;
                    foreach ($tenantUsers as $tenantUser) {
                        Noti::insert([
                            'title' => ' có bill các phòng ',
                            'sender_id' => $userId,
                            'receiver_id' => $tenantUser->user_id,
                            'content' => " chủ trọ vừa lập bill xong rồi các anh chị em có thể vào xem rồi  ",
                            'noti_type_id' => 4,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }
        });
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function getBillOwnRoom(Request $request)  {
        $userId = $request->user()->id ;
        $user = User::find($userId);
        $tenantUser = $user->latest_tenant_user;
        $bills = $tenantUser->tenant->bills;
        $billsArr = BillResource::collection($bills);
        return response()->json([
            'bills' => $billsArr ,
            'statusCode'  => 1 ,
        ]);
    }
    public function sendBillYes(Request $request) {
        $userId = $request->user()->id ;
        $content= $request->content ;
        $room = Room::find($request->room_id);
        $bill = $request->bill;
        $title = "phòng $room->name bill " .$bill['date_begin'] . '-'.$bill['date_end'];
        $user = User::find($userId) ;
        $receiver = $user->latest_tenant_user->tenant->room->room_type->motel->user ;
        $receiver_id = $receiver->id ;

        NotiController::sendNotiChoose($title,$userId,$receiver_id,$content,4,null,0);


        return response()->json([
            'statusCode' => 1 ,
            'receiver_id' => $title,
        ]);
    }
    public function sendBillError(Request $request) {
        $userId = $request->user()->id ;
        $content= $request->content ;
        $room = Room::find($request->room_id);
        $user = User::find($userId) ;
        $bill = $request->bill;
        $motel= $user->latest_tenant_user->tenant->room->room_type->motel;
        $title = "phòng $room->name bill ".' Trọ '. $motel->name .' ngày ' .$bill['date_begin'] . '-'.$bill['date_end'];
        $receiver = $motel->user ;
        $receiver_id = $receiver->id ;

        NotiController::sendNotiChoose($title,$userId,$receiver_id,$content,4,null,0);


        return response()->json([
            'statusCode' => 1 ,
            'receiver_id' => $title,
        ]);

    }
}
