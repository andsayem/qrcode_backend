<?php

namespace App\Http\Controllers\Backend\Gift;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gift;
use App\Models\GiftPolicy;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    /**
     * Display listing
     */
    public function index()
    {
        $gifts = Gift::with('policy')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('backend.gifts.index', compact('gifts'));
    }

    /**
     * Create form
     */
    public function create()
    {
        $policies = GiftPolicy::orderBy('id', 'desc')->get();

        return view('backend.gifts.create', compact('policies'));
    }

    /**
     * Store gift
     */
    public function store(Request $request)
    {
        $request->validate([
            'policy_id'     => 'required|exists:gift_policies,id',
            'point_slab'    => 'required|integer',
            'gift_name'     => 'required|string|max:255',
            'policy_type'     => 'required|in:instant,year_end',
            'gift_type' => 'required|in:payment_gateway,physical_gift',
            'is_point_cut'  => 'nullable|boolean',
            'max_redeem_limit' => 'nullable|integer|min:0',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|dimensions:width=720,height=400',
        ], [
            'image.dimensions' => 'The gift image must be exactly 720x400 pixels.',
        ]);

        $data = $request->only([
            'policy_id',
            'point_slab',
            'gift_name',
            'policy_type',
            'gift_type',
            'is_point_cut',
            'max_redeem_limit'
        ]);

        // default value fix
        $data['is_point_cut'] = $request->is_point_cut ?? 1;
        $data['max_redeem_limit'] = $request->max_redeem_limit ?? null;

        // image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = 'gifts/' . $filename;

            $img = Image::make($image->getRealPath())->fit(720, 400);
            Storage::disk('public')->put($path, (string) $img->encode());
            $data['image'] = $path;
        }

        Gift::create($data);

        return redirect()
            ->route('admin.gifts.index')
            ->with('success', 'Gift created successfully');
    }

    /**
     * Show single
     */
    public function show($id)
    {
        $gift = Gift::with('policy')->findOrFail($id);

        return view('backend.gifts.show', compact('gift'));
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $gift = Gift::findOrFail($id);
        $policies = GiftPolicy::orderBy('id', 'desc')->get();

        return view('backend.gifts.edit', compact('gift', 'policies'));
    }

    /**
     * Update gift
     */
    public function update(Request $request, $id)
    {
        $gift = Gift::findOrFail($id);

        $request->validate([
            'policy_id'     => 'required|exists:gift_policies,id',
            'point_slab'    => 'required|integer',
            'gift_name'     => 'required|string|max:255',
            'policy_type'     => 'required|in:instant,year_end',
            'gift_type' => 'required|in:payment_gateway,physical_gift',
            'is_point_cut'  => 'nullable|boolean',
            'max_redeem_limit' => 'nullable|integer|min:0',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|dimensions:width=720,height=400',
        ], [
            'image.dimensions' => 'The gift image must be exactly 720x400 pixels.',
        ]);

        $data = $request->only([
            'policy_id',
            'point_slab',
            'gift_name',
            'policy_type',
            'gift_type',
            'is_point_cut',
            'max_redeem_limit'
        ]);

        $data['is_point_cut'] = $request->is_point_cut ?? 1;
        $data['max_redeem_limit'] = $request->max_redeem_limit ?? null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = 'gifts/' . $filename;

            $img = Image::make($image->getRealPath())->fit(720, 400);
            Storage::disk('public')->put($path, (string) $img->encode());
            $data['image'] = $path;
        }

        $gift->update($data);

        return redirect()
            ->route('admin.gifts.index')
            ->with('success', 'Gift updated successfully');
    }

    /**
     * Delete gift
     */
    public function destroy($id)
    {
        $gift = Gift::findOrFail($id);
        $gift->delete();

        return redirect()
            ->route('admin.gifts.index')
            ->with('success', 'Gift deleted successfully');
    }
}
