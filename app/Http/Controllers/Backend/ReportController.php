<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\CodeUploader;
use App\Models\Report;
use App\Models\Product;
use App\Models\UserRedeemRequest;
use App\Models\RequestCode;
use App\Utilities\Enum\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        if (!auth()->user()->can('request-code-list')) {
            abort(403);
        }
        $ssgcodes = $this->ssgcodeFilterProcess(new Request($request->all()));
        $ssgcodes =  $ssgcodes->orderBy('id', 'desc')
        ->paginate(50);
        $data['ssgcodes'] = $ssgcodes ;
           
        $result  = Product::select(DB::raw('concat(sku, " (", product_name, ")") as sku'), 'id')
            ->where('status', StatusEnum::Active)
            ->orderBy('sku', 'asc')
            ->pluck('sku', 'id')
            ->toArray(); 
        $data['parentproducts'] = $result;
        
         //->with('technicians')

        return view('backend.reports.index')->with($data);
    }

    public function ssgcodeFilterProcess(Request $request)
    {
        $ssgcodes = Report::with(['product', 'uploader'])->with('technicians')->orderBy('id', 'desc');

        if (isset($request->code)) {
            $ssgcodes = $ssgcodes->where('code', 'like', '%' . $request->code . '%');
        }

       
        if (isset($request->serial)) {
            $ssgcodes = $ssgcodes->where('serial', 'like', '%' . $request->serial . '%');
        }

        if (isset($request->mobile)) {
            $ssgcodes = $ssgcodes->where('mobile', 'like', '%' . $request->mobile . '%');
        }

        if (isset($request->status)) {
            $ssgcodes = $ssgcodes->where('status', $request->status);
        }

        if (isset($request->code_used_time)) {
            $ssgcodes = $ssgcodes->where('code_used_time', $request->code_used_time);
        }

        if (isset($request->name)) {
            $ssgcodes = $ssgcodes->where('name', 'like', '%' . $request->name . '%');
        }
        if (isset($request->from_date)) {
            $ssgcodes->whereDate('created_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if (isset($request->to_date)) {
            $ssgcodes->whereDate('updated_at', '<=', dateConvertFormtoDB($request->to_date));
        }

        if (isset($request->product_id)) {
            $ssgcodes->where('product_id', $request->product_id);
        }

        return $ssgcodes;
    }

    public function codeSampleFile()
    {
        $list = collect([
            [
                'Serial' => '20211200000008',
                'Unique Code' => 'MK6385V3540985',
            ],
            [
                'Serial' => '20211200000009',
                'Unique Code' => 'MK6385V3540982',
            ]
        ]);

        return (new FastExcel($list))->download('code-upload-sample-' . time() . '.xlsx');
    }

}
