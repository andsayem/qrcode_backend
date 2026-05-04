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
        $transaction = GiftTransaction::findOrFail($id);

        // prevent re-approve
        if ($transaction->request_status !== 0) {
            return redirect()->back()->with('error', 'Already processed!');
        }

        $transaction->update([
            'request_status' => 1,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Request approved!');
    }

    /**
     * Reject request (Admin)
     */
    public function reject($id)
    {
        $transaction = GiftTransaction::findOrFail($id);

        if ($transaction->request_status !== 0) {
            return redirect()->back()->with('error', 'Already processed!');
        }

        $transaction->update([
            'request_status' => 2,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Request rejected!');
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

        // Update the request_status to 'approved' for the selected transactions
        GiftTransaction::whereIn('id', $transactionIds)
            ->where('request_status', 0)
            ->update([
                'request_status' => 1,
                'approved_at' => now(),
            ]);

        // Redirect back with a success message
        return response()->json([
            'message' => 'Gifts Approved successfully'
        ]);
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
            'user_id' => 'required|exists:users,id',
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

        // $currentPoints = UserPoint::where('user_id', $user->id)->sum('point');

        // dd([
        //     'user_points' => $currentPoints,
        //     'required_points' => $gift->point_slab
        // ]);
        // INSTANT Gift Logic (Points are "locked" instead of "cut")
        if ($gift->policy_type === 'instant') {
            // Calculate total points locked by all previous instant gift redemptions (Pending or Approved)
            $totalLockedPoints = GiftTransaction::join('gifts', 'gift_transactions.gift_id', '=', 'gifts.id')
                ->where('gift_transactions.user_id', $user->id)
                ->where('gifts.policy_type', 'instant')
                ->whereIn('gift_transactions.request_status', [0, 1])
                ->sum('gifts.point_slab');

            $availableForInstant = $technician->current_point - $totalLockedPoints;

            if ($availableForInstant < $gift->point_slab) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient available points. You have {$availableForInstant} points free for instant gifts."
                ], 400);
            }
        }

        //  YEAR-END
        if ($gift->policy_type === 'year_end') {

            // once per year
            $alreadyRedeemed = GiftTransaction::where('user_id', $user->id)
                ->where('gift_id', $gift->id)
                ->whereYear('requested_at', now()->year)
                ->whereIn('request_status', [0, 1])
                ->exists();

            if ($alreadyRedeemed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This year-end gift has already been requested this year.'
                ], 400);
            }

            if ($technician->current_point < $gift->point_slab) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient points available for this gift. Balance: {$technician->current_point} points, Required: {$gift->point_slab} points."
                ], 400);
            }
        }

        DB::beginTransaction();

        try {
            $transaction = GiftTransaction::create([
                'user_id' => $user->id,
                'gift_id' => $gift->id,
                'policy_id' => $gift->policy_id,
                'request_status' => 0,
                'delivery_status' => 'not_sent',
                'requested_at' => now(),
            ]);

            // Deduct points from the technician's wallet if applicable.
            // Per requirement: Instant gifts points are "locked" via balance validation, not "cut" (deducted).
            if ($gift->is_point_cut && $gift->policy_type !== 'instant') {
                if ($technician->current_point < $gift->point_slab) {
                    throw new \Exception('Insufficient points during deduction');
                }

                $technician->current_point -= $gift->point_slab;
                $technician->save();
            }

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
                // 'message' => 'Something went wrong. Please try again later.'
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
