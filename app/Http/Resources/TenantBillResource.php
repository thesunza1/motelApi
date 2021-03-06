<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantBillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tenant_users = $this->whenLoaded('tenant_users');
        $tenant_room_equip = $this->whenLoaded('tenant_room_equips');
        $bills = $this->whenLoaded('bills');
        // $num_bills = $this->whenLoaded('num_bills');
        $no_bills = $this->whenLoaded('no_bills');

        return [
            'id' => $this->id ,
            'eq_status' => $this->eq_status,
            'elec_num' => $this->elec_num,
            'water_num' => $this->water_num,
            'num_status' => $this->num_status,
            'in_date' => $this->in_date,
            'out_date' => $this->out_date,
            'status' => $this->status,
            'room_id' => $this->room_id ,
            'tenant_users' => TenantUserResource::collection($tenant_users),
            'tenant_room_equips' => TenantRoomEquipResource::collection($tenant_room_equip),
            // 'tenant_room_equips' =>
            'bills' => BillResource::collection($bills),
            'bill_num' =>count(BillResource::collection($bills)),
            // 'bill_num' =>count(BillResource::collection($num_bills)),
            'no_bills' =>count( BillResource::collection($no_bills)),
        ];
    }
}
