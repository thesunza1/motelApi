<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeBillResource extends JsonResource
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
        // $rooms = $this->whenLoaded(relationship: 'rooms');
        $had_rooms = $this->whenLoaded(relationship: 'had_rooms');
        return [
            'id' => $this->id ,
            'name' => $this->name,
            // 'area' => $this->erea,
            // 'cost' => $this->cost,
            // 'male' => $this->male,
            // 'female' => $this->female,
            // 'everyone' => $this->everyone,
            // 'content' => $this->content,
            // 'motel' => new MotelResource($motel),
            // 'rooms' =>  RoomBillResource::collection($rooms),
            'had_rooms' =>  RoomBillResource::collection($had_rooms),];
    }
}
