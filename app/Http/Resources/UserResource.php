<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $role = $this->whenLoaded(relationship: 'role');
        return [
            'id' => $this->id ,
            'email' => $this->email ,
            'name' => $this->name,
            'phone_number' => $this->phone_number ,
            'job' => $this->job ,
            'address' =>$this->address,
            'birth_date' => $this->birth_date,
            'role' =>new RoleResource($role),
        ];
    }
}
