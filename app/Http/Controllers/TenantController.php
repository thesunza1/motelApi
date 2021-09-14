<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Noti;
use Carbon\Carbon;

class TenantController extends Controller
{
    //
    public function getTenant(Request $request)
    {
        $userId = $request->user()->id;
        $tenant_user = TenantUser::where('user_id', $userId)->first();
        $tenantId = $tenant_user->tenant_id;
        $tenant = Tenant::find($tenantId);
        $tenant->tenant_users;
        $tenant->tenant_room_equips;
        $tenant->room;
        return response()->json([
            // 'tenant_user' =>$tenant_user,
            'tenant' => $tenant,
            'statusCode' => 1,
        ]);
    }
    public function getNumRoom(Request $request)
    {
        $userId = $request->user()->id;
        $tenant_user = TenantUser::where('user_id', $userId)->first();
        $tenant = $tenant_user->tenant;
        return response()->json([
            'elec_num' => $tenant->elec_num,
            'water_num' => $tenant->water_num,
            'num_status' => $tenant->num_status,
        ]);
    }
    public function updateNumRoom(Request $request)
    {
        $userId = $request->user()->id;
        $tenant = $this->spGetTenant($userId);
        $room = $tenant->room;
        $title = 'xác nhận phòng ' . $room->name;
        $content = 'xác nhận số điện , nước ';
        $sender_id = $userId;
        $receiver_id = $room->room_type->motel->user->id;
        $noti_type_id = 4;
        $tenant->num_status = 0;
        $tenant->water_num = $request->water_num;// chua tesat
        $tenant->elec_num = $request->elec_num;
        $tenant->save();

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
        return response()->json(['statusCode'=> 1]);
    }

    //support function
    public function spGetTenant($userId)
    {
        $tenant_user = TenantUser::where('user_id', $userId)->first();
        $tenant = $tenant_user->tenant;
        return $tenant;
    }
}
