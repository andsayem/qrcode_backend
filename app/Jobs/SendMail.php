<?php

namespace App\Jobs;

use config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;


class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data = [];

    public $tries = 3;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;

        try{
            Mail::send('mails.basic_dynamic_mail', $data, function ($message)  use ($data) {
                $message->to($data['mailReceiverEmail'], $data['mailReceiverName'])
                        ->sender($data['mailSenderEmail'], $data['mailReceiverName'])
                        ->priority(1)
                        ->subject($data['subject']);
                $message->from($data['mailSenderEmail'], $data['mailReceiverName']);
            });
        }
        catch (\Exception $e) {
            Log::error($e);
        }
    }
}
