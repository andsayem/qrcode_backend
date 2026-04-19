<?php

use Carbon\Carbon;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Http;
function dateConvertFormtoDB($date)
{
    if (!empty($date)) {
        return Carbon::parse($date)->format('Y-m-d');
    }
}

function dateConvertDBtoForm($date)
{
    if (!empty($date)) {
        return \Carbon\Carbon::parse($date)->format('d-m-Y');
    }
}

function dateConvertDBtoFormDM($date)
{
    if (!empty($date)) {
        return \Carbon\Carbon::parse($date)->format('d M');
    }
}

function dateConvertDBtoFormDFCY($date)
{
    if (!empty($date)) {
        return \Carbon\Carbon::parse($date)->format('d F, Y');
    }
}

function dateTimeConvertDBtoForm($date)
{
    if (!empty($date)) {
        return \Carbon\Carbon::parse($date)->format('d-m-Y H:i:s');
    }
}

if (!function_exists('dbDate')) {
    function dbDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
}


if (!function_exists('dbDateTime')) {
    function dbDateTime($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }
}

if (!function_exists('removeArrayValues')) {
    function removeArrayValues($originalArray, $removeValueArray)
    {
        return array_diff($originalArray, $removeValueArray);
    }
}

if (!function_exists('calculateAchievements')) {
    function calculateAchievements($target, $sales)
    {
        if ($target <= 0) {
            return $achievement = 0;
        }
        $achievement = ($sales / $target) * 100;

        return number_format($achievement, 2, '.', '');
    }
}


if (!function_exists('writeToLog')) {
    function writeToLog($logMessage, $logType = 'error')
    {
        try {
            $allLogTypes = ['alert', 'critical', 'debug', 'emergency', 'error', 'info'];

            $logType = strtolower($logType);

            if (in_array($logType, $allLogTypes)) {
                \Log::$logType($logMessage);
            }
        } catch (\Exception $exception) {
            //
        }
    }
}

if (!function_exists('exportErrorsFile')) {
    function exportErrorsFile($errors, $userId)
    {
        $filePath = errorListFilePath($userId);

        if (isset($filePath) && file_exists(storage_path($filePath))) {
            unlink(storage_path($filePath));
        }

        $errorCollection = collect($errors);
        (new FastExcel($errorCollection))->export(storage_path($filePath));

    }
}

if (!function_exists('formatCommonErrorLogMessage')) {
    function formatCommonErrorLogMessage($exception)
    {
        $logMessage = 'Error occured on File: ' . $exception->getFile() . ' on Line: ' . $exception->getLine() . ' due to: ' . $exception->getMessage();

        return $logMessage;
    }
}


if (!function_exists('csvExport')) {
    function csvExport($data, $filename)
    {
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        $fp = fopen('php://output', 'w');

        function prettyString($string)
        {
            $val = str_replace("_", " ", $string);
            return ucwords($val);
        }

        $header = array_keys($data[0]);
        $header = array_map('prettyString', $header);

        fputcsv($fp, $header);
        foreach ($data as $row) {
            //$fp = fopen('php://output', 'a');
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }
}


function msisdn($mobile_no, $with880 = true)
{
    if ($with880) {
        return '880' . substr($mobile_no, -10);
    } else {
        return substr($mobile_no, -11);
    }
}

if (!function_exists('calculateAchievements')) {
    function calculateAchievements($target, $sales)
    {
        if ($target <= 0) {
            return $achievement = 0;
        }
        $achievement = ($sales / $target) * 100;

        return number_format($achievement, 2, '.', '');
    }
}


if (!function_exists('calculateStringTimeToMiliseconds')) {
    function calculateStringTimeToMiliseconds($startDate, $endDate)
    {

        $date1 = new DateTime($startDate);
        $date2 = new DateTime($endDate);
        $interval = $date1->diff($date2);
        //$startTime = new \DateTime($startDate);
        //var_dump($startTime);
        //$endDate = new \DateTime($endDate);
//var_dump($endDate);
        //$interval = $startTime->diff($endDate);

        $totalMiliseconds = 0;
        $totalMiliseconds += $interval->m * 2630000000;
        $totalMiliseconds += $interval->d * 86400000;
        $totalMiliseconds += $interval->h * 3600000;
        $totalMiliseconds += $interval->i * 60000;
        $totalMiliseconds += $interval->s * 1000;

        return $totalMiliseconds;
    }

}

if (!function_exists('bn2en')) {
    function bn2en($number)
    {
        $bn = ["১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০"];
        $en = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];

        return str_replace($bn, $en, $number);
    }
}


if (!function_exists('en2bn')) {
    function en2bn($string)
    {
        $en = [
            "1", "2", "3", "4", "5", "6", "7", "8", "9", "0",
            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
        ];
        $bn = [
            "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০",
            "জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "অগাস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর"
        ];

        return str_replace($en, $bn, $string);
    }
}


