<?php

namespace App\Http\Controllers\Backend\Gift;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftTransaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftTransactionsExport;
use App\Models\Gift;
use App\Models\Technician;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftTransactionController extends Controller
{
    /**
     * List all transactions (Admin)
     */
    public function index(Request $request)
    {
        $query = GiftTransaction::with(['user', 'gift', 'policy']);

        // 👤 User filter (name/email/phone)
        if ($request->user) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->user}%")
                    ->orWhere('email', 'like', "%{$request->user}%");
            });
        }

        // 🎁 Gift filter
        if ($request->gift_id) {
            $query->where('gift_id', $request->gift_id);
        }

        // 📋 Policy filter
        if ($request->policy_id) {
            $query->where('policy_id', $request->policy_id);
        }

        // 📅 Date filter (requested_at)
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('requested_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        // Request Status filter (default to pending)
        $status = $request->get('request_status', 0);

        if ($status == 'sent') {
            $query->where('delivery_status', 'sent');
        } else {
            $query->where('request_status', $status);
        }


        $transactions = $query->latest()->paginate(20);

        // keep filter data in pagination
        $transactions->appends($request->all());

        // dropdown data (better performance than calling in blade)
        $gifts = \App\Models\Gift::select('id', 'gift_name')->get();
        $policies = \App\Models\GiftPolicy::select('id', 'program_name')->get();

        return view('backend.gifts.transactions.index', compact(
            'transactions',
            'gifts',
            'policies',
            'status'
        ));
    }
    /**
     * Export gift transactions to Excel
     */
    public function export(Request $request)
    {
        $filters = $request->all();

        // Default to pending (0) if no status is provided
        if (!isset($filters['request_status'])) {
            $filters['request_status'] = 0;
        }

        return Excel::download(new GiftTransactionsExport($filters), 'gift_transactions.xlsx');
    }
    /**
     * User request gift
     */
    public function store(Request $request)
    {
        $request->validate([
            'gift_id' => 'required|exists:gifts,id',
            'policy_id' => 'nullable|exists:gift_policies,id',
        ]);

        GiftTransaction::create([
            'user_id' => auth()->id(),
            'gift_id' => $request->gift_id,
            'policy_id' => $request->policy_id,
            'request_status' => 0,
            'delivery_status' => 'not_sent',
            'requested_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Gift request sent successfully!');
    }

    /**
     * Approve request (Admin)
     */
    public function approve($id)
    {
        $transaction = GiftTransaction::with('gift')->findOrFail($id);

        // prevent re-approve
        if ($transaction->request_status !== 0) {
            return redirect()->back()->with('error', 'Already processed!');
        }

        DB::beginTransaction();
        try {
            $gift = $transaction->gift;
            if ($gift->is_point_cut == 1) {
                $technician = Technician::where('user_id', $transaction->user_id)->first();
                if ($technician) {
                    $technician->pending_point -= $gift->point_slab;
                    $technician->save();
                }
            }

            $transaction->update([
                'request_status' => 1,
                'approved_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Request approved!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    /**
     * Reject request (Admin)
     */
    public function reject($id)
    {
        $transaction = GiftTransaction::with('gift')->findOrFail($id);

        if ($transaction->request_status !== 0) {
            return redirect()->back()->with('error', 'Already processed!');
        }

        DB::beginTransaction();
        try {
            $gift = $transaction->gift;
            if ($gift->is_point_cut == 1) {
                $technician = Technician::where('user_id', $transaction->user_id)->first();
                if ($technician) {
                    $technician->current_point += $gift->point_slab;
                    $technician->pending_point -= $gift->point_slab;
                    $technician->save();
                }
            }

            $transaction->update([
                'request_status' => 2,
                'approved_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Request rejected!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject: ' . $e->getMessage());
        }
    }

    /**
     * Send gift (Admin)
     */
    public function send($id)
    {
        $transaction = GiftTransaction::findOrFail($id);

        if ($transaction->request_status !== 1) {
            return redirect()->back()->with('error', 'Must be approved first!');
        }

        $transaction->update([
            'delivery_status' => 'sent',
            'sent_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Gift sent!');
    }

    /**
     * User confirm received
     */
    public function received($id)
    {
        $transaction = GiftTransaction::findOrFail($id);

        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($transaction->delivery_status !== 'sent') {
            return redirect()->back()->with('error', 'Gift not sent yet!');
        }

        $transaction->update([
            'delivery_status' => 'received',
            'received_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Gift received confirmed!');
    }

    /**
     * Show single transaction (Tracking page)
     */
    public function show($id)
    {
        $transaction = GiftTransaction::with(['user', 'gift', 'policy'])
            ->findOrFail($id);

        return view('backend.gifts.transactions.show', compact('transaction'));
    }
    /**
     * Bulk approve gifts
     */
    public function bulkApprove(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:gift_transactions,id',
        ]);

        // Get the selected transaction IDs
        $transactionIds = $request->input('transaction_ids');

        DB::beginTransaction();
        try {
            $transactions = GiftTransaction::with('gift')
                ->whereIn('id', $transactionIds)
                ->where('request_status', 0)
                ->get();

            foreach ($transactions as $transaction) {
                $gift = $transaction->gift;
                if ($gift->is_point_cut == 1) {
                    $technician = Technician::where('user_id', $transaction->user_id)->first();
                    if ($technician) {
                        $technician->pending_point -= $gift->point_slab;
                        $technician->save();
                    }
                }
                $transaction->update(['request_status' => 1, 'approved_at' => now()]);
            }
            DB::commit();
            return response()->json(['message' => 'Gifts Approved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk send gifts
     */
    public function bulkSend(Request $request)
    {
        $request->validate([
            'transaction_ids' => 'required|array|min:1',
            'transaction_ids.*' => 'exists:gift_transactions,id',
        ]);

        GiftTransaction::whereIn('id', $request->transaction_ids)
            ->where('request_status', 1) // only approved
            ->where('delivery_status', 'not_sent') // not sent yet
            ->update([
                'delivery_status' => 'sent',
                'sent_at' => now(),
            ]);

        return response()->json([
            'message' => 'Gifts Send successfully'
        ]);
    }

    public function redeemApi(Request $request)
    {
        $request->validate([
            'gift_id' => 'required|exists:gifts,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // $user = auth()->user();
        $user = \App\Models\User::with('technician')->findOrFail($request->user_id);
        $gift = Gift::findOrFail($request->gift_id);

        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            return response()->json([
                'success' => false,
                'message' => 'Technician profile not found.'
            ], 404);
        }

        // 1. Point Balance Validation (Check points first as requested)
        if ($gift->is_point_cut != 0) {
            if ($technician->current_point < $gift->point_slab) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient points available for this gift. Balance: {$technician->current_point} points, Required: {$gift->point_slab} points."
                ], 400);
            }
        } else {
            // LOCKED System (If Point Cut is set to No in form)
            $totalLockedPoints = GiftTransaction::join('gifts', 'gift_transactions.gift_id', '=', 'gifts.id')
                ->where('gift_transactions.user_id', $user->id)
                ->where('gifts.is_point_cut', 0)
                ->whereIn('gift_transactions.request_status', [0, 1])
                ->sum('gifts.point_slab');

            $availableForUse = $technician->current_point - $totalLockedPoints;

            if ($availableForUse < $gift->point_slab) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient available points. You have {$availableForUse} points free for this gift."
                ], 400);
            }
        }

        // 2. Only apply payment gateway validations if the gift type is 'payment_gateway'
        if ($gift->gift_type === 'payment_gateway') {
            // Specifically check if Nagad (2) or Rocket (3) is saved in the technician table
            if (in_array($technician->payment_gateway, [2, 3])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Currently only bKash is supported. Please change your payment gateway to bKash and provide a valid bKash number in your profile.'
                ], 200);
            }

            // General check to ensure bKash (1) is configured and number is available
            if ($technician->payment_gateway != 1 || empty($technician->gatway_number)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please set your Payment Gateway to bKash and provide a valid number in your profile.'
                ], 200);
            }

            // Validate gateway number format (11 digits)
            $number = preg_replace('/^\+?88/', '', $technician->gatway_number);
            if (strlen($number) != 11 || !ctype_digit($number)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your bKash number must be exactly 11 digits.'
                ], 200);
            }
        }

        // 3. Policy Frequency Check
        $alreadyRedeemed = GiftTransaction::where('user_id', $user->id)
            ->where('gift_id', $gift->id)
            ->whereBetween('requested_at', [$gift->policy->start_date, $gift->policy->end_date])
            ->whereIn('request_status', [0, 1])
            ->count();



        if ($alreadyRedeemed >= $gift->max_redeem_limit) {
            return response()->json([
                'success' => false,
                'message' => 'This gift has already been requested in this period.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Re-fetch technician with lock inside transaction to ensure point deduction is accurate
            $technician = Technician::where('user_id', $user->id)->lockForUpdate()->first();

            // Deduct points from the technician's wallet if applicable.
            if ($gift->is_point_cut != 0) {
                if ($technician->current_point < $gift->point_slab) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient points available for this gift. Balance: {$technician->current_point} points, Required: {$gift->point_slab} points."
                    ], 400);
                }

                // Move points from current to pending until admin approves
                $technician->current_point -= $gift->point_slab;
                $technician->pending_point += $gift->point_slab;
                $technician->save();
            }

            $transaction = GiftTransaction::create([
                'user_id' => $user->id,
                'gift_id' => $gift->id,
                'policy_id' => $gift->policy_id,
                'request_status' => 0,
                'delivery_status' => 'not_sent',
                'requested_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gift requested successfully',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
