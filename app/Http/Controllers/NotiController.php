<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Http\Resources\NotiResource;
use App\Models\Noti;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class NotiController extends Controller
{
    //send invite from room_id to user_id
    public function sendInvite(Request $request)
    {
        $statusCode = 1;
        $roomId = $request->roomId;
        $receiverId = $request->receiverId;
        $userRole = User::find($receiverId)->role_id;
        if ($userRole == 2) $statusCode = 0;
        else if ($userRole == 3) $statusCode = 2;
        else {
            $room = Room::find($roomId);
            $motel = $room->room_type->motel;
            $motelName = $motel->name;
            $noti_type_id = 3;
            $title = 'mời vào trọ ' . $motelName;
            $noii = Noti::create([
                'title' => $title,
                'receiver_id' =>$receiverId,
                'content' => '',
                'sender_id' => $request->user()->id,
                'noti_type_id' => $noti_type_id,
                'room_id' => $roomId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        };
        return response()->json([
            'statusCode' => $statusCode,
        ]);
    }
    public function getAllNoti(Request $request)
    {
        $userId = $request->user()->id;

        $noti = Noti::where('receiver_id', $userId)->orderBy('created_at', 'desc')->get();

        $notiArr =   NotiResource::collection($noti->loadMissing('senderUser'));

        return response()->json(['notis' => $notiArr]);
    }
    public function countNoti(Request $request) {
        $userId = $request->user()->id;

        $noti = Noti::where('receiver_id' , $userId)->where('status' , 0 )->get() ;
        $num = -1;
        if($noti) {
            $num = count($noti);
        }
        return response()->json([
            'num' =>$num ,
        ]);
    }

    public function  sendNoti(Request $request) {
        $senderId = $request->user()->id ;
        $noti = $request->noti ;
        $statusCode = 1;
        try{
        DB::transaction(function() use($senderId,$noti ) {
          $send = Noti::insert([
            'sender_id' => $senderId,
            'receiver_id' =>(int)$noti['receiver_id'],
            'title' => $noti['title'],
            'content' => $noti['content'],
            'noti_type_id' => $noti['noti_type_id'],
            'created_at' => Carbon::now() ,
            'updated_at' => Carbon::now() ,
          ]) ;
        });

        }
        catch(\ExcepTion $e) {
            $statusCode = 0 ;
        }
        return response()->json([
            'statusCode' =>$statusCode,
            'noti' => $noti,
        ]);
    }
    public function isSeen($notiId){
        $noti = Noti::find($notiId);
        $noti->status = 1 ;
        $noti->save() ;
        return response()->json(['statusCode' => 1 ]);
    }
    public static function sendNotiChoose($title,$senderId , $receiverId,$content,$notiTypeId,$room_id ,$status){
        if($notiTypeId !=3 ) {
            $roomId = null;
        }
        Noti::insert([
            'title' => $title,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $content,
            'room_id' => $roomId,
            'noti_type_id' => $notiTypeId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return true ;
    }
}
