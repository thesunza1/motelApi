<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantRoomEquipResource extends JsonResource
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
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'content' => $this->content,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'img_details' => ImgDetailResource::collection($img_details)
        ];
    }
}
