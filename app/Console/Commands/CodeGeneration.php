<?php

namespace App\Console\Commands;

use App\Jobs\CodeGenCSVZipLockProcess;
use App\Models\CodeDetail;
use App\Models\RequestCode;
use App\Traits\CodeGenerateTrait;
use Illuminate\Console\Command;

class CodeGeneration extends Command
{
    use CodeGenerateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is code generation command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // try {
        //     ini_set('max_execution_time', config('app.memory_limit'));
        //     ini_set('memory_limit', config('app.max_report_execution_time'));

        //     $requestcoderesult = RequestCode::where('status', 2)->orderBy('id', 'asc')->first();
        //     if (!$requestcoderesult) {
        //         //report('no code available for process');

        //         return true;
        //     }

        //     $code_length = $requestcoderesult->code_length;
        //     $req_code = $requestcoderesult->total_no_of_code;
        //     $setting_id = $requestcoderesult->id;
        //     $comple_code = $requestcoderesult->total_complete;
        //     $prefix = $requestcoderesult->product->sku;
        //     $product_id = $requestcoderesult->product->id;


        //     if ($req_code >= $comple_code) {
        //         if ($req_code >= 1000) {
        //             $req_code = 1000;
        //         } else {
        //             $req_code = $req_code - $comple_code;
        //         }
        //     } else {
        //         $req_code = 0;
        //     }
        //     $j = 0;

        //     while ($j < $req_code) {
        //         [$random_code, $random_digit] = $this->generateRandomString2(10);
        //         $CodeDetail = new CodeDetail;
        //         $luhn_checksum = generateChecksum($random_code); // luhn algo checksum find
        //         $unique_code_without_prefix = substr_replace($random_code, $random_digit, rand(0, 9), 0); // put random code(1 char) inside the unique code (only numbers) in random position
        //         $unique_code = $prefix . $unique_code_without_prefix;
        //         $final_unique_code = $unique_code . $luhn_checksum;
        //         $CodeDetail->prefix = $prefix;
        //         $CodeDetail->random_code = $random_code;
        //         $CodeDetail->random_digit = $random_digit;
        //         $CodeDetail->unique_code = $unique_code;
        //         $CodeDetail->checksum_digit = $luhn_checksum;
        //         $CodeDetail->final_unique_code = $final_unique_code;
        //         $CodeDetail->serial = $this->generateCodeDetailsSerial();
        //         $CodeDetail->request_code_id = $setting_id;
        //         $CodeDetail->product_id = $product_id;
        //         $saveresult = $CodeDetail->save();

        //         $this->codeDetailSummaryUpdate();
        //         if ($saveresult) {
        //             $requestcoderesult->total_complete = $requestcoderesult->total_complete + 1;
        //             $requestcoderesult->save();

        //             if ($requestcoderesult->total_no_of_code <= $requestcoderesult->total_complete) {
        //                 $requestcoderesult->status = 3;
        //                 $requestcoderesult->save();
        //                 break;
        //             }
        //         }

        //         $j++;
        //     }
        //     /*CodeGenCSVZipLockProcess::dispatch(
        //         [
        //             'setting_id' => $setting_id,
        //             'final_unique_code' => $final_unique_code
        //         ]
        //     );*/
        //     $this->info('Done successfully');
        // } catch (\Exception $e) {
        //     report($e);
        //     $this->error('Something error found: ');
        // }
    }
}
