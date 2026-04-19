<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TechnicianResource extends JsonResource
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
            'payment_gateway' => $this->payment_gateway ? $this->payment_gateway : 0 ,
            'gatway_number' => $this->gatway_number ? $this->gatway_number : '',
            'user_id' => $this->user_id,
            'nid_font' => $this->nid_font ? $this->nid_font : '',
            'nid_back' => $this->nid_back ? $this->nid_back : '', 
            'referral_code' => $this->referral_code ? $this->referral_code  : '',
            'total_point' => $this->total_point ? $this->total_point : 0,
            'total_redeem_value' => $this->total_redeem_value ?  $this->total_redeem_value  : 0,
            'current_point' => $this->current_point ?   $this->current_point : 0,
            'pending_point' => $this->pending_point ?  $this->pending_point  : 0,
            'father_name' => $this->father_name ? $this->father_name : '',
            'permanent_address' => $this->permanent_address ? $this->permanent_address : '',
            'current_address' => $this->current_address ?  $this->current_address : '',
            'birthday' => $this->birthday ? $this->birthday : '',
            'occupation' => $this->occupation ? $this->occupation :'',
            'nid_number' => $this->nid_number ? $this->nid_number : '',
            'blood_group' => $this->blood_group ? $this->blood_group : '',
            'experience' => $this->experience ? $this->experience : '', 
            'dealer_code' => $this->dealer_code ?  $this->dealer_code : '', 
            'dealer_name' => $this->dealer_name ?  $this->dealer_name :'',
            'organization' => $this->organization ?  $this->organization : '',
            'zone' => $this->zone ?  $this->zone : '',
            'education' => $this->education ?  $this->education :'',
            'update_status' => $this->update_status ?  $this->update_status :0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at 
        ];
    }
}
