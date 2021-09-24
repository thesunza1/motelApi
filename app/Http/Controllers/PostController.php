<?php

namespace App\Http\Controllers;

use App\Models\TenantUser;
use Illuminate\Http\Request;
use App\Models\Post;

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

    public function getPost(Request $request) {
        
    }
    public function getSearch(Request $request) {

    }
}
