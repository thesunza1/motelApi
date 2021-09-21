<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MotelImgResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $img_details = $this->whenLoaded('img_details');
        return [
            'id' => $this->id,
            'place' => $this->place,
            'content' => $this->content,
            'img_type_id' => $this->img_type_id,
            'img_details' => ImgDetailResource::collection($img_details),
            'imgs' => null,
        ];
    }
}
