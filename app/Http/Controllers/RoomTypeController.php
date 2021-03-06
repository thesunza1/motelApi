<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;
use Carbon\Carbon;
use Faker\Core\Number;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomTypeController extends Controller
{
    //
    public function getRoomTypeUser(Request $request)
    {
        $userId = $request->user()->id;
        $tenantUser = TenantUser::where('user_id', $userId)->first();
        $tenant_id = $tenantUser->tenant_id;
        $roomType = Tenant::find($tenant_id)->room->room_type;
        $roomTypeArr= $roomType->loadMissing('motel.user')->loadMissing('motel.motel_imgs.img_details');
        $resRoomTypeArr = new RoomTypeResource($roomTypeArr) ;
        // $roomType->motel->user;
        return response()->json([
            'statusCode' => 1,
            // 'roomType' => $roomType,
            'roomType' => $resRoomTypeArr,
        ]);
    }

    public function getRoomTypeImgs(Request $request)
    {
        $motel = Motel::find($request->motelId) ;
        $roomTypes = $motel->room_types;
        $roomTypesImgs = $roomTypes->loadMissing('img_details');
        $array = RoomTypeResource::collection($roomTypesImgs);

        return response()->json([
            'statusCode' => 1,
            'roomTypeImg' => $array,
        ]);
    }

    public function addNumRoom(Request $request)
    {
        $roomType = RoomType::find($request->roomTypeId);
        $numRoom = intval($request->numRoom);
        $room = $roomType->rooms;
        $maxRoom = intval($room[count($room) - 1]->name);

        DB::transaction(function () use ($roomType, $numRoom, $maxRoom) {
            for ($i = 1; $i <= $numRoom; $i++) {
                $roomType->rooms()->create([
                    'name' => $maxRoom + $i,
                    'room_status_id' => 1,
                ]);
            }
        });
        return response()->json([
            'statusCode' => 1,
            'maxRoom' => $maxRoom,
        ]);
    }

    public function updateRoomTypeContent(Request $request)
    {
        $roomType = RoomType::find($request->id);
        $data = [
            'name' => $request->name,
            'male' => $request->male,
            'female' => $request->female,
            'everyone' => $request->everyone,
            'content' => $request->content,
            'area' => $request->area,
        ];
        $roomType->update($data);
        $roomType->save();
        return response()->json([
            'statusCode' => 1,

        ]);
    }
    public function createRoomType(Request $request)
    {
        $motel = Motel::find($request->motelId);
        //create room_type
        $roomTypeData  = [
            'content' => $request->content,
            'male' => $request->male,
            'female' => $request->female,
            'everyone' => $request->everyone,
            'cost' => $request->cost,
            'name' => $request->name,
            'area' => $request->area,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        $numRoom = $request->numRoom;
        $img_num = $request->img_num;
        $latestRoomType = $motel->room_types()->latest('id')->first();
        $latestRoom = $latestRoomType->rooms()->latest('id')->first();
        $name = (int)$latestRoom->name;
        $roomType = $motel->room_types()->create($roomTypeData);
        $post = $roomType->posts()->create([
            'room_id' => null,
            'post_type_id' => 1 ,
            'conpound_content' => ' ' ,
            'content' => ' ',
            'status' => 1 ,
            'title' => $motel->name  ,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        //create imgdetail
        $pathfile = 'image';
        for ($i = 0; $i < $img_num; $i++) {

            $files = $request->file('img' . $i);
            $ran = Str::random(20);
            $namefile = Carbon::now()->timestamp . $ran . '.' . $files->getClientOriginalExtension();
            $files->move($pathfile, $namefile);
            $roomType->img_details()->create([
                'motel_img_id' => null,
                'img' => $namefile,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= $numRoom; $i++) {
            $roomType->rooms()->create([
                'name' => $i + $name,
                'room_status_id' => 1,
            ]);
        }
        //create room
        return response()->json(['statusCode' => 1 ]);
    }

    //delete roomtype for motel
    public function deleteRoomType(Request $request) {
        $roomType = RoomType::find($request->roomTypeId) ;
        $had_room = $roomType->had_rooms;
        $imgDetails = $roomType->img_details;
        $numHadRoom = count($had_room) ;
        $statusCode = 1 ;
        if($numHadRoom == 0 ){
            foreach($imgDetails as $img) {
                $pathImg = public_path('image/'.$img->img);
                if(file_exists($pathImg)){
                    unlink($pathImg);
                }
            }
            $roomType->delete();

        } else {
            $statusCode = 0;
        }

        return response()->json([
            'statusCode' => $statusCode ,
            'count' => $numHadRoom,
        ]);
    }
}
