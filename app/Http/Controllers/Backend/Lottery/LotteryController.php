<?php

namespace App\Http\Controllers\Backend\Lottery;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use Illuminate\Http\Request;
use App\Models\LotteryGiftAssign;
use App\Models\LotteryWinner;
use App\Models\User;
use App\Models\UserPoint;
use App\Exports\LotteryEligibleExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LotteryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lottery::query();

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lotteries = $query
            ->latest()
            ->paginate(10);

        return view('backend.lotteries.index', compact('lotteries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.lotteries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'from_date'        => 'required|date',
            'to_date'          => 'required|date|after_or_equal:from_date',
            'draw_date'        => 'required|date|after_or_equal:to_date',
            'required_points'  => 'required|integer|min:0',
            'total_winners'    => 'required|integer|min:1',
            'status'           => 'required|in:pending,running,completed',
        ]);

        Lottery::create([
            'title'            => $request->title,
            'from_date'        => $request->from_date,
            'to_date'          => $request->to_date,
            'draw_date'        => $request->draw_date,
            'required_points'  => $request->required_points,
            'total_winners'    => $request->total_winners,
            'status'           => $request->status,
            'current_position' => 0,
            'started_at'       => $request->status == 'running'
                ? now()
                : null,
            'completed_at'     => $request->status == 'completed'
                ? now()
                : null,
        ]);

        return redirect()
            ->route('admin.lotteries.index')
            ->with('success', 'Lottery created successfully.');
    }

    public function eligible(Request $request, Lottery $lottery)
    {
        $users = $lottery->eligibleUsersDetailQuery($request->search)
            ->paginate(50)
            ->withQueryString();

        $totalEligible = $lottery->eligibleUsersCount();

        return view('backend.lotteries.eligible', compact('lottery', 'users', 'totalEligible'));
    }

    public function eligibleExport(Lottery $lottery)
    {
        $fileName = 'lottery_' . $lottery->id . '_eligible_technicians_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LotteryEligibleExport($lottery), $fileName);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lottery $lottery)
    {
        return view('backend.lotteries.show', compact('lottery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lottery $lottery)
    {
        if ($lottery->status === 'completed') {
            return redirect()->route('admin.lotteries.index')->with('error', 'Completed lotteries cannot be edited.');
        }
        return view('backend.lotteries.edit', compact('lottery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lottery $lottery)
    {
        if ($lottery->status === 'completed') {
            return redirect()->route('admin.lotteries.index')->with('error', 'Completed lotteries cannot be updated.');
        }

        $request->validate([
            'title'            => 'required|string|max:255',
            'from_date'        => 'required|date',
            'to_date'          => 'required|date|after_or_equal:from_date',
            'draw_date'        => 'required|date|after_or_equal:to_date',
            'required_points'  => 'required|integer|min:0',
            'total_winners'    => 'required|integer|min:1',
            'status'           => 'required|in:pending,running,completed',
        ]);

        $startedAt = $lottery->started_at;
        $completedAt = $lottery->completed_at;

        if ($request->status == 'running' && !$startedAt) {
            $startedAt = now();
        }

        if ($request->status == 'completed' && !$completedAt) {
            $completedAt = now();
        }

        $lottery->update([
            'title'            => $request->title,
            'from_date'        => $request->from_date,
            'to_date'          => $request->to_date,
            'draw_date'        => $request->draw_date,
            'required_points'  => $request->required_points,
            'total_winners'    => $request->total_winners,
            'status'           => $request->status,
            'started_at'       => $startedAt,
            'completed_at'     => $completedAt,
        ]);

        return redirect()
            ->route('admin.lotteries.index')
            ->with('success', 'Lottery updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lottery $lottery)
    {
        if ($lottery->status === 'completed') {
            return redirect()->route('admin.lotteries.index')->with('error', 'Completed lotteries cannot be deleted.');
        }
        $lottery->delete();

        return redirect()
            ->route('admin.lotteries.index')
            ->with('success', 'Lottery deleted successfully.');
    }

    // 🎯 DRAW PAGE
    public function draw(Lottery $lottery)
    {
        $lottery->load([
            'giftAssignments.gift',
            'winners' => function ($query) {
                $query->orderBy('position', 'asc')->with(['giftAssign.gift', 'user']);
            },
        ]);

        // The checklist runs heavy aggregates, so only build it before the start
        $checklist = $lottery->status === 'pending' ? $this->drawChecklist($lottery) : [];
        $canStart = collect($checklist)->every(function ($item) {
            return $item['ok'] || !$item['required'];
        });

        $drawnCount = $lottery->current_position ?? 0;
        $nextPosition = $drawnCount < $lottery->total_winners
            ? $lottery->total_winners - $drawnCount
            : null;
        $nextGift = $nextPosition
            ? $lottery->giftAssignments->firstWhere('position', $nextPosition)
            : null;

        // Random eligible names used only for the shuffle animation
        $shuffleNames = $lottery->status === 'running'
            ? User::whereIn('id', $lottery->eligibleUsersQuery())
                ->inRandomOrder()
                ->limit(40)
                ->pluck('name')
            : collect();

        $winnerDetails = $this->winnerDetails($lottery);

        return view('backend.lotteries.draw', compact(
            'lottery',
            'checklist',
            'canStart',
            'nextPosition',
            'nextGift',
            'shuffleNames',
            'winnerDetails'
        ));
    }

    /**
     * Extra info per winner (keyed by position) for the prize board popup.
     */
    private function winnerDetails(Lottery $lottery)
    {
        $userIds = $lottery->winners->pluck('user_id')->unique()->values();

        if ($userIds->isEmpty()) {
            return collect();
        }

        $points = UserPoint::whereIn('user_id', $userIds)
            ->whereBetween('created_at', [
                $lottery->from_date->copy()->startOfDay(),
                $lottery->to_date->copy()->endOfDay(),
            ])
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(point) as total_points, COUNT(*) as entries')
            ->get()
            ->keyBy('user_id');

        $lifetimePoints = UserPoint::whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(point) as total_points')
            ->pluck('total_points', 'user_id');

        // Wins in other lotteries, to spot repeat winners
        $otherWins = LotteryWinner::whereIn('user_id', $userIds)
            ->where('lottery_id', '!=', $lottery->id)
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as total')
            ->pluck('total', 'user_id');

        $technicians = DB::table('technicians')
            ->leftJoin('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id')
            ->leftJoin('geo_district', 'geo_district.id', '=', 'technicians.district_id')
            ->leftJoin('geo_thana', 'geo_thana.id', '=', 'technicians.upazilla_id')
            ->whereIn('technicians.user_id', $userIds)
            ->select(
                'technicians.*',
                'geo_divisions.name as division',
                'geo_district.district',
                'geo_thana.thana'
            )
            ->get()
            ->keyBy('user_id');

        return $lottery->winners->mapWithKeys(function ($winner) use ($points, $technicians, $lifetimePoints, $otherWins) {
            $point = $points->get($winner->user_id);
            $tech = $technicians->get($winner->user_id);
            $user = $winner->user;
            $gift = optional($winner->giftAssign)->gift;
            $birthday = $tech && $tech->birthday ? \Carbon\Carbon::parse($tech->birthday) : null;

            return [$winner->position => [
                'position' => $winner->position,
                'name' => $winner->winner_name,
                'user_id' => $winner->user_id,
                'phone' => optional($user)->phone_number ?: $winner->mobile_no,
                'phone_verified' => (bool) optional($user)->phone_verification_status,
                'account_active' => (bool) optional($user)->status,
                'profile_image' => $user && $user->profile_image
                    ? asset('storage/profile/' . $user->profile_image)
                    : null,
                'member_since' => $user && $user->created_at ? $user->created_at->format('d M Y') : null,

                'father_name' => $tech->father_name ?? null,
                'birthday' => $birthday ? $birthday->format('d M Y') . ' (' . $birthday->age . ' yrs)' : null,
                'blood_group' => $tech->blood_group ?? null,
                'nid_number' => $tech->nid_number ?? null,
                'education' => $tech->education ?? null,
                'occupation' => $tech->occupation ?? null,
                'experience' => $tech && $tech->experience ? $tech->experience . ' years' : null,
                'organization' => $tech->organization ?? null,

                'current_address' => $tech->current_address ?? null,
                'permanent_address' => $tech->permanent_address ?? null,
                'division' => $tech->division ?? null,
                'district' => $tech->district ?? null,
                'thana' => $tech->thana ?? null,

                'payment_number' => $tech->gatway_number ?? null,
                'fo' => $tech && $tech->fo_name ? trim($tech->fo_name . ' (' . $tech->fo_code . ')') : null,
                'tsm' => $tech && $tech->tsm_name ? trim($tech->tsm_name . ' (' . $tech->tsm_code . ')') : null,
                'point' => $tech && $tech->point_name ? trim($tech->point_name . ' (' . $tech->point_code . ')') : null,
                'dealer' => $tech && $tech->dealer_name ? trim($tech->dealer_name . ' (' . $tech->dealer_code . ')') : null,

                'total_points' => $point ? round($point->total_points, 2) : 0,
                'entries' => $point->entries ?? 0,
                'lifetime_points' => round($lifetimePoints->get($winner->user_id, 0), 2),
                'other_wins' => (int) $otherWins->get($winner->user_id, 0),
                'draw_time' => $winner->draw_time
                    ? \Carbon\Carbon::parse($winner->draw_time)->format('d M Y, h:i A')
                    : null,
                'gift_name' => $gift->gift_name ?? 'N/A',
                'gift_image' => $gift && $gift->gift_image
                    ? asset('uploads/lottery_gifts/' . $gift->gift_image)
                    : null,
            ]];
        });
    }

    // 🎯 START LOTTERY
    public function start(Lottery $lottery)
    {
        if ($lottery->status !== 'pending') {
            return back()->with('error', 'Only a pending lottery can be started.');
        }

        $blocking = collect($this->drawChecklist($lottery))->first(function ($item) {
            return $item['required'] && !$item['ok'];
        });

        if ($blocking) {
            return back()->with('error', 'Cannot start: ' . $blocking['label'] . '.');
        }

        $lottery->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        Cache::forget('lottery_upcoming');

        // No flash message: the draw stage itself shows that the lottery is running
        return back();
    }

    // 🎯 DRAW NEXT WINNER
    public function drawNext(Lottery $lottery)
    {
        $result = DB::transaction(function () use ($lottery) {
            // Lock the row so a double click cannot draw the same position twice
            $lottery = Lottery::whereKey($lottery->id)->lockForUpdate()->first();

            if ($lottery->status === 'pending') {
                return ['error' => 'Start the lottery before drawing winners.'];
            }

            $current = $lottery->current_position ?? 0;

            if ($lottery->status === 'completed' || $current >= $lottery->total_winners) {
                return ['error' => 'All winners already drawn!'];
            }

            // Positions are drawn in reverse (last prize first, 1st prize last)
            $nextPosition = $lottery->total_winners - $current;

            $giftAssign = LotteryGiftAssign::where('lottery_id', $lottery->id)
                ->where('position', $nextPosition)
                ->first();

            if (!$giftAssign) {
                return ['error' => 'No gift assigned for position ' . $nextPosition . '!'];
            }

            // Random eligible user who has not won in this lottery yet
            $user = User::whereIn('id', $lottery->eligibleUsersQuery())
                ->whereNotIn('id', function ($query) use ($lottery) {
                    $query->select('user_id')
                        ->from('lottery_winners')
                        ->where('lottery_id', $lottery->id);
                })
                ->inRandomOrder()
                ->first();

            if (!$user) {
                return ['error' => 'No more eligible users found for the draw!'];
            }

            $winner = LotteryWinner::create([
                'lottery_id' => $lottery->id,
                'gift_assign_id' => $giftAssign->id,
                'user_id' => $user->id,
                'position' => $nextPosition,
                'winner_name' => $user->name,
                'mobile_no' => $user->email ?? $user->phone_number ?? 'N/A',
                'draw_time' => now(),
            ]);

            $updateData = ['current_position' => $current + 1];

            if (($current + 1) >= $lottery->total_winners) {
                $updateData['status'] = 'completed';
                $updateData['completed_at'] = now();
            }

            $lottery->update($updateData);

            return ['winner_id' => $winner->id];
        });

        if (isset($result['error'])) {
            return redirect()->route('admin.lotteries.draw', $lottery->id)->with('error', $result['error']);
        }

        Cache::forget('lottery_upcoming');

        return redirect()
            ->route('admin.lotteries.draw', $lottery->id)
            ->with('drawn_winner_id', $result['winner_id']);
    }

    /**
     * Pre-draw checks shown on the draw page. "required" items block the start.
     */
    private function drawChecklist(Lottery $lottery)
    {
        $total = $lottery->total_winners;

        $assignedPositions = $lottery->giftAssignments()
            ->whereBetween('position', [1, $total])
            ->distinct()
            ->count('position');

        $eligibleCount = $lottery->eligibleUsersCount();

        $otherRunning = Lottery::where('status', 'running')
            ->where('id', '!=', $lottery->id)
            ->first();

        $daysLeft = $lottery->daysUntilDraw();

        if (!$lottery->draw_date) {
            $drawDateLabel = 'Draw date is not set';
        } elseif ($daysLeft > 0) {
            $drawDateLabel = 'Draw date is ' . $lottery->draw_date->format('d M Y') . " ({$daysLeft} days left)";
        } else {
            $drawDateLabel = 'Draw date reached (' . $lottery->draw_date->format('d M Y') . ')';
        }

        return [
            [
                'label' => "Gifts assigned to all positions ({$assignedPositions} / {$total})",
                'ok' => $assignedPositions >= $total,
                'required' => true,
                'link' => route('admin.lottery-gift-assign.index', ['lottery' => $lottery->id]),
            ],
            [
                'label' => "Enough eligible technicians ({$eligibleCount} eligible, {$total} needed)",
                'ok' => $eligibleCount >= $total,
                'required' => true,
                'link' => route('admin.lotteries.eligible', $lottery->id),
            ],
            [
                'label' => $otherRunning
                    ? "Another lottery is running ({$otherRunning->title})"
                    : 'No other lottery is running',
                'ok' => !$otherRunning,
                'required' => true,
                'link' => $otherRunning ? route('admin.lotteries.draw', $otherRunning->id) : null,
            ],
            [
                'label' => $drawDateLabel,
                'ok' => $lottery->draw_date && $daysLeft <= 0,
                'required' => false,
                'link' => null,
            ],
        ];
    }
}
