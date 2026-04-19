<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $message;
    public $adminId;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param string $message
     * @param int $adminId
     */
    public function __construct(int $userId, string $message, int $adminId)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->adminId = $adminId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->userId);

        if (!$user) {
            return; // User deleted or invalid
        }

        // Send SMS via your gateway
       // $gatewayResponse = sendMaskSms($user->email, $this->message); // your SMS function
       $gatewayResponse = sendMaskSms( $user->email, $this->message); // your SMS function
    
        // Parse response
        $responseData = json_decode($gatewayResponse, true);
        $smsStatus = 'failed';
        $responseText = $gatewayResponse;

        if (isset($responseData['response'][0]['status'])) {
            $smsStatus = $responseData['response'][0]['status'] == 0 ? 'sent' : 'failed';
            $responseText = json_encode($responseData['response'][0]); // save only relevant info
        }

        // Log SMS
        SmsLog::create([
            'user_id' => $this->adminId, // Admin who sent
            'mobile' => $user->email,
            'message' => $this->message,
            'status' => $smsStatus,
            'response' => $responseText,
            'sent_at' => now(),
        ]);
    }
}
