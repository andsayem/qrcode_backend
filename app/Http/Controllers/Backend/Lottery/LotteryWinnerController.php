<?php

namespace App\Http\Controllers\Backend\Lottery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LotteryWinner; // Ensure this model exists in app/Models/LotteryWinner.php
use App\Models\Lottery;
use App\Models\UserPoint;

class LotteryWinnerController extends Controller
{
   /**
     * Display a listing of the lottery winners.
     */
    public function index(Request $request)
    {
        $query = LotteryWinner::with(['lottery', 'giftAssign.gift', 'user']);

        // Search by winner name or mobile number or user ID - grouped to prevent conflict with other filters
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('winner_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('mobile_no', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('user_id', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($uq) use ($request) {
                      $uq->where('email', 'LIKE', '%' . $request->search . '%')
                         ->orWhere('phone_number', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by lottery
        if ($request->filled('lottery_id')) {
            $query->where('lottery_id', $request->lottery_id);
        }

        $winners = $query->latest()->paginate(10);

        $lotteries = Lottery::all();

        return view('backend.lotteries.lotteryWinner.index', compact('winners', 'lotteries'));
    }

    /**
     * Display the details of a specific winner.
     */
    public function show(LotteryWinner $winner)
    {
        $winner->load(['lottery', 'giftAssign.gift', 'user']);

        $achievedPoints = UserPoint::where('user_id', $winner->user_id)
            ->whereBetween('created_at', [
                $winner->lottery->from_date->startOfDay(),
                $winner->lottery->to_date->endOfDay()
            ])
            ->sum('point');

        return view('backend.lotteries.lotteryWinner.show', compact('winner', 'achievedPoints'));
    }

    /**
     * Remove the specified winner from storage.
     */
    public function destroy(LotteryWinner $winner)
    {
        $winner->delete();

        return redirect()
            ->route('admin.lottery-winners.index')
            ->with('success', 'Winner deleted successfully.');
    }
}
