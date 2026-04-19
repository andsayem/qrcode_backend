<?php

namespace App\Http\Controllers\Backend\Gift;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftTransaction;

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

    $transactions = $query->latest()->paginate(20);

    // keep filter data in pagination
    $transactions->appends($request->all());

    // dropdown data (better performance than calling in blade)
    $gifts = \App\Models\Gift::select('id', 'gift_name')->get();
    $policies = \App\Models\GiftPolicy::select('id', 'program_name')->get();

    return view('backend.gifts.transactions.index', compact(
        'transactions',
        'gifts',
        'policies'
    ));
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
            'request_status' => 'pending',
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
        if ($transaction->request_status !== 'pending') {
            return redirect()->back()->with('error', 'Already processed!');
        }

        $transaction->update([
            'request_status' => 'approved',
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

        if ($transaction->request_status !== 'pending') {
            return redirect()->back()->with('error', 'Already processed!');
        }

        $transaction->update([
            'request_status' => 'rejected',
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

        if ($transaction->request_status !== 'approved') {
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
}