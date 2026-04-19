<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use App\Models\CodeDetail;
use App\Models\Product;
use App\Models\RequestCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;
use config;

class FileGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is file generate command';

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
        try {
            ini_set('max_execution_time', config('app.memory_limit'));
            ini_set('memory_limit', config('app.max_report_execution_time'));

            $requestCode = RequestCode::where('status', 3)->where('is_file_generate', 0)->orderBy('id', 'desc')->first();
            if (!$requestCode) {

                report('No request found');
                $this->info('No request found');

                exit();
            }
            $setting_id = $requestCode->id;
            $product = Product::find($requestCode->product_id);
            $final_unique_code = $this->clean($product->product_name) . '_QR' . '-' . date('Ymd') . '-' . time();
            $data = CodeDetail::where('request_code_id', $setting_id);


            if (!$data->count()) {

                report('No Data Found For Download');
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

            $this->info('File generate done successfully');
        } catch (\Exception $e) {
            report($e);
        }
    }
    function clean($string)
    {
        $string = str_replace(' ', ' ', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);

        return preg_replace('/-+/', '-', $string);
    }
}
