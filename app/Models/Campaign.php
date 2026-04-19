<?php

namespace App\Models;
use App\Models\CampaignDetails;
use Auth ;
use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class Campaign
 * @package App\Models
 * @version April 2, 2022, 12:44 pm +06
 *
 * @property integer $campaign_category_id
 * @property string $start_date
 * @property string $end_date
 * @property integer $product_id
 * @property string $point
 * @property string $title
 * @property string $image
 */
class Campaign extends Model
{

    public $table = 'campaigns';

    public $fillable = [
        'campaign_category_id',
        'start_date',
        'end_date',
        'product_id',
        'point',
        'title',
        'number_of_scan',
        'image',
        'campaign_type',
        'content_type',
        'link',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'campaign_category_id' => 'integer',
        'start_date' => 'string',
        'end_date' => 'string',
        'product_id' => 'integer',
        'point' => 'string',
        'title' => 'string',
        'image' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title'=> 'required|string|max:255',
        'campaign_type'  => 'required|string',
        'product_id'     => 'required_if:campaign_type,campaign_with_product',
        'point'          => 'required_if:campaign_type,campaign_with_product',
        'start_date'      => 'required',
        'end_date'        => 'required',
        'content_type'   => 'required|string|in:image,link',
        'image'          => 'required_if:content_type,image|image|mimes:jpg,jpeg,png|max:2048',
        'link'           => 'required_if:content_type,link',
    ];


    public function numberOfScan(){
        $user_data = Auth::user(); 
        $data  =  CampaignDetails::where('campaign_id', $this->id)->where('user_id', $user_data->id)->first();
        if( $data){
           return $data->number_of_scan ? $data->number_of_scan : 0 ;
        }else{
            return 0 ;
        }
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function category(){
        return $this->belongsTo(CampaignCategory::class, 'campaign_category_id');
    }
    
}
