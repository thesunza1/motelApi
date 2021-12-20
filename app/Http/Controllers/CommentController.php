<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //
    public function getAllComment(Request $request) {
        $postId = $request->post_id ;
        $comments = Comment::where('post_id','=',$postId)->with('user')->paginate(10);
        // $commentRes = CommentResource::collection($comments);

        return response()->json([
            'statusCode' => 1 ,
            'postComments' => $comments,
        ]);
    }
    public function createComment(Request $request) {
        $post = Post::find($request->post_id) ;
        $content = $request->content ;
        $nowTime = Carbon::now();

        $post->comments()->create([
            'content' => $content,
            'user_id' => $request->user()->id,
            'created_at' => $nowTime,
            'updated_at' => $nowTime,
        ]);
        return response()->json([
            'statusCode' => 1 ,
        ]);
    }
}
