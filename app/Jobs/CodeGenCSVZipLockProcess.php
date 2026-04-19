<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use ZipArchive;
use App\Models\CodeDetail;
use App\Models\RequestCode;
use config;
use Illuminate\Support\Facades\File;
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


class CodeGenCSVZipLockProcess implements ShouldQueue
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
        Log::alert('hit');
        $data = $this->data;

        try {
            ini_set('max_execution_time', config('app.memory_limit'));
            ini_set('memory_limit', config('app.max_report_execution_time'));

            $setting_id = $data['setting_id'];
            $final_unique_code = $data['final_unique_code'];
            $data = CodeDetail::where('request_code_id', $setting_id);


            if (!$data->count()) {

                report('No Data Found For Download');
            }

            // =========================================================
            // some data to be used in the csv files
            /*$headers = array_keys($data[0]);
            dd($headers);
            $records = $data->toArray();*/
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
                    'Url' => 'https://qrc.ssgbd.com/checkCodeURL/' . $item->final_unique_code,
                ];
                fputcsv($fd, $array);
            };
            /*foreach ($records as $record) {
                fputcsv($fd, $record);
            }*/

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
        } catch (\Exception $e) {
            report($e);
        }
    }
}
