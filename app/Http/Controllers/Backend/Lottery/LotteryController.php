<?php

namespace App\Http\Controllers\Backend\Lottery;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use Illuminate\Http\Request;
use App\Models\LotteryGiftAssign;
use App\Models\LotteryWinner;
use App\Models\User;

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
            'status'           => 'required|in:pending,running,completed',
        ]);

        Lottery::create([
            'title'            => $request->title,
            'from_date'        => $request->from_date,
            'to_date'          => $request->to_date,
            'required_points'  => $request->required_points,
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
        $lottery->load(['giftAssignments.gift', 'winners']);

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
        return view('backend.lotteries.edit', compact('lottery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lottery $lottery)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'from_date'        => 'required|date',
            'to_date'          => 'required|date|after_or_equal:from_date',
            'required_points'  => 'required|integer|min:0',
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
        $lottery->delete();

        return redirect()
            ->route('admin.lotteries.index')
            ->with('success', 'Lottery deleted successfully.');
    }
    // 🎯 DRAW PAGE
    public function draw(Lottery $lottery)
    {
        $lottery->load('winners.giftAssign.gift');

        return view('backend.lotteries.draw', compact('lottery'));
    }

    // 🎯 DRAW NEXT WINNER
    public function drawNext(Lottery $lottery)
    {
        $current = $lottery->current_position ?? 0;

        // stop condition
        if ($current >= $lottery->total_winners) {
            return back()->with('error', 'All winners already drawn!');
        }

        $nextPosition = $current + 1;

        // get gift for position
        $giftAssign = LotteryGiftAssign::where('lottery_id', $lottery->id)
            ->where('position', $nextPosition)
            ->first();

        if (!$giftAssign) {
            return back()->with('error', 'No gift assigned for this position!');
        }

        // random user (replace with real logic later)
        $user = User::inRandomOrder()->first();

        // save winner
        LotteryWinner::create([
            'lottery_id' => $lottery->id,
            'gift_assign_id' => $giftAssign->id,
            'user_id' => $user->id,
            'position' => $nextPosition,
            'winner_name' => $user->name,
            'mobile_no' => $user->mobile_no ?? 'N/A',
            'draw_time' => now(),
        ]);

        // update position
        $lottery->update([
            'current_position' => $nextPosition
        ]);

        return back()->with('success', '🎉 Winner Drawn Successfully!');
    }
}
