<?php

namespace App\Http\Controllers\Backend;


use App\Repositories\CodeUploader;
use Illuminate\Http\Request;
use App\Models\SSGCodeDetail;
use App\Models\CodeDetail;
use App\Models\RequestCode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class SSGCodeController extends Controller
{

    public function index(Request $request)
    {
        if (!auth()->user()->can('request-code-list')) {
            abort(403);
        }
        $ssgcodes = $this->ssgcodeFilterProcess(new Request($request->all()));
        if($request->status==2){
            $ssgcodes->orderBy('updated_at','DESC');
        }else{
           $ssgcodes->orderBy('id', 'desc');
        } 
        $data['ssgcodes'] = $ssgcodes->paginate(10);

        return view('backend.ssgcode.index')->with($data);
    } 
    public function ssgcodeFilterProcess(Request $request)
    {
        $ssgcodes = SSGCodeDetail::with(['product', 'uploader']);
        
           // ->orderBy('id', 'desc');

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
            if($request->status==2){
                $ssgcodes = $ssgcodes->where('total_used', '>', 1);
            }else{
                $ssgcodes = $ssgcodes->where('status', $request->status);
            } 
        }

        return $ssgcodes;
    }


    public function verifiedProduct(Request $request){
        if (!auth()->user()->can('request-code-list')) {
            abort(403);
        }
        $ssgcodes = SSGCodeDetail::with(['product', 'user.technician'])
            ->whereHas('user') 
            ->where('total_used', 1);  

        if (isset($request->code)) {
            $ssgcodes = $ssgcodes->where('ssg_code_details.code', 'like', '%' . $request->code . '%');
        }

        if (isset($request->serial)) {
            $ssgcodes = $ssgcodes->where('ssg_code_details.serial', 'like', '%' . $request->serial . '%');
        }

        if (isset($request->mobile)) {
            $ssgcodes = $ssgcodes->where('ssg_code_details.mobile', 'like', '%' . $request->mobile . '%');
        } 
        $ssgcodes->orderBy('ssg_code_details.id', 'desc');
        // $ssgcodes->limit(5);
        // $ddd = $ssgcodes->get();
        // dd($ddd->toArray());
            
        $data['ssgcodes'] = $ssgcodes->paginate(10);
        // dd( $data['ssgcodes']);

        return view('backend.ssgcode.verified-product')->with($data);
    }


    /*public function ssg_code_upload(SSGCodeUploadRequest $request)
    {
        $file = request()->file('csv_file');
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('/'), $fileName);

        $data = (new FastExcel)->import($fileName, function ($item) {

            return $item;
        });

        unlink(public_path('/' . $fileName));

        if ($data->count() == 0) {

            return back()->withInput()->with('fail', ['No Data']);
        }

        if (!(isset($data[0]['Serial']) && isset($data[0]['Unique Code']))) {

            return back()->withInput()->with('fail', ['Invalid Data Set']);
        }

        $uploader_id = Auth::user()->id;
        $uploader_ip = \Request::ip();
        foreach ($data as $key => $item) {
            $CodeDetail = CodeDetail::where('serial', $item['Serial'])->first();
            if (!isset($CodeDetail)) {

                return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
            }

            if ($CodeDetail->serial == $item['Serial'] && $CodeDetail->final_unique_code == $item['Unique Code']) {
                $ssg_code_detail = [
                    'product_id' => $CodeDetail->product_id,
                    'serial' => $CodeDetail->serial,
                    'code' => $CodeDetail->final_unique_code,
                    'status' => 0,
                    'uploaded_by' => $uploader_id,
                    'uploaded_ip' => $uploader_ip,
                ];

                try {
                    DB::beginTransaction();
                    SSGCodeDetail::create($ssg_code_detail);
                    DB::commit();

                    return back()->with('success', ['Successfully uploaded']);
                } catch (\Exception $e) {
                    DB::rollback();
                    report($e);

                    return back()->with('success', ['Successfully uploaded. But some data was either duplicated or something went wrong!']);
                }
            }
        }
    }*/
    public function ssg_code_printed($id, Request $request)
    {

        // use App\Models\SSGCodeDetail;
        // use App\Models\CodeDetail;
        $uploaded_by = auth()->user()->id;
        $uploaded_ip = \Request::ip();

        // CodeDetail::where('is_print',0)
        // ->where('request_code_id',$id)
        // ->orderBy('id','ASC')
        // ->limit(10)
        // ->update(['is_print' => 1]);
        // exit();
        $requestcoderesult = RequestCode::find($id);

        if (!$requestcoderesult) {
            //report('no code available for process'); 
            return back()->withInput()->with('fail', ['Request not found']);
        }

        $dataResult = CodeDetail::select(
            'id',
            'product_id',
            'serial',
            'final_unique_code as code',
        )
            ->where('is_print', 0)
            ->where('request_code_id', $id)
            ->orderBy('id', 'ASC')
            ->limit(1200000)
            ->get();

        foreach ($dataResult as $key => $value) {
            $insertData  = array(
                'product_id' => $value->product_id,
                'serial' => $value->serial,
                'code' => $value->code,
                'request_code_id' => $id,
                'status' =>  0,
                'uploaded_by' => $uploaded_by,
                'uploaded_ip' => $uploaded_ip,
            );

            try {
                DB::beginTransaction();
                $ssgCode =  SSGCodeDetail::where('code', $value->code)->exists();

                if (!$ssgCode) {
                    SSGCodeDetail::insert($insertData);
                }
                CodeDetail::where('is_print', 0)
                    ->where('id', $value->id)
                    ->update(['is_print' => 1]);
                DB::commit();
            } catch (\Exception $e) {
                //dd($e);
                DB::rollback();
                continue;
                //return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
            }
        }

        if(count($dataResult) > 0){
            RequestCode::where('id', $id)->update(['print_status' => 2]);
        }
        return redirect()->back()->with('success', ['Successfully uploaded ' . count($dataResult)  . ' rows']);


        //     foreach ($dataResult as $key => $item) {
        //         # code...

        //     try {
        //         DB::beginTransaction(); 
        //         // $codeDetails = CodeDetail::where('serial', $this->inputs['serial'])
        //         // ->where('final_unique_code', $this->inputs['code']) 
        //         // ->first();
        //         if ($codeDetails ) {
        //             $codeDetails->is_print = 1;
        //             $codeDetails->save(); 

        //             CodeDetail::->where('id', 3)
        //             ->update(['title' => "Updated Title"]);

        //             $code = new SSGCodeDetail;
        //             $code->product_id = $codeDetails->product_id ?? null;
        //             $code->serial = $this->inputs['serial'];
        //             $code->code = $this->inputs['code'];
        //             $code->status = 0;
        //             $code->uploaded_by = auth()->user()->id;
        //             $code->uploaded_ip = \Request::ip();
        //             $code->save();

        //         }

        //         DB::commit();

        //     } catch (\Exception $e) {
        //         DB::rollback();
        //         report($e);
        //         $this->has_error = true;
        //         $this->errorMessages[] = $e->getMessage();
        //     }
        // }

        // print_r($id);

        // exit();

    }

    public function ssg_code_upload(Request $request)
    {
        // ini_set('max_execution_time', -1); // unlimited mins
        // ini_set('memory_limit', '500024M'); // 5024 MB
        // dd($request->csv_file->getClientOriginalName());

        if ($request->hasFile('csv_file')) {
            $extension = File::extension($request->csv_file->getClientOriginalName());
            if (!in_array($extension, ["xlsx", "xls", "csv"])) {
                return redirect()->back()->withErrors('The file must be a file of type: csv, xlsx, xls.');
            }
        } else {

            return redirect()->back()->withErrors('No file selected');
        }

        try {
            if ($request->hasFile('csv_file')) {


                $newFileName = Str::random(64) . '.' . $request->file('csv_file')->getClientOriginalExtension();
                $path = $request->file('csv_file')->storeAs('code_upload_dir', $newFileName, 'local');

                $contents = [];

                (new FastExcel)->import(storage_path() . '/app/' . $path, function ($line) use (&$contents, &$monthYear) {

                    $contents[] = [
                        'serial' => $line['Serial'],
                        'code' => $line['Unique Code'],
                    ];
                });

                $failedList = 0;
                //$failedList = [];
                if (count($contents) <= 3000000) {


                    foreach ($contents as $content) {
                        $pu = new CodeUploader($content);
                        if ($pu->hasErrors()) {
                            //$failedList = $contents + $pu->getErrors();
                            $failedList += 1;
                        }

                        // try {
                        //     DB::beginTransaction();

                        //         $codeDetails = CodeDetail::where('serial',$content['serial'])
                        //         ->where('is_print',0)
                        //         ->first(); 
                        //         if ($codeDetails) {
                        //             $codeDetails->is_print = 1;
                        //             $codeDetails->save(); 

                        //             $code = new SSGCodeDetail; 
                        //             $code->product_id = $codeDetails->product_id ?? null;
                        //             $code->serial = $content['serial'];
                        //             $code->code = $content['code'];
                        //             $code->status = 0;
                        //             $code->uploaded_by = auth()->user()->id;
                        //             $code->uploaded_ip = \Request::ip();

                        //             $code->save(); 

                        //             $failedList += 1;
                        //         //     //$failedList = $contents + $pu->getErrors();
                        //         //     $failedList += 1;

                        //         }

                        //         DB::commit();

                        //     } catch (\Exception $e) {
                        //         DB::rollback();
                        //         report($e);
                        //         $this->has_error = true;
                        //         $this->errorMessages[] = $e->getMessage();
                        //     } 

                        //dd($content['serial']);

                        // $pu = new CodeUploader($content);
                        // if ($pu->hasErrors()) {
                        //     //$failedList = $contents + $pu->getErrors();
                        //     $failedList += 1;
                        // }
                    }
                } else {

                    return redirect()->back()->with('fail', ['Max upload 200000 rows']);
                }

                /*$failedListFile = collect($failedList);
                if (count($failedListFile) > 0) {
                    \Log::error($failedListFile);

                    if (isset($newFileName) && file_exists(storage_path('/app/code_upload_dir/' . $newFileName))) {
                        unlink(storage_path('/app/code_upload_dir/' . $newFileName));
                    }
                    return (new FastExcel($failedListFile))->download('code_upload_failed_data' . time() . '.xlsx');
                }*/
            }

            if (isset($newFileName) && file_exists(storage_path('/app/code_upload_dir/' . $newFileName))) {
                unlink(storage_path('/app/code_upload_dir/' . $newFileName));
            }

            return redirect()->back()->with('success', ['Successfully uploaded ' . (count($contents) - $failedList) . ' rows']);
        } catch (\Exception $e) {

            dd($e);
            Log::error($e);

            return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
        }
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
