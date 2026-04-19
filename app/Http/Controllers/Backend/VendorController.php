<?php

namespace App\Http\Controllers\Backend;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\VendorRequest;

class VendorController extends Controller
{

    public function index(Request $request)
    {
        if (!auth()->user()->can('vendor-list')) {
            abort(403);
        }
        $vendors = $this->vendorFilterProcess(new Request($request->all()));
        $data['vendors'] = $vendors->paginate(10);
        $data['parentvendors'] = Vendor::orderBy('vendor_name', 'asc')->pluck('vendor_name', 'vendor_name')->toArray();

        return view('backend.vendor.index')->with($data);
    }

    public function vendorFilterProcess(Request $request)
    {
        $vendors = Vendor::where('id', '>', 0);
        if (isset($request->vendor_name)) {
            $vendors = $vendors->where('vendor_name', 'like', '%' . $request->vendor_name . '%');
        }

        return $vendors;
    }

    public function create()
    {
        if (!auth()->user()->can('vendor-create')) {
            abort(403);
        }
        return view('backend.vendor.create');
    }


    public function store(VendorRequest $request)
    {
        if (!auth()->user()->can('vendor-create')) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $request['created_by'] = Auth::user()->id;
            Vendor::create($request->all());
            DB::commit();
            return redirect()->route('admin.vendors.index')->with('success', ['Vendor created successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('vendor-edit')) {
            abort(403);
        }

        $data['editModeData'] = Vendor::findOrFail($id);
        return view('backend.vendor.edit')->with($data);
    }


    public function update(VendorRequest $request, $id)
    {
        if (!auth()->user()->can('vendor-edit')) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $request['updated_by'] = Auth::user()->id;
            $vendor = Vendor::findOrFail($id);
            $vendor->update($request->all());

            DB::commit();
            return redirect()->route('admin.vendors.index')->with('success', ['Vendor updated successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }


}