function strip_except_english($str)
{

    // $str = preg_replace('/[^0-9A-Za-z\-]/', '', $str);
    $str = preg_replace('/\p{Han}+/u', '', $str);  // strip chinese
    $str = preg_replace('/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu', '', $str);  // strip russian
    return $str;
}

function cacheRemove()
{
    try {
        \Artisan::call('cache:clear');
        \Artisan::call('config:cache');
    } catch (\Throwable $th) {
    }
}


function getToday()
{
    return date('Y-m-d');
}

function getTodayWStartTime()
{
    return date('Y-m-d') . ' 00:00:00';
}

function getTodayWEndTime()
{
    return date('Y-m-d') . ' 23:59:59';
}

function getThisWeekFirstDay()
{
    return \Carbon\Carbon::now('+06:00')->subDays(6)->format('Y-m-d');
}

function getThisWeekFirstDayWStartTime()
{
    return \Carbon\Carbon::now('+06:00')->subDays(6)->format('Y-m-d') . ' 00:00:00';
}

function getThisMonthFirstDay()
{
    return \Carbon\Carbon::now('+06:00')->subDays(30)->format('Y-m-d');
}

function getThisMonthFirstDayWStartTime()
{
    return \Carbon\Carbon::now('+06:00')->subDays(30)->format('Y-m-d') . ' 00:00:00';
}

function getCurrentMonthFirstDayWStartTime()
{
    return \Carbon\Carbon::now('+06:00')->startOfMonth()->format('Y-m-d') . ' 00:00:00';
}

function getCurrentMonthLastDayWEndTime()
{
    return \Carbon\Carbon::now('+06:00')->endOfMonth()->format('Y-m-d') . ' 23:59:59';
}


function getTodayDateTime()
{
    return date('Y-m-d H:i:s');
}

function getNow()
{
    return \Carbon\Carbon::now('+06:00');
}

function getYesterday()
{
    return \Carbon\Carbon::yesterday()->format('Y-m-d');
}


function getCurrentYear()
{
    return \Carbon\Carbon::now('+06:00')->format('Y');
}

function getCurrentMonth()
{
    return \Carbon\Carbon::now('+06:00')->format('m');
}


// paginate
// paginate

function paginate($items, $perPage = 10, $page = null, $options = [])
{
    $page = $page ?: (Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Illuminate\Support\Collection ? $items : Illuminate\Support\Collection::make($items);

    $data = new Illuminate\Pagination\LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, $options);

    return $data;
}

function getFormattedPaginatedArray($data)
{
    return (object)[
        'current_page' => $data->currentPage(),
        'total_pages' => $data->lastPage(),
        'previous_page_url' => $data->previousPageUrl(),
        'next_page_url' => $data->nextPageUrl(),
        'record_per_page' => $data->perPage(),
        'current_page_items_count' => $data->count(),
        'total_count' => $data->total(),
        'pagination_last_page' => $data->lastPage(),
    ];
}


function getPaginatedSerial($paginator, $index)
{
    return $paginator->current_page > 1 ? (($paginator->current_page - 1) * $paginator->record_per_page + $index + 1) : $index + 1;
}

function generate_all_req_to_query_params($arr)
{
    $additional_query_params = '&';
    foreach ($arr as $key => $value) {
        // dd($key);
        // dd($value);
        if ($key != 'page') {
            $additional_query_params = $additional_query_params . $key . '=' . $value . '&';
        }
    }
    // dd($additional_query_params);
    return $additional_query_params;
}

function processStatus($array = [])
{
    $selected_value = $array['selected'] ?? '';
    $status = $array['status'] ?? '';

    $selected_key = '';

    foreach ($status as $key => $status_item) {
        if ($status_item['value'] == $selected_value) {
            $selected_key = $status_item['key'];
        }
    }
    return $selected_key;
}

function getDatesFromARange($firstDay, $numberofdays)
{
    $dates = [];
    $currentDate = $firstDay;
    for ($i = 0; $i < $numberofdays; $i++) {
        $dates[$i] = $currentDate;
        $currentDate = date('Y-m-d', strtotime("+1 day", strtotime($dates[$i])));
    }
    return $dates;
}

function getDatesFromARangeSubtractDay($firstDay, $numberofdays)
{
    $dates = [];
    $currentDate = $firstDay;
    for ($i = $numberofdays; $i > 0; $i--) {
        $dates[$i] = $currentDate;
        $currentDate = date('Y-m-d', strtotime("-1 day", strtotime($dates[$i])));
    }
    return $dates;
}


function getBGs()
{
    $arr = ['bg-orange-3', 'bg-blue-3', 'bg-green-3', 'bg-green-4'];
    return $arr;
}


