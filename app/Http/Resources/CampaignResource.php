<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'campaign_category_id' => $this->campaign_category_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'product_id' => $this->product_id,
            'point' => $this->point,
            'title' => $this->title,
            'image' => $this->image,
            'scan_limit' =>  $this->number_of_scan,
            'number_of_scan' => $this->numberOfScan(),
            'image_path' => $this->image ? url('storage/campaign/'.$this->image) : '' ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'campaign_type'=>$this->campaign_type,
            'content_type'=>$this->content_type,
            'link'=>$this->link,
        ];
    }
}
