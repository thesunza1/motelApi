<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $room = $this->whenLoaded('room') ;
        $comments = $this->whenLoaded('comments') ;
        $room_type = $this->whenLoaded('room_type') ;
        $post_type = $this->whenLoaded('post_type') ;
        return [
            'id' => $this->id ,
            'title' => $this->title ,
            'room_id' => $this->room_id,
            'room_type_id' => $this->room_type_id ,
            'conpound_content' => $this->conpound_content ,
            'status' => $this->status,
            'post_type_id' => $this->post_type_id ,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'room' => new RoomResource($room),
            'room_type' => new RoomTypeResource($room_type),
            'post_type' => new PostTypeResource($post_type),
            'comments' => CommentResource::collection($comments),
        ];
    }
}
