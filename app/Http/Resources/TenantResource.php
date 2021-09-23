<?php

namespace App\Http\Resources;

use App\Models\TenantUser;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
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
        $infor_tenant_users = $this->whenLoaded('infor_tenant_users');
        $tenant_room_equip = $this->whenLoaded('tenant_room_equips');
        $bills = $this->whenLoaded('bills');

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
            'infor_tenant_users' => TenantUserResource::collection($infor_tenant_users),
        ];
    }
}
