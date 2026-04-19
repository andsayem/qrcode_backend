<?php

namespace App\Http\Controllers\Backend\Gift;

use App\Http\Controllers\Controller;
use App\Models\GiftPolicy;
use Illuminate\Http\Request;

class GiftPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $giftPolicies = GiftPolicy::withCount('gifts')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('backend.gift-policies.index', compact('giftPolicies'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.gift-policies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $request->validate([
        'program_name' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'image' => 'nullable|image'
    ]);

    $data = $request->only([
        'program_name',
        'start_date',
        'end_date'
    ]);

    // upload image
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('gift_policies', 'public');
    }

    GiftPolicy::create($data);

    return redirect()
        ->route('admin.gift-policies.index')
        ->with('success', 'Gift Policy created successfully');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editModeData = GiftPolicy::findOrFail($id);
        return view('backend.gift-policies.edit', compact('editModeData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $giftPolicy = GiftPolicy::findOrFail($id);
        $giftPolicy->update($request->all());

        return redirect()->route('admin.gift-policies.index')->with('success', 'Gift Policy updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $giftPolicy = GiftPolicy::findOrFail($id);
        $giftPolicy->delete();

        return redirect()->route('admin.gift-policies.index')->with('success', 'Gift Policy deleted successfully.');
    }
}
