<?php

namespace App\Http\Controllers\Backend\Lottery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LotteryGift;

class LotteryGiftController extends Controller
{
    public function index()
    {
        $gifts = LotteryGift::latest()->paginate(10);

        return view('backend.lotteries.lotteryGifts.index', compact('gifts'));
    }

    public function create()
    {
        return view('backend.lotteries.lotteryGifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'gift_name' => 'required|string|max:255',
            'gift_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageName = null;

        if ($request->hasFile('gift_image')) {
            $image = $request->file('gift_image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/lottery_gifts'), $imageName);
        }

        LotteryGift::create([
            'title' => $request->title,
            'gift_name' => $request->gift_name,
            'gift_image' => $imageName
        ]);

        return redirect()
            ->route('admin.lottery-gifts.index')
            ->with('success', 'Lottery gift created successfully');
    }

    public function edit($id)
    {
        $gift = LotteryGift::findOrFail($id);

        return view('backend.lotteries.lotteryGifts.edit', compact('gift'));
    }

    public function update(Request $request, $id)
    {
        $gift = LotteryGift::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'gift_name' => 'required|string|max:255',
            'gift_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageName = $gift->gift_image;

        if ($request->hasFile('gift_image')) {
            if ($gift->gift_image && file_exists(public_path('uploads/lottery_gifts/' . $gift->gift_image))) {
                unlink(public_path('uploads/lottery_gifts/' . $gift->gift_image));
            }
            $image = $request->file('gift_image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/lottery_gifts'), $imageName);
        }

        $gift->update([
            'title' => $request->title,
            'gift_name' => $request->gift_name,
            'gift_image' => $imageName
        ]);

        return redirect()
            ->route('admin.lottery-gifts.index')
            ->with('success', 'Lottery gift updated successfully');
    }

    public function destroy($id)
    {
        $gift = LotteryGift::findOrFail($id);

        if ($gift->assignments()->exists()) {
            return redirect()
                ->route('admin.lottery-gifts.index')
                ->with('fail', 'Cannot delete gift. It is already assigned to a lottery.');
        }

        if ($gift->gift_image && file_exists(public_path('uploads/lottery_gifts/' . $gift->gift_image))) {
            unlink(public_path('uploads/lottery_gifts/' . $gift->gift_image));
        }

        $gift->delete();

        return redirect()
            ->route('admin.lottery-gifts.index')
            ->with('success', 'Lottery gift deleted successfully');
    }
}
