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
        $latest_tenant = $this->whenLoaded('latest_tenant');
        $room_type = $this->whenLoaded('room_type');
        return [
            'id' => $this->id ,
            'name' =>$this->name ,
            'room_status_id' =>$this->room_status_id ,
            'room_status' => new RoomStatusResource($room_statuses),
            'tenant'=> new TenantResource($latest_tenant),
            'room_type'=> new RoomTypeResource($room_type),
        ];
    }
}
