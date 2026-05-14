<?php

namespace App\Http\Controllers\Backend\Lottery;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use Illuminate\Http\Request;
use App\Models\LotteryGiftAssign;
use App\Models\LotteryWinner;
use App\Models\User;
use App\Models\UserPoint;

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
            'required_points'  => 'required|integer|min:0',
            'total_winners'    => 'required|integer|min:1',
            'status'           => 'required|in:pending,running,completed',
        ]);

        Lottery::create([
            'title'            => $request->title,
            'from_date'        => $request->from_date,
            'to_date'          => $request->to_date,
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

    public function drawPage(Lottery $lottery)
    {
        $lottery->load(['giftAssignments.gift', 'winners' => function ($query) {
            $query->orderBy('position', 'asc')->with('giftAssign.gift');
        }]);

        return view('backend.lotteries.draw', compact('lottery'));
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
        $lottery->load(['winners' => function ($query) {
            $query->orderBy('position', 'asc')->with('giftAssign.gift');
        }]);

        return view('backend.lotteries.draw', compact('lottery'));
    }

    // 🎯 DRAW NEXT WINNER
    public function drawNext(Lottery $lottery)
    {
        // Prevent drawing if another lottery is currently running
        $runningLotteryExists = Lottery::where('status', 'running')
                                       ->where('id', '!=', $lottery->id)
                                       ->exists();

        if ($runningLotteryExists) {
            return back()->with('error', 'Another lottery is currently running. Please wait for it to complete before drawing from this lottery.');
        }


        $current = $lottery->current_position ?? 0;

        // stop condition
        if ($current >= $lottery->total_winners) {
            return back()->with('error', 'All winners already drawn!');
        }

        // Calculate position in reverse (e.g., if total is 15 and 0 drawn, next is 15)
        $nextPosition = $lottery->total_winners - $current;

        // get gift for position
        $giftAssign = LotteryGiftAssign::where('lottery_id', $lottery->id)
            ->where('position', $nextPosition)
            ->first();

        if (!$giftAssign) {
            return back()->with('error', 'No gift assigned for this position!');
        }

        // 1. Find eligible user IDs who earned required points within the lottery period
        $eligibleUserIds = UserPoint::select('user_id')
            ->whereBetween('created_at', [
                $lottery->from_date->startOfDay(),
                $lottery->to_date->endOfDay()
            ])
            ->groupBy('user_id')
            ->havingRaw('SUM(point) >= ?', [$lottery->required_points])
            ->pluck('user_id');

        // 2. Pick a random user from the eligible pool who hasn't won in this lottery yet
        $user = User::whereNotIn('id', function ($query) use ($lottery) {
                $query->select('user_id')
                    ->from('lottery_winners')
                    ->where('lottery_id', $lottery->id);
            })
            ->whereIn('id', $eligibleUserIds)
            ->inRandomOrder()
            ->first();

        if (!$user) {
            return back()->with('error', 'No more eligible users found for the draw!');
        }

        // save winner
        LotteryWinner::create([
            'lottery_id' => $lottery->id,
            'gift_assign_id' => $giftAssign->id,
            'user_id' => $user->id,
            'position' => $nextPosition,
            'winner_name' => $user->name,
            'mobile_no' => $user->email ?? $user->phone_number ?? 'N/A',
            'draw_time' => now(),
        ]);

        // Prepare update data
        $updateData = [
            'current_position' => $current + 1 // Increment the count of winners drawn
        ];

        // Auto-update status based on progress
        if ($lottery->status === 'pending') {
            $updateData['status'] = 'running';
            $updateData['started_at'] = now();
        }

        if (($current + 1) >= $lottery->total_winners) {
            $updateData['status'] = 'completed';
            $updateData['completed_at'] = now();
        }

        $lottery->update($updateData);

        return back()->with('success', '🎉 Winner Drawn Successfully!');
    }
}
