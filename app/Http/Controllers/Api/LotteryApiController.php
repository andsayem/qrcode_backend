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
            $lottery = Lottery::with(['giftAssignments.gift', 'winners.giftAssign.gift', 'winners.user'])
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('status', 'running')
                            ->where('current_position', '>', 0); // Only running lotteries with at least one draw
                    })
                        ->orWhere(function ($q) {
                            // Allow the completed lottery to be visible for a 60-second window
                            // so the app can fetch and display the final winner board.
                            $q->where('status', 'completed')
                                ->where('completed_at', '>=', now()->subSeconds(60));
                        });
                })
                ->latest('updated_at')
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
                    $mobile = $winner->mobile_no ?: ($winner->user->phone_number ?? $winner->user->email ?? 'N/A');
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
     * GET /api/lotteries/history
     */

    public function history(Request $request)
    {

        if ($request->filled('lottery_id')) {

            $lottery = Lottery::find($request->lottery_id);

            if (!$lottery) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Lottery not found.',
                ], 404);
            }

            // Only show completed lotteries in history
            if ($lottery->current_position < $lottery->total_winners) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This lottery has not completed yet.',
                ], 422);
            }

            $winners = LotteryWinner::where('lottery_id', $lottery->id)
                ->with(['giftAssign.gift', 'user'])
                ->orderBy('position', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',

                // Upper section — lottery info card
                'lottery' => [
                    'id'           => $lottery->id,
                    'title'        => $lottery->title,
                    'total_winners' => $lottery->total_winners,
                    'completed_at' => $lottery->updated_at
                        ? $lottery->updated_at->format('d M Y, h:i A')
                        : 'N/A',
                ],

                // Lower section — winner list table
                // Columns: SL | User ID | Winner Name | Mobile No | Gift | Winning Position
                'winner_list' => $winners->values()->map(function ($winner, $index) {
                    $mobile = $winner->mobile_no
                        ?: ($winner->user->phone_number
                            ?? $winner->user->email
                            ?? 'N/A');

                    $masked = (strlen($mobile) >= 6)
                        ? substr($mobile, 0, 3) . '****' . substr($mobile, -3)
                        : $mobile;

                    return [
                        'sl'               => $index + 1,                        // row number
                        'user_id'          => $winner->user_id,
                        'winner_name'      => $winner->winner_name,
                        'mobile_no'        => $masked,
                        'gift'             => $winner->giftAssign->gift->gift_name ?? 'N/A',
                        'winning_position' => $winner->position,
                        'position_label'   => $this->ordinal($winner->position) . ' place',
                        'draw_time'        => $winner->draw_time
                            ? $winner->draw_time->format('d M Y, h:i A')
                            : 'N/A',
                    ];
                }),
            ]);
        }

        // -----------------------------------------------------------------------
        // LIST VIEW — show all completed lotteries for selection
        // GET /api/lotteries/history
        // -----------------------------------------------------------------------

        // Derive completed: current_position = total_winners
        // No status column — use column comparison
        $lotteries = Lottery::whereColumn('current_position', 'total_winners')
            ->where('total_winners', '>', 0)     // exclude lotteries never started
            ->latest('updated_at')
            ->paginate(10);

        $lotteries->getCollection()->transform(function ($lottery) {

            // Get position 1 winner (grand prize) for the preview card
            $topWinner = LotteryWinner::where('lottery_id', $lottery->id)
                ->where('position', 1)
                ->with(['giftAssign.gift'])
                ->first();

            return [
                'id'           => $lottery->id,
                'title'        => $lottery->title,
                'total_winners' => $lottery->total_winners,
                'completed_at' => $lottery->updated_at
                    ? $lottery->updated_at->format('d M Y')
                    : 'N/A',
                // Preview info for the lottery card in the list
                'top_winner'   => $topWinner ? $topWinner->winner_name : 'N/A',
                'top_gift'     => $topWinner && $topWinner->giftAssign && $topWinner->giftAssign->gift ? $topWinner->giftAssign->gift->gift_name : 'N/A',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $lotteries,
        ]);
    }



    private function ordinal($n)
    {
        $suffix = ['th', 'st', 'nd', 'rd'];
        $v = $n % 100;
        if ($v >= 11 && $v <= 13) {
            return $n . 'th';
        }
        $idx = $n % 10;
        return $n . ($suffix[$idx] ?? $suffix[0]);
    }
}
