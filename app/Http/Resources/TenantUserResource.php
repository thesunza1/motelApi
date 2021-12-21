<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $this->whenLoaded('user');
        return [
            'id' => $this->id ,
            'user_id' => $this->user_id ,
            'infor_share' => $this->infor_share,
            'user' => new UserResource($user),
        ];
    }
}
