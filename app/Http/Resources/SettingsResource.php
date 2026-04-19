<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
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
            'id' => $this->id,
            'min_redeem_point' => $this->min_redeem_point,
            'point_rate' => $this->point_rate,
            'created_at' => $this->created_at,
            'company_name' => $this->company_name ?? 'Super Star Group',
            'contact_number' => $this->contact_number ?? '+8809610774774',
            'email' => $this->email ?? 'info@ssgbd.com',
            'address' => $this->address ?? 'UCEP Cheyne Tower (3rd Floor), 25 Segunbagicha, Ramna, Dhaka-1000'
        ];
    }
}
