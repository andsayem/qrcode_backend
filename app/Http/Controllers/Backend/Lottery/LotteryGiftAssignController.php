<?php

namespace App\Http\Controllers\Backend\Lottery;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use App\Models\LotteryGift;
use App\Models\LotteryGiftAssign;
use Illuminate\Http\Request;

class LotteryGiftAssignController extends Controller
{
    /**
     * Show gift assign page
     */
    public function index(Lottery $lottery)
    {
        $gifts = LotteryGift::all();

        $assignedGifts = LotteryGiftAssign::with('gift')
            ->where('lottery_id', $lottery->id)
            ->orderBy('position')
            ->get();

        return view('backend.lotteries.gift-assign', compact(
            'lottery',
            'gifts',
            'assignedGifts'
        ));
    }

    /**
     * Store / Update multiple gift assigns
     */
    public function store(Request $request, Lottery $lottery)
    {
        $request->validate([
            'gifts' => 'required|array'
        ]);

        foreach ($request->gifts as $item) {

            if (!isset($item['gift_id']) || !isset($item['position'])) {
                continue;
            }

            LotteryGiftAssign::updateOrCreate(
                [
                    'lottery_id' => $lottery->id,
                    'position'   => $item['position'],
                ],
                [
                    'gift_id' => $item['gift_id'],
                ]
            );
        }

        return back()->with('success', 'Gift assignments saved successfully.');
    }

    /**
     * Remove assigned gift
     */
    public function destroy($id)
    {
        LotteryGiftAssign::findOrFail($id)->delete();

        return back()->with('success', 'Gift removed successfully.');
    }
}
