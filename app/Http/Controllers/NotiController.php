<?php

namespace App\Http\Controllers;

use App\Models\Motel;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Http\Resources\NotiResource;
use App\Models\Noti;
use App\Models\Post;
use App\Models\RoomType;
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
            $title = 'Mời vào trọ ' . $motelName . '. Phòng ' . $room->name;
            $noii = Noti::create([
                'title' => $title,
                'receiver_id' => $receiverId,
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
    //findNoti
    public function findNoti(Request $request)
    {
        $userId = $request->user()->id;
        $notiTypeId = $request->notiTypeId;
        $from = $request->from;
        $to = $request->to;
        $noti = Noti::where('receiver_id', $userId);

        if ($notiTypeId != 0) $noti->where('noti_type_id', $notiTypeId);
        if ($from != 0) $noti->whereBetween('created_at', [$from, $to]);
        $notiArr = $noti->orderByDesc('created_at')->with('senderUser')->get();

        $resData = [
            'statusCode' => 1,
            'noti' => $notiArr,
        ];
        return response()->json($resData);
    }

    //getAllNoti
    public function getAllNoti(Request $request)
    {
        $userId = $request->user()->id;

        $noti = Noti::where('receiver_id', $userId)->orderBy('created_at', 'desc')->get();

        $notiArr =   NotiResource::collection($noti->loadMissing('senderUser'));

        return response()->json(['notis' => $notiArr]);
    }
    public function getAllOutbox(Request $request)
    {
        $userId = $request->user()->id;

        $noti = Noti::where('sender_id', $userId)->orderBy('created_at', 'desc')->get();

        $notiArr =   NotiResource::collection($noti->loadMissing('receiverUser'));

        return response()->json(['notis' => $notiArr]);
    }
    //get getIntoNoti()
    public function getIntoNoti(Request $request)
    {
        // $motelId = $request->motelId;
        $userId = $request->user()->id;

        $noti = Noti::where('receiver_id', $userId)->where('noti_type_id', 5)->orderByDesc('created_at')->get();
        foreach ($noti as $n) {
            if ($n->status == 1) continue;
            $n->status = 1;
            $n->save();
        }
        $notiArr = NotiResource::collection($noti->loadMissing('senderUser'));

        return response()->json(['notis' => $notiArr]);
    }
    //get count into noti()
    public function countIntoNoti(Request $request)
    {
        $userId = $request->user()->id;

        $noti = Noti::where('receiver_id', $userId)->where('noti_type_id', 5)->where('status', 0)->get();
        $num = -1;
        if ($noti) {
            $num = count($noti);
        }
        return response()->json([
            'statusCode' => 1,
            'num' => $num,
        ]);
    }

    public function countNoti(Request $request)
    {
        $userId = $request->user()->id;

        $noti = Noti::where('receiver_id', $userId)->where('status', 0)->get();
        $num = -1;
        if ($noti) {
            $num = count($noti);
        }
        return response()->json([
            'statusCode' => 1,
            'num' => $num,
        ]);
    }

    public function  sendNoti(Request $request)
    {
        $senderId = $request->user()->id;
        $noti = $request->noti;
        $receiverId = $request->receiver_id;
        $statusCode = 1;
        try {
            DB::transaction(function () use ($senderId, $noti, $receiverId) {
                $send = Noti::insert([
                    'sender_id' => $senderId,
                    'receiver_id' => (int) $receiverId,
                    'title' => $noti['title'],
                    'content' => $noti['content'],
                    'noti_type_id' => $noti['noti_type_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            });
        } catch (\ExcepTion $e) {
            $statusCode = 0;
        }
        return response()->json([
            'statusCode' => $statusCode,
            'noti' => $noti,
        ]);
    }
    public function isSeen($notiId)
    {
        $noti = Noti::find($notiId);
        $noti->status = 1;
        $noti->save();
        return response()->json(['statusCode' => 1]);
    }

    public function isReadNoti(Request $request) {
        Noti::where('receiver_id', $request->user()->id)->where('status' , 0 )->update([
            'status' => 1 ,
        ]);

        return response()->json(['statusCode' => 1]);
    }
    public function sendReport(Request $request)
    {
        $type = $request->type;
        $content = $request->content;
        $senderId = $request->senderId;
        $title = 'báo cáo ';
        if ($type == 1) {
            //postId
            $post = Post::find($request->postId);
            if ($post->post_type_id == 1) {
                $motel = $post->room_type->motel;
            } else {
                $motel = $post->room->room_type->motel;
            }

            $arrContent =
                'Trọ : ' .  $motel->name . '<br/>' .
                'Chủ trọ : ' .  $motel->user->name . '<br/>' .
                'Email chủ trọ : ' .  $motel->user->email . '<br/>' .
                $content . '<br/>';
            $title .= $motel->name;
        } else if ($type == 2) {
            //motelId
            $motel = Motel::find($request->motelId);
            $arrContent =
                'trọ : '  . $motel->name . '<br/>' .
                'Chủ trọ : ' .  $motel->user->name . '<br/>' .
                'Email chủ trọ : ' .  $motel->user->email . '<br/>' .
                $content . '<br/>';

            $title .= $motel->name;
        } else {
            //roomTypeId
            $motel = RoomType::find($request->roomTypeId)->motel;
            $arrContent =
                'trọ : ' . $motel->name . '<br/>' .
                'Chủ trọ : ' .  $motel->user->name . '<br/>' .
                'Email chủ trọ : ' .  $motel->user->email . '<br/>' .
                $content . '<br/>';
            $title .= $motel->name;
        }

        NotiController::sendNotiChoose($title, $senderId, 3, $arrContent, 2, null, 0);
        return response()->json([
            'statusCode' => 1,
        ]);
    }

    public function getRoomInto(Request $request)
    {
        $notiId =  $request->notiId;
        $content  = Noti::find($notiId)->content;
        $roomString = substr($content, 4, strpos($content, '-', 5) - 4);
        $roomIdList = (array) explode(' ', $roomString);
        foreach ($roomIdList as &$roomId) {
            $roomId = (int) $roomId;
        }
        $room = Room::whereIn('id', $roomIdList)->where('room_status_id', 1)->get();
        $motel = Room::find($roomIdList[0])->room_type->motel;
        return response()->json([
            'statusCode' => 1,
            'roomString' => $roomString,
            'roomList' => $roomIdList,
            'motel' => $motel,
            'room' => $room,
        ]);
    }

    public function changeIntoStatus(Request $request)
    {
        $noti = Noti::find($request->notiId);
        $noti->invite_status = $request->inviteStatus;
        $noti->save();
        return response()->json([
            'statusCode'  => 1,
        ]);
    }
    public function sendReject(Request $request)
    {
        $motel = Motel::find($request->motelId);
        $receiverId = $request->receiverId;
        $title = 'Trọ ' . $motel->name . ' Từ chối xin vào trọ của bạn';
        $content = $request->content;

        NotiController::sendNotiChoose($title, $request->user()->id, $receiverId, $content, 4, null, 0);
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public static function sendNotiChoose($title, $senderId, $receiverId, $content, $notiTypeId, $room_id, $status)
    {
        if ($notiTypeId != 3) {
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
        return true;
    }
}
