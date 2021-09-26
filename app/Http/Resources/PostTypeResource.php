<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $posts = $this->whenLoaded('posts');
        return [
            'id' => $this->id ,
            'name' => $this->name ,
            'post' => PostResource::collection($posts->paginate(10)),

        ];
    }
}
