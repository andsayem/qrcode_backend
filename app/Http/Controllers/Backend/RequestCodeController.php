<?php

namespace App\Http\Controllers\Backend;

use App\Utilities\Enum\RequestCodeStatusEnum;
use App\Utilities\Enum\StatusEnum;
use ZipArchive;
use App\Jobs\SendMail;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Settings;
use App\Models\RequestCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RequestCodeStoreRequest;
use App\Http\Requests\RequestCodeApprovalRequest;
use App\Jobs\CodeGenCSVZipLockProcess;
use App\Models\CodeDetail;
use App\Traits\CodeGenerateTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use config;

class RequestCodeController extends Controller
{
    use CodeGenerateTrait;
    public function index(Request $request)
    {
        if (!auth()->user()->can('request-code-list')) {
            abort(403);
        }

        $requestcodes = $this->requestcodeFilterProcess(new Request($request->all()));
        $data['requestcodes'] = $requestcodes->orderBy('id', 'desc')->paginate(25);
        $data['parentproducts'] = Product::select(DB::raw('concat(sku, " (", product_name, ")") as sku'), 'id')
            ->where('status', StatusEnum::Active)
            ->orderBy('sku', 'asc')
            ->pluck('sku', 'id')
            ->toArray();

        $data['parentvendors'] = Vendor::orderBy('vendor_name', 'asc')->pluck('vendor_name', 'id')->toArray();

        return view('backend.requestcode.index')->with($data);
    }

    public function create()
    {
        if (!auth()->user()->can('request-code-create')) {
            abort(403);
        }

        $data['parentproducts'] = Product::select(DB::raw('concat(sku, " (", product_name, ")") as sku'), 'id')
            ->where('status', 1)
            ->orderBy('sku', 'asc')
            ->pluck('sku', 'id')
            ->toArray();
        $data['parentvendors'] = Vendor::orderBy('vendor_name', 'asc')->pluck('vendor_name', 'id')->toArray();

        return view('backend.requestcode.create')->with($data);
    }


