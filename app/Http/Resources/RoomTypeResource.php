<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $motel = $this->whenLoaded(relationship: 'motel');
        $rooms = $this->whenLoaded(relationship: 'rooms');
        $had_rooms = $this->whenLoaded(relationship: 'had_rooms');
        $img_details = $this->whenLoaded(relationship: 'img_details');
        return [
            'id' => $this->id ,
            'name' => $this->name,
            'area' => $this->erea,
            'cost' => $this->cost,
            'male' => $this->male,
            'female' => $this->female,
            'everyone' => $this->everyone,
            'content' => $this->content,
            // 'motel' => new MotelResource($motel),
            'rooms' =>  RoomResource::collection($rooms),
            'had_rooms' =>  RoomResource::collection($had_rooms),
            'img_details' => ImgDetailResource::collection($img_details),
        ];
    }
}
