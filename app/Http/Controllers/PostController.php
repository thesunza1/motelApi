<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostType;

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
        $post = Post::find($post_id)->with('room_type.first_img_detail')->with('room.room_type.first_img_detail')
            ->with('room_type.motel.user')->with('room.room_type.motel.user')
            ->with('room.latest_tenant.tenant_users.user');


        return response()->json([
            'statusCode' => 1,
            'post' => $post,
        ]);
    }
}
