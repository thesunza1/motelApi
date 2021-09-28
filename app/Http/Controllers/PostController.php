<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostType;
use App\Models\User;

class PostController extends Controller
{
    //
    public function createPostUser(Request $request)
    {
        $userId = $request->user()->id;
        $roomId = TenantUser::where('user_id', $userId)->first()->tenant->room->id;
        $title = ' tìm người ở ghép ';
        $conpound_content = $request->content;
        $post = Post::create([
            'title' => $title,
            'room_id' => $roomId,
            'conpound_content' => $conpound_content,
            'content' => '',
            'post_type_id' => 2,
            'status' => 1
        ]);
        return response()->json([
            'statusCode' => 1,
            'post' => $post,
        ]);
    }
    public function getPostConpound(Request $request)
    {
        $userId = $request->user()->id;
        $roomId = TenantUser::where('user_id', $userId)->first()->tenant->room->id;

        $post = Post::where('room_id', $roomId)->get();
        return response()->json([
            'statusCode' => 1,
            'posts' => $post
        ]);
    }
    public function changeStatusConpound(Request $request)
    {
        $post = Post::find($request->post_id);
        $post->status = -$post->status;
        $post->save();
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function deleteConpound(Request $request)
    {
        Post::find($request->post_id)->delete();
        return response()->json([
            'statusCode' => 1,
        ]);
    }

    public function getPost(Request $request)
    {
        $post_type = PostType::all();
        $posts = Post::where('id', '>', 0)->orderByDesc('created_at');
        $postsPaginator = $posts->with('room_type.first_img_detail')->with('room.room_type.first_img_detail')
            ->with('room_type.motel.user')->with('room.room_type.motel.user')
            ->with('room.latest_tenant.tenant_users.user')->paginate(9);
        return response()->json([
            'statusCode' => 1,
            'posts' => $postsPaginator,
            'post_type' => $post_type,
        ]);
    }
    public function getSearch(Request $request)
    {
    }
    public function detailPost(Request $request)
    {
        $post_id = $request->post_id;
        $post = Post::find($post_id);
        $postWith = $post->loadMissing('room_type.img_details')->loadMissing('room.room_type.img_details')
            ->loadMissing('room_type.motel.user')->loadMissing('room.room_type.motel.user')
            ->loadMissing('room.latest_tenant.tenant_users.user')->loadMissing('room_type.motel.motel_imgs.img_details')->loadMissing('room.room_type.motel.motel_imgs.img_details');
        $postRes = new  PostResource($postWith);
        $rooms = null;
        if ($post->post_type_id == 1) {
            $rooms = $post->room_type->none_rooms;
        }
        return response()->json([
            'statusCode' => 1,
            'post' => $postRes,
            'post_id' => $post_id,
            'rooms' => $rooms,
        ]);
    }
    public function sendIntoNoti(Request $request)
    {
        $title = 'muốn vào phòng trọ';
        $content = '';
        $user = User::find($request->user()->id);
        $content .= 'phòng muốn vào : ' . implode(", ", $request->ListRooms) . '<br>';
        $content .= 'họ tên: ' . $user->name . '<br/>';
        $content .= ' điện thoại: ' . $user->phone_number . '<br/>';
        $content .= ' nghề: ' . $user->job . '<br/>';
        $user = Post::find($request->postId)->room_type->motel->user;
        $senderId = $request->user()->id;
        $receiverId = $user->id;
        NotiController::sendNotiChoose($title, $senderId, $receiverId, $content, 4, null, 0);
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function sendIntoNotiRoom(Request $request)
    {
        $title = 'muốn vào phòng trọ';
        $content = '';
        $user = User::find($request->user()->id);
        $content .= 'họ tên: ' . $user->name . '<br/>';
        $content .= ' điện thoại: ' . $user->phone_number . '<br/>';
        $content .= ' nghề: ' . $user->job . '<br/>';
        $users = Post::find($request->postId)->room->latest_tenant->tenant_users;
        $senderId = $request->user()->id;
        foreach ($users as $user) {
            $receiverId = $user->user->id;
            NotiController::sendNotiChoose($title, $senderId, $receiverId, $content, 4, null, 0);
        }
        return response()->json([
            'statusCode' => 1,
        ]);
    }
}
