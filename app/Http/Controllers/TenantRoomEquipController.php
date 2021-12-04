<?php

namespace App\Http\Controllers;

use App\Http\Resources\TenantResource;
use App\Http\Resources\TenantRoomEquipResource;
use App\Models\Motel;
use App\Models\Noti;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TenantUser;
use Carbon\Carbon;
use App\Models\TenantRoomEquip;
use Illuminate\Support\Str;

use function PHPUnit\Framework\fileExists;

class TenantRoomEquipController extends Controller
{
    public function getTenantRoomEquips(Request $request)
    {
        // $userId = $request->user()->id;
        $userId = $request->user();
        // $tenant_user = TenantUser::where('user_id', $userId)->latest()->first();
        // $equips = TenantRoomEquip::where('tenant_id', $tenant_user->tenant_id)->get();
        $tenant = $userId->latest_tenant_user->tenant;
        $equip = $tenant->tenant_room_equips;
        $eqRela = $equip->loadMissing('img_details');
        $equips = TenantRoomEquipResource::collection($eqRela) ;
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
        $tenant->save();
        $imgDetails = TenantRoomEquip::find($equipId)->img_details ;
        foreach($imgDetails as $imgDetail ) {
            $img = public_path('image/'.$imgDetail->img) ;
            if(file_exists($img)) {
                unlink($img) ;
            }
        }
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
        $tenant->eq_status = 0;
        $tenant->save();
        $oldLen = count($tenant->tenant_room_equips);
        $newLen = $request->equip_num;
        $newEquips = $request->equips;

        $room = $tenant->room;
        $motelUser = $room->room_type->motel->user;
        $motel = $room->room_type->motel;
        $title  = ' Xác nhận phòng ' . $room->name . ' Trọ ' . $motel->name;
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
        $this->sendNoti($title, $sender_id, $receiver_id, $content, $noti_type_id);
        return response()->json([
            'statusCode' => 1,
            // 'new_equips' => $newEquips[0]['content'],
        ]);
    }
    public function uploadImgTenantRoomEquip(Request $request)
    {
        $imgs = $request->files;
        $tenantRoomEquipId = $request->tenantRoomEquipId;
        if($tenantRoomEquipId == -1 ) {
            return response()->json([
                'statusCode' => 0 ,
            ]);
        }
        $tenantRoomEquip = TenantRoomEquip::find($tenantRoomEquipId);
        $imgDetails = $tenantRoomEquip->img_details;
        //unlink old file  delete img in db
        if (count($imgDetails) > 0) {
            foreach ($imgDetails as $ids) {
                $img = public_path('image/' . $ids->img);
                if (file_exists($img)) {
                    unlink($img);
                }
            }
            $tenantRoomEquip->img_details()->delete();
        }
        //add new imgs and insert to db
        $time = Carbon::now();
        foreach($imgs as $img) {
            $ran = Str::random(20);
            $fileName = $time->timestamp.$ran . '.' . $img->getClientOriginalExtension() ;
            $data = [
                'room_type_id' =>null ,
                'motel_img_id' => null ,
                'img' => $fileName,
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $tenantRoomEquip->img_details()->create($data) ;
            $img->move('image', $fileName);
        }
        $tenantRoomEquip->tenant()->update([
            'eq_status' => 0 ,
        ]);
        return response()->json([
            'statusCode' => 1,
            'id' => $tenantRoomEquipId,
        ]);
    }

    //change status re
    public function tREStatus(Request $request) {
        $status= $request->status ;
        $equipId = $request->id;

        $eq = TenantRoomEquip::find($equipId);
        $eq->status = $status ;
        $eq->save() ;

        return response()->json([
            'statusCode' => 1,
        ]);
    }

    public function getAllTRE(Request $request) {
        $arrRoomType= RoomType::where('motel_id' , $request->motelId)->pluck('id')->toArray();
        $lasTenant = Room::whereIn('room_type_id', $arrRoomType)->where('room_status_id',2)->pluck('id')->toArray();
        $teant = Tenant::whereIn('room_id', $lasTenant )->where('status',0)->pluck('id')->toArray();
        $tenantRE = TenantRoomEquip::whereIn('tenant_id',$teant)->where('status',0)->with('img_details')->get();

        return response()->json([
            'arrRoomType' => $arrRoomType,
            'latestTenant' => $lasTenant,
            'tenant' => $tenantRE,
            'statusCode' => 1 ,
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
