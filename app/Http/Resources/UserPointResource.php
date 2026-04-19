<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPointResource extends JsonResource
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
            'point' => $this->point,
            'point_type'=> $this->point_type,
            'note'=> $this->note,
            'product' => $this->product,
            'procode' => $this->procode,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
