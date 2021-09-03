<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $room_statuses = $this->whenLoaded('room_status');
        return [
            'id' => $this->id ,
            'name' =>$this->name ,
            'room_status' => new RoomStatusResource($room_statuses),
        ];
    }
}