    public function store(RequestCodeStoreRequest $request)
    {
        if (!auth()->user()->can('request-code-create')) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $request['created_by'] = Auth::user()->id;
            RequestCode::create($request->all());
            DB::commit();

            SendMail::dispatch(
                [
                    'mailReceiverEmail' => config('app.mail_to_address'),
                    'mailReceiverName' => config('app.mail_to_name'),
                    'mailSenderEmail' => config('app.mail_from_address'),
                    'mailSenderName' => config('app.mail_from_name'),
                    'subject' => 'A new code generation request has been submitted for your approval.',
                    'body' => 'A new code generation request has been submitted for your approval.',
                    'type' => 'notification',
                ]
            );

            return redirect()->route('admin.requestcodes.index')->with('success', ['Code generation request created successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function requestcodes_approval(RequestCodeApprovalRequest $request)
    {

        if (!auth()->user()->can('request-code-edit')) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $data['updated_by'] = Auth::user()->id;
            $data['approved_by'] = Auth::user()->id;
            $data['approved_at'] = getNow();
            $data['status'] = $request->status;
            $data['comments'] = $request->comments;
            RequestCode::where('id', $request->id)->where('status', 1)->update($data);
            DB::commit();
            // if ($request->status == 2) {
            //     $this->code_generator($request->id);
            // }
            return redirect()->route('admin.requestcodes.index')->with('success', ['Code generation request updated to ' . RequestCodeStatusEnum::getKey($request->status)]);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function requestcodes_print(Request $request)
    {

        if (!auth()->user()->can('request-code-edit')) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $data['updated_by'] = Auth::user()->id;
            $data['approved_by'] = Auth::user()->id;
            $data['print_status'] = $request->print_status;
            $data['comments'] = $request->comments;
            RequestCode::where('id', $request->id)->where('status', 3)->update($data);
            DB::commit();
            // if ($request->status == 2) {
            //     $this->code_generator($request->id);
            // }
            return redirect()->route('admin.requestcodes.index')->with('success', ['Status request updated  ']);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }
    public function code_generator_v3()
        {
            ini_set('max_execution_time', config('app.memory_limit'));
            ini_set('memory_limit', config('app.max_report_execution_time'));
            ini_set('max_allowed_packet', config('app.max_allowed_packet'));

            $requestcoderesult = RequestCode::where('status', 2)
                ->orderBy('id', 'asc')
                ->first();

            if (!$requestcoderesult) {
                return back()->withInput()->with('fail', ['Request not found']);
            }

            $serial_number = $this->generateCodeDetailsSerial();
            $data = [];

            $settings = Settings::firstOrFail();

            $code_length = $requestcoderesult->code_length;
            $req_code = $requestcoderesult->total_no_of_code;
            $comple_code = $requestcoderesult->total_complete;
            $req_code = $req_code - $comple_code;
            if ($req_code <= 0) {
                return back()->withInput()->with('fail', ['Request not found']);
            }
           
                if ($settings->code_generator == 1) {
                    $settings->update(['code_generator' => 0]);

                    for ($i = 1; $i <= $req_code; $i++) {
                        $prefix = $requestcoderesult->product->sku;
                        [$random_code, $random_digit] = $this->generateRandomString2(10);
                        $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0);
                        $unique_code = $prefix . $unique_code_without_prefix;
                        $luhn_checksum = generateChecksum($random_code);
                        $final_unique_code = $unique_code . $luhn_checksum;
                        $serial_number = $serial_number + 1;
                        $request_code_id = $requestcoderesult->id;
                        $product_id = $requestcoderesult->product->id;
                
                        // Check if the unique code already exists
                        while (DB::table('code_details')->where('unique_code', $unique_code)->exists()) {
                            [$random_code, $random_digit] = $this->generateRandomString2(10);
                            $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0);
                            $unique_code = $prefix . $unique_code_without_prefix;
                            $final_unique_code = $unique_code . $luhn_checksum;
                        }
                
                        $data[] = [
                            'prefix' => $prefix,
                            'random_code' => $random_code,
                            'random_digit' => $random_digit,
                            'unique_code' => $unique_code,
                            'checksum_digit' => $luhn_checksum,
                            'final_unique_code' => $final_unique_code,
                            'serial' => $serial_number,
                            'request_code_id' => $request_code_id,
                            'product_id' => $product_id,
                        ];
                
                        // Insert batch of 2000 records
                        if ($i % 2000 === 0) {
                            DB::table('code_details')->insert($data);
                            $requestcoderesult->total_complete += count($data); 
                            $requestcoderesult->save(); 
                            $this->codeDetailSummaryUpdate_v3(count($data));

                            $data = []; // Reset data array
                        }
                    }

                    // Insert remaining records
                    if (!empty($data)) {
                        DB::table('code_details')->insert($data);
                        $requestcoderesult->total_complete += count($data); 
                        $requestcoderesult->save(); 
                        $this->codeDetailSummaryUpdate_v3(count($data));

                    } 
                    if ($requestcoderesult->total_no_of_code == $requestcoderesult->total_complete) {
                        $requestcoderesult->status = 3;
                        $requestcoderesult->print_status = 1;
                        $requestcoderesult->save();
                      
                    }
                    $this->file_generator($requestcoderesult->id);
                    $settings->code_generator = 1;
                    $settings->save();
                    
                    return redirect()->route('admin.requestcodes.index')->with('success', ['Code generate done successfully']);
                    
                } else {

                    return back()->withInput()->with('fail', ['Already other request processing. ']);
                }
            
        }


        // test

    public function code_generator_v2()
    {
    if (!auth()->user()->can('request-code-edit')) {
        abort(403);
    }
    try {

      
        ini_set('max_execution_time', config('app.memory_limit'));
        ini_set('memory_limit', config('app.max_report_execution_time'));
        ini_set('max_allowed_packet', config('app.max_allowed_packet'));
 
        $requestcoderesult = RequestCode::where('status', 2)
            ->orderBy('id', 'asc')
            ->first();
        if (!$requestcoderesult) {
            return back()->withInput()->with('fail', ['Request not found']);
        }

        $settings = Settings::firstOrFail();
        if ($settings->code_generator == 1) {
            $prefix = $requestcoderesult->product->sku;
            $product_id = $requestcoderesult->product->id;
            $code_length = $requestcoderesult->code_length;
            $req_code = $requestcoderesult->total_no_of_code;
            $comple_code = $requestcoderesult->total_complete;
            $req_code = $req_code - $comple_code;
            if ($req_code <= 0) {
                return back()->withInput()->with('fail', ['Request not found']);
            }
            $settings->update(['code_generator' => 0]);

            $codesToInsert = [];
            $serial_number = $this->generateCodeDetailsSerial() ;
            for ($i = 0; $i < $req_code; $i++) {
                $serial_number  = $serial_number + 1 ;
                if ($requestcoderesult->total_no_of_code > $requestcoderesult->total_complete) {
                    [$random_code, $random_digit] = $this->generateRandomString2(10);
                    $luhn_checksum = generateChecksum($random_code);
                    $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0);
                    $unique_code = $prefix . $unique_code_without_prefix;
                    $final_unique_code = $unique_code . $luhn_checksum;
                    $codeDetail = array(
                        'prefix' => $prefix,
                        'random_code' => $random_code,
                        'random_digit' => $random_digit,
                        'unique_code' => $unique_code,
                        'checksum_digit' => $luhn_checksum,
                        'final_unique_code' => $final_unique_code,
                        'serial' => $serial_number , //$this->generateCodeDetailsSerial(),
                        'request_code_id' => $requestcoderesult->id,
                        'product_id' => $product_id,
                    );
                    array_push($codesToInsert, $codeDetail);
                    $requestcoderesult->total_complete += 1;
                }
            }

            DB::beginTransaction();
            try {
 
                    $chunkSize = 1000; // number of codes to insert in each chunk
                    $chunks = array_chunk($codesToInsert, $chunkSize);

                    foreach ($chunks as $chunk) {
                        DB::table('code_details')->insert($chunk);
                       // DB::table('codes')->insert($chunk);
                       // CodeDetail::insert($codesToInsert);
                    }

               
                $requestcoderesult->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $settings->code_generator = 1;
                $settings->save();
                return back()->withInput()->with('fail', [$e->getMessage()]);
            }

            $settings->code_generator = 1;
            $settings->save();

            if ($requestcoderesult->total_no_of_code == $requestcoderesult->total_complete) {
                $requestcoderesult->status = 3;
                $requestcoderesult->print_status = 1;
                $requestcoderesult->save();
                $this->file_generator($requestcoderesult->id);
                $this->codeDetailSummaryUpdate();
            }

            return redirect()->route('admin.requestcodes.index')->with('success', ['Code generate done successfully']);
    } else {

        return back()->withInput()->with('fail', ['Already other request processing. ']);
    }
    } catch (\Exception $e) { 
         $this->error('Something error found: ');
    }
    }


    public function code_generator()
    {
        if (!auth()->user()->can('request-code-edit')) {
            abort(403);
        }
        try {
            ini_set('max_execution_time', config('app.memory_limit'));
            ini_set('memory_limit', config('app.max_report_execution_time'));

        $requestcoderesult = RequestCode::where('status', 2)
            ->orderBy('id', 'asc')
            ->first();
            if (!$requestcoderesult) {
                //report('no code available for process'); 
                return back()->withInput()->with('fail', ['Request not found']);
            }
    
        $settings = Settings::firstOrFail();
        if ($settings->code_generator == 1) {
        $prefix = $requestcoderesult->product->sku;
        $product_id = $requestcoderesult->product->id;
        $code_length = $requestcoderesult->code_length;
        $req_code = $requestcoderesult->total_no_of_code;
        $comple_code = $requestcoderesult->total_complete;
        $req_code = $req_code - $comple_code;
    
        if ($req_code <= 0) {
            return back()->withInput()->with('fail', ['Request not found']);
        }
     
        $settings->update(['code_generator' => 0]);

        $serial_number = $this->generateCodeDetailsSerial() ;
    
        for ($i = 0; $i < $req_code; $i++) {
            $serial_number  = $serial_number + 1 ;
            if ($requestcoderesult->total_no_of_code > $requestcoderesult->total_complete) {
                [$random_code, $random_digit] = $this->generateRandomString2(10);
                $luhn_checksum = generateChecksum($random_code);
                $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0);
                $unique_code = $prefix . $unique_code_without_prefix;
                $final_unique_code = $unique_code . $luhn_checksum;
        
                $codeDetail = array(
                    'prefix' => $prefix,
                    'random_code' => $random_code,
                    'random_digit' => $random_digit,
                    'unique_code' => $unique_code,
                    'checksum_digit' => $luhn_checksum,
                    'final_unique_code' => $final_unique_code,
                    'serial' => $serial_number , // $this->generateCodeDetailsSerial(),
                    'request_code_id' => $requestcoderesult->id,
                    'product_id' => $product_id,
                ) ;


                try {
                    DB::beginTransaction(); 
                        $CodeDetail = new CodeDetail($codeDetail);
                        $CodeDetail->save();
            
                        $requestcoderesult->total_complete += 1; 
                        $requestcoderesult->save(); 
                    
                    DB::commit(); 
                } catch (\Exception $e) {
                    DB::rollback();
                    $settings->code_generator =  1;
                    $settings->save();
                    return back()->withInput()->with('fail', [$e->getMessage()]);
                }
            }
        }
     // dd($codeDetails );
     
        $settings->code_generator =  1;
        $settings->save();
        if ($requestcoderesult->total_no_of_code == $requestcoderesult->total_complete) {
            $requestcoderesult->status = 3;
            $requestcoderesult->print_status = 1;
            $requestcoderesult->save();
            $this->file_generator($requestcoderesult->id);
            $this->codeDetailSummaryUpdate();
        }
        return redirect()->route('admin.requestcodes.index')->with('success', ['Code generate done successfully']);

    } else {

        return back()->withInput()->with('fail', ['Already other request processing. ']);
    }
    } catch (\Exception $e) { 
         $this->error('Something error found: ');
    }
    }
    public function code_generator_backup()
    {
        if (!auth()->user()->can('request-code-edit')) {
            abort(403);
        }
        try {
            ini_set('max_execution_time', config('app.memory_limit'));
            ini_set('memory_limit', config('app.max_report_execution_time'));

        $requestcoderesult = RequestCode::where('status', 2)
            ->orderBy('id', 'asc')
            ->first();
            if (!$requestcoderesult) {
                //report('no code available for process'); 
                return back()->withInput()->with('fail', ['Request not found']);
            }
    
        $settings = Settings::firstOrFail();
        if ($settings->code_generator == 1) {
        $prefix = $requestcoderesult->product->sku;
        $product_id = $requestcoderesult->product->id;
        $code_length = $requestcoderesult->code_length;
        $req_code = $requestcoderesult->total_no_of_code;
        $comple_code = $requestcoderesult->total_complete;
        $req_code = $req_code - $comple_code;
    
        if ($req_code <= 0) {
            return back()->withInput()->with('fail', ['Request not found']);
        }
     
        $settings->update(['code_generator' => 0]);
    
        for ($i = 0; $i < $req_code; $i++) {
            if ($requestcoderesult->total_no_of_code > $requestcoderesult->total_complete) {
                [$random_code, $random_digit] = $this->generateRandomString2(10);
                $luhn_checksum = generateChecksum($random_code);
                $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0);
                $unique_code = $prefix . $unique_code_without_prefix;
                $final_unique_code = $unique_code . $luhn_checksum;
        
                $codeDetail = array(
                    'prefix' => $prefix,
                    'random_code' => $random_code,
                    'random_digit' => $random_digit,
                    'unique_code' => $unique_code,
                    'checksum_digit' => $luhn_checksum,
                    'final_unique_code' => $final_unique_code,
                    'serial' => $this->generateCodeDetailsSerial(),
                    'request_code_id' => $requestcoderesult->id,
                    'product_id' => $product_id,
                ) ;


                try {
                    DB::beginTransaction(); 
                        $CodeDetail = new CodeDetail($codeDetail);
                        $CodeDetail->save();
            
                        $requestcoderesult->total_complete += 1; 
                        $requestcoderesult->save(); 
                    
                    DB::commit(); 
                } catch (\Exception $e) {
                    DB::rollback();
                    $settings->code_generator =  1;
                    $settings->save();
                    return back()->withInput()->with('fail', [$e->getMessage()]);
                }
            }
        }
     // dd($codeDetails );
     
        $settings->code_generator =  1;
        $settings->save();
        if ($requestcoderesult->total_no_of_code == $requestcoderesult->total_complete) {
            $requestcoderesult->status = 3;
            $requestcoderesult->print_status = 1;
            $requestcoderesult->save();
            $this->file_generator($requestcoderesult->id);
            $this->codeDetailSummaryUpdate();
        }
        return redirect()->route('admin.requestcodes.index')->with('success', ['Code generate done successfully']);

    } else {

        return back()->withInput()->with('fail', ['Already other request processing. ']);
    }
    } catch (\Exception $e) { 
         $this->error('Something error found: ');
    }
    }

    public function code_generator_old()
    {
        if (!auth()->user()->can('request-code-edit')) {
            abort(403);
        }
        try {
            ini_set('max_execution_time', config('app.memory_limit'));
            ini_set('memory_limit', config('app.max_report_execution_time'));

            $requestcoderesult = RequestCode::where('status', 2)
                ->orderBy('id', 'asc')
                ->first();
            if (!$requestcoderesult) {
                //report('no code available for process'); 
                return back()->withInput()->with('fail', ['Request not found']);
            }
            $settings =  Settings::first();

            if ($settings->code_generator == 1) {
                $code_length = $requestcoderesult->code_length;
                $req_code = $requestcoderesult->total_no_of_code;
                $setting_id = $requestcoderesult->id;
                $comple_code = $requestcoderesult->total_complete;
                $prefix = $requestcoderesult->product->sku;
                $product_id = $requestcoderesult->product->id; 
                $req_code = $req_code - $comple_code;

                // if ($req_code >= $comple_code) {
                //     if ($req_code >= 10) {
                //         $req_code = 10;
                //     } else {
                //         $req_code = $req_code - $comple_code;
                //     }
                // } else {
                //     $req_code = 0;
                // }
                $settings->code_generator =  0;
                $settings->save();
                $j = 0;
                if ($req_code > 0) {
                    while ($j < $req_code) { 
                        if($requestcoderesult->total_complete < $req_code){

                      
                        
                        [$random_code, $random_digit] = $this->generateRandomString2(10);
                        $CodeDetail = new CodeDetail;
                        $luhn_checksum = generateChecksum($random_code); // luhn algo checksum find
                        $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0); // put random code(1 char) inside the unique code (only numbers) in random position
                        $unique_code = $prefix . $unique_code_without_prefix;
                        $final_unique_code = $unique_code . $luhn_checksum;
                        $CodeDetail->prefix = $prefix;
                        $CodeDetail->random_code = $random_code;
                        $CodeDetail->random_digit = $random_digit;
                        $CodeDetail->unique_code = $unique_code;
                        $CodeDetail->checksum_digit = $luhn_checksum;
                        $CodeDetail->final_unique_code = $final_unique_code;
                        $CodeDetail->serial = $this->generateCodeDetailsSerial();
                        $CodeDetail->request_code_id = $setting_id;
                        $CodeDetail->product_id = $product_id;

                         
                            DB::beginTransaction();
                            $saveresult = $CodeDetail->save(); 
                            $this->codeDetailSummaryUpdate(); 

                            $requestcoderesult->total_complete = $requestcoderesult->total_complete + 1;
                            $requestcoderesult->save();

                            $requestcoderesult->status = 3;
                            $requestcoderesult->print_status = 1;
                            $requestcoderesult->save();
                            $this->file_generator($requestcoderesult->id); 
                            DB::commit();
                            break;
 



                        // $saveresult = $CodeDetail->save(); 
                        // $this->codeDetailSummaryUpdate(); 

                        // if ($saveresult) {
                        //     $requestcoderesult->total_complete = $requestcoderesult->total_complete + 1;
                        //     $requestcoderesult->save();

                        //     if ($requestcoderesult->total_no_of_code <= $requestcoderesult->total_complete) {
                               
                        //         try {
                        //             DB::beginTransaction();
                        //             $requestcoderesult->status = 3;
                        //             $requestcoderesult->print_status = 1;
                        //             $requestcoderesult->save();
                        //             $this->file_generator($requestcoderesult->id);
                        //             $settings->code_generator =  1;
                        //             $settings->save();
                        //             DB::commit();

                        //             break;
                        //         }catch (\Exception $e) { 
                        //             DB::rollback(); 
                        //             $settings->code_generator = 1;
                        //             $settings->save();
                        //             report($e);
                        //             return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
                        //         }
                        //     }
                        // } 
                        $j++; 
                        // }else{
                        //     return redirect()->route('admin.requestcodes.index')->with('success', ['Code generate done successfully']);
                        // }
                        }

                    }
                    $settings->code_generator =  1;
                    $settings->save();
                    return redirect()->route('admin.requestcodes.index')->with('success', ['Code generate done successfully']);
                }

            } else {

                return back()->withInput()->with('fail', ['Already other request processing. ']);
            }
            
        } catch (\Exception $e) {
            dd($e);
            // $this->error('Something error found: ');
        }
    }
    public function code_generator_delete($id)
    {
        CodeDetail::where('request_code_id', $id)->delete();
        $requestcoderesult = RequestCode::where('id', $id)
            ->first();
        $requestcoderesult->total_complete = 0;
        $requestcoderesult->status = 1;
        $requestcoderesult->file_path = null;
        $requestcoderesult->is_file_generate = 0;
        $requestcoderesult->save();
        return redirect()->route('admin.requestcodes.index')->with('success', ['Code Deleted']);
    }
    function clean($string)
    {
        $string = str_replace(' ', ' ', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);

        return preg_replace('/-+/', '-', $string);
    }
    public function file_generator($id)
    {
        try {
            ini_set('max_execution_time', config('app.memory_limit'));
            ini_set('memory_limit', config('app.max_report_execution_time'));

            $requestCode = RequestCode::where('status', 3)
                ->where('id', $id)
                ->where('is_file_generate', 0)
                ->orderBy('id', 'desc')->first();
            if (!$requestCode) {
                return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
            }
            $setting_id = $requestCode->id;

            $product = Product::find($requestCode->product_id);
            $final_unique_code = $this->clean($product->product_name) . '_QR' . '-' . date('Ymd') . '-' . time();
            $data = CodeDetail::where('request_code_id', $setting_id);


            if (!$data->count()) {

                return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
            }

            // =========================================================

            $zipname = $final_unique_code . '.zip';
            $zip = new ZipArchive;
            $zip->open(public_path($zipname), ZipArchive::CREATE);
            $fd = fopen('php://temp/maxmemory:1048576', 'w');
            if (false === $fd) {
                die('Failed to create temporary file');
            }

            $headers = [
                0 => "Serial",
                1 => "Unique Code",
                2 => "Url"
            ];

            // write the data to csv
            fputcsv($fd, $headers);
            $datas = $data->cursor();
            foreach ($datas as $key => $item) {
                $array = [];
                $array = [
                    'Serial' => $item->serial,
                    'Unique Code' => $item->final_unique_code,
                    'Url' => 'https://qrc.ssgbd.com/verify/' . $item->final_unique_code,
                ];
                fputcsv($fd, $array);
            };

            rewind($fd);
            $zip->addFromString('ssg-' . time() . '-code-file.csv', stream_get_contents($fd));
            $password = randomPassword();
            $zip->setEncryptionName('ssg-' . time() . '-code-file.csv', ZipArchive::EM_AES_256, $password);
            fclose($fd);
            $zip->close();

            $base_path = '/uploads/code_generations';
            $fullPublicPath = public_path() . $base_path;
            $filePath_without_baseurl = $base_path . '/' . $zipname;
            $filePath = $fullPublicPath . '/' . $zipname;
            File::ensureDirectoryExists($fullPublicPath);
            File::move(public_path($zipname), $filePath);

            // Db update
            $RequestCode = RequestCode::find($setting_id);
            $RequestCode->file_path = $filePath_without_baseurl;
            $RequestCode->file_password = $password;
            $RequestCode->status = 3;
            $RequestCode->is_file_generate = 1;
            $RequestCode->save();

            SendMail::dispatch(
                [
                    'mailReceiverEmail' => config('app.mail_to_address'),
                    'mailReceiverName' => config('app.mail_to_name'),
                    'mailSenderEmail' => config('app.mail_from_address'),
                    'mailSenderName' => config('app.mail_from_name'),
                    'subject' => 'A new code generation successfully completed.',
                    'body' => 'A new code generation successfully completed.' . '<br><br>' .
                        '<ul>' .
                        '<li>File Link: ' . asset($filePath_without_baseurl) . '</li>' .
                        '<li>File Password: ' . $password . '</li>' .
                        '</ul>',
                    'type' => 'notification',
                ]
            );
            return redirect()->route('admin.requestcodes.index')->with('success', ['File generate done successfully']);
            // $this->info('File generate done successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }



    public function requestcodes_vendor(RequestCodeApprovalRequest $request)
    {


        if (!auth()->user()->can('request-code-edit')) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $data['updated_by'] = Auth::user()->id;
            $data['approved_by'] = Auth::user()->id;
            $data['vendor_id'] = $request->status;
            // $data['comments'] = $request->comments;
            RequestCode::where('id', $request->id)->update($data);
            DB::commit();

            return redirect()->route('admin.requestcodes.index')->with('success', ['Successfully data update']);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function download_codes($id)
    {
        $requestcode = RequestCode::findOrFail($id);
        $filePath = $requestcode->file_path;
        $filename = explode('/', $filePath);
        $filename = end($filename);
        $filePath_pp = public_path($filePath);

        header('Content-disposition: attachment; filename=' . $filename);
        header('Content-type: application/zip');
        /*ob_end_clean();*/

        readfile($filePath_pp);

        $requestcode->totalDownload = $requestcode->totalDownload + 1;
        $requestcode->save();
    }


    private function requestcodeFilterProcess(Request $request)
    {
        $requestcodes = RequestCode::with(['vendor', 'product', 'creator']);
        // $requestcodes->withCount(['printNumber AS print_num' => function ($query) {
        //     $query->select(\DB::raw("SUM(is_print) as is_print_num"))
        //         ->where('is_print', 1);
        // }]);
        if (isset($request->product_id)) {
            $requestcodes = $requestcodes->where('product_id', $request->product_id);
        }

        if (isset($request->vendor_id)) {
            $requestcodes = $requestcodes->where('vendor_id', $request->vendor_id);
        }

        if (isset($request->status)) {
            $requestcodes = $requestcodes->where('status', $request->status);
        }

        return $requestcodes;
    }


    /*public function code_generation()
    {
        ini_set('max_execution_time', config('app.memory_limit'));
        ini_set('memory_limit', config('app.max_report_execution_time'));

        $requestcoderesult = RequestCode::where('status', 1)->orderBy('id', 'asc')->first();

        if (!$requestcoderesult) {
            report('no code available for process');

            return True;
        }

        $code_length = $requestcoderesult->code_length;
        $req_code = $requestcoderesult->total_no_of_code;
        $setting_id = $requestcoderesult->id;
        $comple_code = $requestcoderesult->total_complete;
        $prefix = $requestcoderesult->product->sku;
        $product_id = $requestcoderesult->product->id;


        if ($req_code >= $comple_code) {
            if ($req_code >= 80000) {
                $req_code = 80000;
            } else {
                $req_code = $req_code - $comple_code;
            }
        } else {
            $req_code = 0;
        }
        $j = 0;

        while ($j < $req_code) {
            [$random_code, $random_digit] = $this->generateRandomString2(10);
            $CodeDetail = new CodeDetail;
            $luhn_checksum = generateChecksum($random_code); // luhn algo checksum find
            $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0); // put random code(1 char) inside the unique code (only numbers) in random position
            $unique_code = $prefix . $unique_code_without_prefix;
            $final_unique_code = $unique_code . $luhn_checksum;
            $CodeDetail->prefix = $prefix;
            $CodeDetail->random_code = $random_code;
            $CodeDetail->random_digit = $random_digit;
            $CodeDetail->unique_code = $unique_code;
            $CodeDetail->checksum_digit = $luhn_checksum;
            $CodeDetail->final_unique_code = $final_unique_code;
            $CodeDetail->serial = $this->generateCodeDetailsSerial();
            $CodeDetail->request_code_id = $setting_id;
            $CodeDetail->product_id = $product_id;
            $saveresult = $CodeDetail->save();

            $this->codeDetailSummaryUpdate();
            if ($saveresult) {
                $requestcoderesult->total_complete = $requestcoderesult->total_complete + 1;
                $requestcoderesult->save();

                if ($req_code <= $requestcoderesult->total_complete) {
                    $requestcoderesult->status = 2;
                    $requestcoderesult->save();
                    break;
                }
            }

            $j++;
        }
        CodeGenCSVZipLockProcess::dispatch(
            [
                'setting_id' => $setting_id,
                'final_unique_code' => $final_unique_code
            ]
        );
    }


    public function code_gen_csv_zip_lock_process($data)
    {
        ini_set('max_execution_time', config('app.memory_limit'));
        ini_set('memory_limit', config('app.max_report_execution_time'));

        $setting_id = $data['setting_id'];
        $final_unique_code = $data['final_unique_code'];


        $data = CodeDetail::where('request_code_id', $setting_id);
        $data = $data->get()->map(function ($item, $key) {
            return [
                'Serial' => $item->serial,
                'Unique Code' => $item->final_unique_code,
                'Url' => url('/') . '/checkCodeURL/' . $item->final_unique_code,
            ];
        });

        if (!$data->count()) {
            return redirect()->back()->with('fail', ['No Data Found For Download']);
        }

        // =========================================================
        // some data to be used in the csv files
        $headers = array_keys($data[0]);

        $records = $data->toArray();


        // create your zip file
        $zipname = $final_unique_code . '.zip';
        $zip = new ZipArchive;
        $zip->open(public_path($zipname), ZipArchive::CREATE);


        // loop to create 3 csv files
        // create a temporary file
        $fd = fopen('php://temp/maxmemory:1048576', 'w');
        if (false === $fd) {
            die('Failed to create temporary file');
        }

        // write the data to csv
        fputcsv($fd, $headers);
        foreach ($records as $record) {
            fputcsv($fd, $record);
        }

        // return to the start of the stream
        rewind($fd);

        // add the in-memory file to the archive, giving a name
        $zip->addFromString('file.csv', stream_get_contents($fd));
        $password = randomPassword();
        $zip->setEncryptionName('file.csv', ZipArchive::EM_AES_256, $password);
        //close the file
        fclose($fd);

        // close the archive
        $zip->close();

        $base_path = '/uploads/code_generations';
        $fullPublicPath = public_path() . $base_path;
        $filePath_without_baseurl = $base_path . '/' . $zipname;
        $filePath = $fullPublicPath . '/' . $zipname;

        File::ensureDirectoryExists($fullPublicPath);

        File::move(public_path($zipname), $filePath);

        // Db update
        $RequestCode = RequestCode::find($setting_id);
        $RequestCode->file_path = $filePath_without_baseurl;
        $RequestCode->file_password = $password;
        $RequestCode->save();

        SendMail::dispatch(
            [
                'mailReceiverEmail' => config('app.mail_to_address'),
                'mailReceiverName' => config('app.mail_to_name'),
                'mailSenderEmail' => config('app.mail_from_address'),
                'mailSenderName' => config('app.mail_from_name'),
                'subject' => 'A new code generation successfully completed.',
                'body' => 'A new code generation successfully completed.' . '<br><br>' .
                    '<ul>' .
                    '<li>File Link: ' . asset($filePath_without_baseurl) . '</li>' .
                    '<li>File Password: ' . $password . '</li>' .
                    '</ul>',
                'type' => 'notification',
            ]
        );
    }
    */
}
