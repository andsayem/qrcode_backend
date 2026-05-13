<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lottery;
use App\Models\LotteryWinner;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LotteryApiController extends Controller
{
    /**
     * GET /api/lotteries/current
     * The main polling endpoint for the mobile app.
     */
    public function current()
    {
        // Cache for 3 seconds to handle burst polling during live draws
        $data = Cache::remember('lottery_poll_current', 3, function () {
            $lottery = Lottery::with(['giftAssignments.gift', 'winners.giftAssign.gift'])
                ->where('to_date', '>=', now()->toDateString())
                ->whereIn('status', ['pending', 'running', 'completed'])
                ->latest()
                ->first();

            if (!$lottery) {
                return null;
            }

            $winnersCount = $lottery->winners->count();
            $currentPos = $lottery->current_position ?? 0;
            $total = $lottery->total_winners;

            // Derive status for the app
            $status = "open";
            if ($currentPos == $total) {
                $status = "completed";
            } elseif ($currentPos > 0) {
                $status = "live";
            }

            $pollInterval = ($status === 'live') ? 4000 : 30000;

            return [
                'id' => $lottery->id,
                'title' => $lottery->title,
                'status' => $status,
                'total_winners' => $total,
                'winners_count' => $winnersCount,
                'current_position' => $currentPos,
                'poll_interval_ms' => $pollInterval,
                'gifts' => $lottery->giftAssignments->sortBy('position')->values()->map(function ($ga) {
                    return [
                        'position' => $ga->position,
                        'gift_name' => $ga->gift->gift_name ?? 'N/A',
                        'gift_image' => ($ga->gift && $ga->gift->gift_image) 
                            ? asset('uploads/lottery_gifts/' . $ga->gift->gift_image) 
                            : null,
                    ];
                }),
                'winners' => $lottery->winners->sortBy('position')->values()->map(function ($winner) {
                    $mobile = $winner->mobile_no;
                    $masked = (strlen($mobile) >= 6) ? substr($mobile, 0, 3) . '****' . substr($mobile, -3) : $mobile;

                    return [
                        'user_id' => $winner->user_id,
                        'name' => $winner->winner_name,
                        'mobile' => $masked,
                        'position' => $winner->position,
                        'position_label' => $this->ordinal($winner->position) . ' Place',
                        'gift_name' => $winner->giftAssign->gift->gift_name ?? 'N/A',
                    ];
                }),
            ];
        });

        if (!$data) {
            return response()->json(['data' => null, 'message' => 'No active lottery at the moment.']);
        }

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/lotteries/{lottery}/winners
     */
    public function getWinnersList(Lottery $lottery)
    {
        $winners = $lottery->winners()
            ->with(['giftAssign.gift'])
            ->orderBy('position', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $winners->map(function ($winner, $index) {
                $mobile = $winner->mobile_no;
                $masked = (strlen($mobile) >= 6) ? substr($mobile, 0, 3) . '****' . substr($mobile, -3) : $mobile;

                return [
                    'sl' => $index + 1,
                    'position' => $winner->position,
                    'position_label' => $this->ordinal($winner->position) . ' Place',
                    'name' => $winner->winner_name,
                    'mobile' => $masked,
                    'gift_name' => $winner->giftAssign->gift->gift_name ?? 'N/A',
                    'drawn_at' => $winner->draw_time ? $winner->draw_time->toIso8601String() : null,
                ];
            }),
        ]);
    }

    /**
     * GET /api/lotteries/history
     */
    public function history(Request $request)
    {
        // If a specific lottery_id is provided (e.g., user clicked a history item),
        // return the full winner details for that lottery.
        if ($request->filled('lottery_id')) {
            $lottery = Lottery::find($request->lottery_id);
            if (!$lottery) {
                return response()->json(['status' => 'error', 'message' => 'Lottery not found.'], 404);
            }
            return $this->getWinnersList($lottery);
        }

        $history = Lottery::where('current_position', '>=', DB::raw('total_winners'))
            ->with([
                'winners' => fn($q) => $q->where('position', 1),
                'giftAssignments' => fn($q) => $q->where('position', 1)->with('gift')
            ])
            ->orderBy('completed_at', 'DESC')
            ->latest('updated_at')
            ->paginate(10);

        $history->getCollection()->transform(function ($l) {
            $topWinner = $l->winners->first();
            $topGift = $l->giftAssignments->first();

            return [
                'id' => $l->id,
                'title' => $l->title,
                'completed_at' => ($l->completed_at ?? $l->updated_at)->toIso8601String(),
                'total_winners' => $l->total_winners,
                'top_winner_name' => $topWinner->winner_name ?? 'N/A',
                'top_gift_name' => $topGift->gift->gift_name ?? 'N/A',
            ];
        });

        return response()->json($history);
    }
    
    /**
     * GET /api/lotteries/my-wins
     */
    public function myWins()
    {
        if (!auth()->check()) {
            return response()->json(['data' => []]);
        }

        $wins = LotteryWinner::where('user_id', auth()->id())
            ->with(['lottery', 'giftAssign.gift'])
            ->orderBy('draw_time', 'DESC')
            ->get();

        return response()->json([
            'data' => $wins->map(function ($win) {
                return [
                    'lottery_id' => $win->lottery_id,
                    'lottery_title' => $win->lottery->title ?? 'N/A',
                    'position' => $win->position,
                    'position_label' => $this->ordinal($win->position) . ' Place',
                    'gift_name' => $win->giftAssign->gift->gift_name ?? 'N/A',
                    'drawn_at' => $win->draw_time ? $win->draw_time->toIso8601String() : null,
                ];
            })
        ]);
    }

    private function ordinal($n)
    {
        $suffix = ['th','st','nd','rd'];
        $v = $n % 100;
        if ($v >= 11 && $v <= 13) {
            return $n . 'th';
        }
        $idx = $n % 10;
        return $n . ($suffix[$idx] ?? $suffix[0]);
    }
    
}
