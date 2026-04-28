<?php

namespace App\Http\Controllers\Backend\Gift;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftTransaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftTransactionsExport;

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
        } 
        else {
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

}