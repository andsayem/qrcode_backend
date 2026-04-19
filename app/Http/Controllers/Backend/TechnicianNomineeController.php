<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TechnicianNominee;
use App\Models\User;

class TechnicianNomineeController extends Controller
{
    /**
     * Show all nominees for a technician
     */
    public function index($userId)
    {
        $nominees = TechnicianNominee::where('user_id', $userId)->get();

        return response()->json([
            'status' => 1,
            'message' => 'Nominees retrieved successfully',
            'data' => $nominees
        ]);
    }

    /**
     * Store a new nominee
     */
    public function store(Request $request)
    {

       
        $request->validate([ 
            'nominee_name' => 'required|string|max:255',
            'relation' => 'required|string|max:100',
            'nominee_address' => 'required|string',
            'amount_percentage' => 'required|numeric|min:0|max:100',
            'national_id_no' => 'nullable|string|max:20',
        ]);

       $nominee = TechnicianNominee::create([
            'user_id' => auth()->id(),   
            'nominee_name' => $request->nominee_name,
            'relation' => $request->relation,
            'nominee_address' => $request->nominee_address,
            'amount_percentage' => $request->amount_percentage,
            'national_id_no' => $request->national_id_no,
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Nominee added successfully',
            'data' => $nominee
        ]);
    }

    /**
     * Update a nominee
     */
    public function update(Request $request, $id)
    {
        $nominee = TechnicianNominee::findOrFail($id);

        $request->validate([
            'nominee_name' => 'required|string|max:255',
            'relation' => 'required|string|max:100',
            'nominee_address' => 'required|string',
            'amount_percentage' => 'required|numeric|min:0|max:100',
            'national_id_no' => 'nullable|string|max:20',
        ]);

        $nominee->update($request->all());

        return response()->json([
            'status' => 1,
            'message' => 'Nominee updated successfully',
            'data' => $nominee
        ]);
    }

    /**
     * Delete a nominee
     */
    public function destroy($id)
    {
        $nominee = TechnicianNominee::findOrFail($id);
        $nominee->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Nominee deleted successfully',
        ]);
    }
}
