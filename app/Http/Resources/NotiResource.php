<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $senderUser = $this->whenLoaded('senderUser');
        $receiverUser = $this->whenLoaded('receiverUser');
        return [
            'id' => $this->id ,
            'title' => $this->title ,
            'senderUser' => new UserResource($senderUser),
            'receiverUser' => new UserResource($receiverUser),
            'content' => $this->content ,
            'status' => $this->status,
            'room_id' => $this->room_id ,
            'noti_type_id' =>$this->noti_type_id ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at ,
        ];
    }
}
