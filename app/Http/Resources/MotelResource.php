<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $room_types =  $this->whenLoaded('room_types');
        $motel_imgs =  $this->whenLoaded('motel_imgs');
        $user =  $this->whenLoaded('user');

        return [
            'id' => $this->id ,
            'name' => $this->name ,
            'address' => $this->address ,
            'phone_number' => $this->phone_number ,
            'latitude' => $this->latitude ,
            'longitude' => $this->longitude ,
            'closed' => $this->closed ,
            'open' => $this->open ,
            'camera' => $this->camera ,
            'parking' => $this->parking ,
            'deposit' => $this->deposit ,
            'elec_cost' => $this->elec_cost ,
            'water_cost' => $this->water_cost ,
            'people_cost' => $this->people_cost,
            'elec_more' => $this->elec_more,
            'content' => $this->content,
            'auto_post' => $this->auto_post,
            'room_types' =>RoomTypeResource::collection($room_types),
            // 'room_types' => RoomTypeResource::collection($this->room_types),
            'motel_imgs' => MotelImgResource::collection($motel_imgs),
            'user_id' =>$this->user_id ,
            'user' => new UserResource($user),
            'created_at' => $this->created_at ,
            'updated_at' => $this->updated_at ,
        ];
    }
}
