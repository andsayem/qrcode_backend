<?php
/*
use Illuminate\Support\Facades\DB;
try{
    DB::beginTransaction();
    DB::commit();
}catch(\Exception $e){
    DB::rollback();
}
*/

use Illuminate\Support\Facades\Http;
function getIPAddress()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

/** base64 image upload function */
function base64_to_image($base64_string, $location)
{
    $filename = time().rand(100,999).".jpg";
    $local_path  = $_SERVER['DOCUMENT_ROOT'];

    $path        = env('APP_PATH')."/public/" . $location . "/" . $filename;
    $output_file = $local_path . "/" . $path; //save to local address

    // open the output file for writing
    $ifp = fopen($output_file, 'wb');
    
    $data = explode(',', $base64_string);
    if(sizeof($data) > 1)
    {
        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));
        // clean up the file resource
        fclose($ifp);
    }
    else
    {
        $filename = NULL;
    }

    return $filename;
}


function sendSms($phone, $smg)
{
    
    $maskName = 'SSG';
   // $phone = normalizePhoneNumber($phone);

   // $url = "https://api.fastsmsbd.com/smsapiv4";

    $url = "https://smsapi.fastsmsbd.com/smsapiv4";

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post($url, [
        'api_key'   => 'b7bbbad2b9302200d22fba19024ee99b',
        //'api_key'   => '0f0eec6960a1ba220e9d658b41d30b30',
        'type'      => 'text/unicode',
        'contacts'  => $phone,
        'senderid'  => $maskName,
        'purpose'   => 'Business Automation',
        'msg'       => $smg,
    ]);

    return $response->body();
}


// function normalizePhoneNumber($number)
// {
//     // Remove leading +880 or 880
//     if (strpos($number, '+880') === 0) {
//         $number = substr($number, 4); // Remove +880
//     } elseif (strpos($number, '880') === 0) {
//         $number = substr($number, 3); // Remove 880
//     }
//     // Ensure the number starts with 0
//     // if ($number[0] !== '0') {
//     //     $number = '0' . $number;
//     // }
//     return $number;
// }
function cstr_slug($string)
{
    return preg_replace('/\s+/u', '-', trim($string));
}



function refCode()
{
    return chr(rand(65,90)).substr(time(), -4).chr(rand(65,90));
}

 

 
function csvToArray($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename)) {
        return false;
    }

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if($row[0] == 'id')
            {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            else
            {
                if(!empty($header))
                {
                    $data[] = array_combine($header, $row);
                }
            }
        }
        fclose($handle);
    }
    return $data;
}


function onSignal($order){
    
    // $rest_api_key =  'NWZmNTIyMjUtNDBmYi00MTZjLWEwZjUtOGNiYTk2NTE5Y2E4';
    // $app_id  ='25afc514-5707-4aa1-9c23-063daadc5a30'; 
    // 6-7-21 change date 
    $rest_api_key =  'YWJlOTdhM2YtY2FjYi00ZGI5LWFiODQtZjQ1OGE4YTFjNzg0';
    $app_id  ='c1bb78e3-165d-4dc1-b3bb-3ff753ae546b'; 
    $text = "Category : " . $order->category_name .", Date : " . $order->date  .", Time : " . $order->time ;
    $content      = array(
        "en" => $text ,
    );
    $heading = array(
      "en" => 'New Job' ,
  ); 
    $Additional_Data =  array('type' => 'order' , 'order_id' =>  $order->id ); 
    $fields = array(
        'app_id' => $app_id  , 
        'data' => $Additional_Data ,
        'contents' => $content, 
        'headings' => $heading,
        "large_icon" => "https://mistrimama.com/backend/public/frontend/logo.png", 
    );  
    $fields['included_segments'] = array('All') ;
    $fields = json_encode($fields);  
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.$rest_api_key,
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($ch); 
    curl_close($ch); 
    return  $data = json_decode($response, true); 
}
function onSignalFoUser($data){
    
    // $rest_api_key =  'NWZmNTIyMjUtNDBmYi00MTZjLWEwZjUtOGNiYTk2NTE5Y2E4';
    // $app_id  ='25afc514-5707-4aa1-9c23-063daadc5a30'; 
    // 6-7-21 change date
    $rest_api_key =  'NGFkMTIzMzItMTQwYy00OWU4LTlhYzEtNTY5Mjg0MTVkMTMy';
    $app_id  ='78e29601-82fc-40c1-b10a-5a59751a7263'; 
    $text = $data['description'];
    $content      = array(
        "en" => $text ,
    );
    $heading = array(
      "en" => $data['notifications_title'],
    ); 
    $Additional_Data =  array('type' => 'notification'); 
    $fields = array(
        'app_id' => $app_id  , 
        'data' => $Additional_Data ,
        'contents' => $content,
        'headings' => $heading,
        "large_icon" => "https://mistrimama.com/backend/public/frontend/logo.png", 
    );  
    $fields['included_segments'] = array('All') ;
    $fields = json_encode($fields);  
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.$rest_api_key,
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($ch); 
    curl_close($ch); 
    return  $data = json_decode($response, true); 
}
