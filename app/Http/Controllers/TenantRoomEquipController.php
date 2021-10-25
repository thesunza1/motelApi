<?php

namespace App\Http\Controllers;

use App\Models\Noti;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TenantUser;
use Carbon\Carbon;
use App\Models\TenantRoomEquip;

class TenantRoomEquipController extends Controller
{
    public function getTenantRoomEquips(Request $request)
    {
        $userId = $request->user()->id;
        $tenant_user = TenantUser::where('user_id', $userId)->latest()->first();
        $equips = TenantRoomEquip::where('tenant_id', $tenant_user->tenant_id)->get();
        $equip_num = count($equips);

        return response()->json([
            'equips' => $equips,
            'statusCode' => 1,
            'equip_num' => $equip_num,
        ]);
    }
    public function deleteTenantRoomEquip(Request $request)
    {
        $equipId = $request->equip_id;
        $tenant = TenantRoomEquip::find($equipId)->tenant;
        $tenant->eq_status = 0;
        $tenant->save() ;
        TenantRoomEquip::find($equipId)->delete();
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function createTenantRoomEquips(Request $request)
    {
        $userId = $request->user()->id;
        $tenant_user = TenantUser::where('user_id', $userId)->latest()->first();
        $tenant = $tenant_user->tenant;
        $tenant->eq_status= 0 ;
        $tenant->save() ;
        $oldLen = count($tenant->tenant_room_equips);
        $newLen = $request->equip_num;
        $newEquips = $request->equips;

        $room = $tenant->room;
        $motelUser = $room->room_type->motel->user;
        $motel= $room->room_type->motel;
        $title  = ' Xác nhận phòng ' . $room->name . ' Trọ '.$motel->name;
        $sender_id = $userId;
        $receiver_id = $motelUser->id;
        $content = 'Xác nhận tình trạng thiết bị phòng!';
        $noti_type_id = 4;
        DB::transaction(function () use ($tenant, $oldLen, $newEquips, $newLen) {
            for ($i = 0; $i < $newLen; $i++) {
                $equipId = $newEquips[$i]['id'];
                $names = $newEquips[$i]['name'];
                $content = $newEquips[$i]['content'];
                if ($i < $oldLen) {
                    $findEquip = TenantRoomEquip::find($equipId);
                    $findEquip->name = $names;
                    $findEquip->content = $content;
                    $findEquip->save();
                } else {
                    $tenant->tenant_room_equips()->create([
                        'name' => $names,
                        'content' => $content,
                    ]);
                }
            }
        });
        $this->sendNoti($title,$sender_id,$receiver_id,$content,$noti_type_id);
        return response()->json([
            'statusCode' => 1,
            // 'new_equips' => $newEquips[0]['content'],
        ]);
    }

    public function sendNoti($title, $sender_id, $receiver_id, $content, $noti_type_id)
    {
        DB::transaction(function () use ($title, $sender_id, $receiver_id, $content, $noti_type_id) {
            Noti::insert([
                'title' => $title,
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'content' => $content,
                'noti_type_id' => $noti_type_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });
    }
}
