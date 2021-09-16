<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id ,
            'tenant_id' => $this->tenant_id ,
            'status' => $this->status ,
            'date_begin' => $this->date_begin ,
            'date_end' => $this->date_end ,
            'elec_begin' => $this->elec_begin ,
            'elec_end' => $this->elec_end ,
            'water_begin' => $this->water_begin ,
            'water_end' => $this->water_end ,
            'cost' => $this->cost,
            'water_cost' => $this->water_cost ,
            'elec_cost' => $this->elec_cost,
            'people_cost' => $this->people_cost,
            'created_at' => $this->created_at ,
            'updated_at' => $this->updated_at ,
        ];
    }
}
