<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email, 
            'profile_image' => $this->profile_image ? url('storage/profile/'.$this->profile_image) : '' ,
            'created_at' => $this->created_at,
            'user_types' => $this->user_types,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
