<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiftTransaction;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Gift;

class GiftTransactionController extends Controller
{
    public function index(Request $request)
    {
        
        $transactions = GiftTransaction::with(['user:id,name', 'gift:id,gift_name,point_slab,image,is_point_cut', 'policy:id,program_name'])
            ->where('user_id', auth()->id())
            // ->where('user_id', $request->user_id)
            ->latest()
            ->paginate(10);

        $transactions->getCollection()->transform(function ($transaction) {
            $statusMap = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];

            return [
                'request_id'=> $transaction->id,
                'name'            => $transaction->user->name ?? null,
                'gift_name'       => $transaction->gift->gift_name ?? null,
                'policy_name'     => $transaction->policy->program_name ?? null,
                'point'      => $transaction->gift->point_slab ?? null,
                'point_cut'       => ($transaction->gift->is_point_cut ?? 0) ? 'Yes' : 'No',
                'image'           => $transaction->gift->image 
                    ? asset('storage/' . $transaction->gift->image) 
                    : null,
                'request_date'    => $transaction->requested_at ? $transaction->requested_at->format('Y-m-d') : null,
                'request_status'  => $statusMap[$transaction->request_status] ?? 'Unknown',
                'delivery_status' => $transaction->delivery_status,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => $transactions->isEmpty() ? 'No transactions found.' : 'Transaction history fetched successfully',
            'data' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }

    public function transactionDetails($id)
    {
        $transaction = GiftTransaction::with(['user:id,name,email', 'gift', 'policy:id,program_name'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $statusMap = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
        ];

        return response()->json([
            'status' => true,
            'message' => 'Transaction details fetched successfully',
            'data' => [
                'request_id'      => $transaction->id,
                'name'            => $transaction->user->name ?? null,
                'email'           => $transaction->user->email ?? null,
                'gift_name'       => $transaction->gift->gift_name ?? null,
                'policy_name'     => $transaction->policy->program_name ?? null,
                'point'           => $transaction->gift->point_slab ?? null,
                'point_cut'       => ($transaction->gift->is_point_cut ?? 0) ? 'Yes' : 'No', 
                'requested_at'    => $transaction->requested_at ? $transaction->requested_at->format('d M Y, h:i A') : null,
                'request_status'  => $statusMap[$transaction->request_status] ?? 'Unknown',
                'delivery_status' => $transaction->delivery_status,
                'received'        => $transaction->received ? 'Yes' : 'Not Received Yet',
            ]
        ]);
    }
}
