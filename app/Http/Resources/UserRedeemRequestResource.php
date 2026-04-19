<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRedeemRequestResource extends JsonResource
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
            'user_id' => $this->user_id,
            'point' => $this->point,
            'amount' => $this->amount,
            'pin' => $this->opt_code,
            'payment_gateway' => $this->payment_gateway,
            'gateway_number' => $this->gateway_number,
            'details' => $this->details,
            'status' => $this->status,
            'sap_code' => $this->sender_sap_code,
            'otp_send_time' => $this->otp_send_time ? date('d M Y',strtotime($this->otp_send_time)): '',
            'request_date' => $this->created_at ? date('d M Y',strtotime($this->created_at)): '',
            'user' => $this->user,
            'technician' => $this->technician, 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
