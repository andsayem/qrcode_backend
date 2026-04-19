<?php

namespace App\Http\Controllers\Backend;
use App\Jobs\SendSmsJob;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SmsLog;
use App\Services\SmsService; // service class for gateway
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    // Show SMS page (send form + template selection)
    public function index()
    {
        $users = User::orderBy('name')->where('status',1)->get(); // all users for multi-select
        return view('backend.sms.index', compact('users'));
    }

    // Send SMS manually / bulk
    public function send(Request $request)
    {
        $request->validate([
            'employee_type' => 'required|in:all,individual',
            'message' => 'required|string',
           // 'users' => 'required_if:employee_type,individual|array',
        ]);

     
        // Determine recipients
        if ($request->employee_type === 'all') {
           $users = User::where('status', 1) 
                 ->where('email', 'like', '01%') // Bangladesh numbers only
                 ->get();
        } else {
            $users = User::whereIn('id', $request->users)
                        ->where('status', 1) // make sure selected users are active
                        ->get();
        }


    foreach ($users as $user) { 
        $gatewayResponse = sendMaskSms($user->email, $request->message);  
        $responseData = json_decode($gatewayResponse, true); 
        $smsStatus = 'failed';
        $responseText = $gatewayResponse;

        if (isset($responseData['response'][0]['status'])) {
            $smsStatus = $responseData['response'][0]['status'] == 0 ? 'sent' : 'failed';
            $responseText = json_encode($responseData['response'][0]); // store only relevant part
        }

        // Log SMS
        SmsLog::create([
            'user_id' => auth()->id(), // Admin who sent
            'mobile' => $user->email,
            'message' => $request->message,
            'status' => $smsStatus,
            'response' => $responseText,
            'sent_at' => now(),
        ]);
 
    }


        return redirect()->back()->with('success', 'SMS sent successfully!');
    }
 
}