function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function requestCodeStatusBdage($status)
{
    $class = '';
    if ($status === 1) {
        $class = 'badge badge-primary';
    } elseif ($status === 2) {
        $class = 'badge badge-warning';
    } elseif ($status === 3) {
        $class = 'badge badge-success';
    }elseif ($status === 4) {
        $class = 'badge badge-danger';
    }

    return $class;
}

/*
 luhn algo checksum find
*/
function generateChecksum($value)
{
    if (!is_numeric($value)) {
        return 0;
    }

    $value = (string)$value;
    $length = strlen($value);
    $parity = $length % 2;
    $sum = 0;

    for ($i = $length - 1; $i >= 0; --$i) {
        $char = $value[$i];
        if ($i % 2 != $parity) {
            $char *= 2;
            if ($char > 9) {
                $char -= 9;
            }
        }

        $sum += $char;
    }

    return ($sum * 9) % 10;
}

if (!function_exists('camelCaseToWords')) {
    function camelCaseToWords($word)
    {
        return ucwords(implode(' ', preg_split('/(?=[A-Z])/', $word)));
    }
}
function sendMaskSms($phone, $smg)
{
    // $maskName = 'SSG';
   //  $phone = normalizePhoneNumber($phone);

    // $url = "https://api.fastsmsbd.com/smsapiv4";

    // $response = Http::withHeaders([
    //     'Content-Type' => 'application/json',
    //     'Accept' => 'application/json',
    // ])->post($url, [
    //     'api_key'   => '0f0eec6960a1ba220e9d658b41d30b30',
    //     'type'      => 'text/unicode',
    //     'contacts'  => $phone,
    //     'senderid'  => $maskName,
    //     'purpose'   => 'Business Automation',
    //     'msg'       => $smg,
    // ]);

    // return $response->body();
    $maskName = 'SSG';
    $phone = normalizePhoneNumber($phone);

    $url = "https://smsapi.fastsmsbd.com/smsapiv4";

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post($url, [
        'api_key'   => 'b7bbbad2b9302200d22fba19024ee99b',
        'type'      => 'text/unicode',
        'contacts'  => $phone,
        'senderid'  => $maskName,
        'purpose'   => 'Business Automation',
        'msg'       => $smg,
    ]);

    return $response->body();
}


function normalizePhoneNumber($number)
{
    // Remove leading +880 or 880
    if (strpos($number, '+880') === 0) {
        $number = substr($number, 4); // Remove +880
    } elseif (strpos($number, '880') === 0) {
        $number = substr($number, 3); // Remove 880
    }
    // Ensure the number starts with 0
    // if ($number[0] !== '0') {
    //     $number = '0' . $number;
    // }
    return $number;
}



function phoneNumberNormalize($number) { 
    $number = preg_replace('/^(\+880|880)/', '', $number);
    
    // Ensure the number starts with 0
    if (strlen($number) > 0 && $number[0] !== '0') {
        $number = '0' . $number;
    }
    
    return $number;
}
function  novocomSMS($phone, $smg, $Is_Unicode = false , $Is_Flash = false , $DataCoding = false){

    $apiKey = 'CRxd+LEinn1wqDeI4UF3A7KdUtMImC8ugC9hVwxPZdo=';

    $clientID = '9c47a0ec-99d0-46fe-ba17-398cb7319262';
    $senderId  =  '8809638013420';

    $url ='https://sms.novocom-bd.com/api/v2/SendSMS?ApiKey=CRxd+LEinn1wqDeI4UF3A7KdUtMImC8ugC9hVwxPZdo=&ClientId='.$clientID .'&SenderId='.$senderId.'&Message='.$smg.'&MobileNumbers=88'.$phone.'&Is_Unicode='.$Is_Unicode.'&Is_Flash='.$Is_Flash.'&DataCoding='.$DataCoding;

 
  
    $response = Http::get( $url );
    return $response->json();  
}

function  fastSMS($phone, $smg){

    $data = [
        "api_key" => "$2y$10$5gH8PWulavwC6rCbA4X3Mu4GLy2f.eqRjFOAa0Eftd/txe1Iz2i3a174",
        "transaction_type" => "T",
        "campaign_id" => "cmp-DlVuuS8nxA",
        "sms_data" => [
            [
                "recipient" => $phone,
                "sender_id" => "8809617620482",
                "message" =>  $smg
            ]
        ]
    ];

    // Send the POST request
    $response = Http::post('https://fastsmsportal.com/smsapi', $data);

    // Check if the request was successful
    if ($response->successful()) {
        $responseData = $response->json(); 
        
        if ($responseData['status'] === 'Success') {
            return response()->json([
                'success' => true,
                'message' => 'SMS sent successfully.',
                'data' => $responseData
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS.',
                'data' => $responseData
            ]);
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Failed to connect to the SMS gateway.'
        ]);
    }
}
