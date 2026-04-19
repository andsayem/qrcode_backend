<?php

namespace App\Traits;

use App\Models\CodeDetail;
use App\Models\CodeDetailSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait CodeGenerateTrait
{
    private function generateRandomString2($length = 10)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_digit = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < 1; $i++) {
            $random_digit .= $characters[rand(0, $charactersLength - 1)];
        }

        $randomString = str_shuffle($randomString);
        $randomString = (int)filter_var($randomString, FILTER_SANITIZE_NUMBER_INT);
        $randomString = Str::padLeft($randomString, $length, '0');

        $CodeDetail = CodeDetail::where('unique_code', $randomString)->first();
        if ($CodeDetail) {
            return generateRandomString2($length);
        } else {
            return [$randomString, $random_digit];
        }

    }

    private function generateCodeDetailsSerial()
    {
        $year = date('Y');
        $month = date('m');;
        $count = CodeDetailSummary::where('month', date('m'))->where('year', date('Y'))->orderBy('id', 'desc')->first()->total ?? 0;
        $serial = Str::padLeft(($count > 0 ? $count + 1 : 1), 8, '0');
        $finalSerial = $year . $month . $serial;

        return $finalSerial;
    }

    private function codeDetailSummaryUpdate()
    {
        CodeDetailSummary::updateOrCreate(
            [
                'year' => (int)getCurrentYear(),
                'month' => (int)getCurrentMonth(),
            ]
            ,
            [
                'year' => (int)getCurrentYear(),
                'month' => (int)getCurrentMonth(),
                'total' => DB::raw('IFNULL(total,0) + 1')
            ]
        );
    }
 
    private function codeDetailSummaryUpdate_v3($code = 1)
        {
            CodeDetailSummary::updateOrCreate(
                [
                    'year' => (int) getCurrentYear(),
                    'month' => (int) getCurrentMonth(),
                ],
                [
                    'year' => (int) getCurrentYear(),
                    'month' => (int) getCurrentMonth(),
                    'total' => DB::raw("IFNULL(total, 0) + {$code}")
                ]
            );
        }

}
