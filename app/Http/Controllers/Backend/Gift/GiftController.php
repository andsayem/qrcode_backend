<?php

namespace App\Http\Controllers\Backend\Gift;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gift;
use App\Models\GiftPolicy;

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
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $data = $request->only([
            'policy_id',
            'point_slab',
            'gift_name',
            'policy_type',
            'gift_type',
            'is_point_cut'
        ]);

        // default value fix
        $data['is_point_cut'] = $request->is_point_cut ?? 1;

        // image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gifts', 'public');
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
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $data = $request->only([
            'policy_id',
            'point_slab',
            'gift_name',
            'policy_type',
            'gift_type',
            'is_point_cut'
        ]);

        $data['is_point_cut'] = $request->is_point_cut ?? 1;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gifts', 'public');
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