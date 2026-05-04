<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gift;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;



class GiftController extends Controller
{
    public function index(Request $request)
    {
    
        $gifts = Gift::with('policy:id,program_name')
        ->select('id', 'policy_id', 'gift_name', 'point_slab', 'policy_type', 'gift_type', 'image' )
        ->paginate(10);

        // transform path
        $gifts->getCollection()->transform(function ($gift) {
            return [
                'policy_name' => $gift->policy->program_name ?? null,
                'id'          => $gift->id,
                'policy_id'   => $gift->policy_id,
                'gift_name'   => $gift->gift_name,
                'point_slab'  => $gift->point_slab,
                'policy_type' => $gift->policy_type,
                'gift_type'   => $gift->gift_type,
                'image'       => $gift->image 
                    ? asset('storage/' . $gift->image) 
                    : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Gift list fetched successfully',
            'data' => $gifts->items(),
            'pagination' => [
                'current_page' => $gifts->currentPage(),
                'last_page' => $gifts->lastPage(),
                'total' => $gifts->total(),
            ]
        ]);

    }
}
