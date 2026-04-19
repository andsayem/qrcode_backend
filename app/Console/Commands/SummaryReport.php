<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserRedeemRequest;
use Illuminate\Console\Command; 
use App\Myclass\PHPMailer;
use App\Myclass\SMTP;
class SummaryReport extends Command
{
 
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:summary_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is summary report command';

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
        $phpMail = new PHPMailer();
        $message = "";
        $lastMonth = Carbon::now()->subMonth()->format('F Y');  
        $role = 'Technician';
        $startDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();  
        $query = User::with(['user_types:id,name'])
            ->where('status', 1)
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'desc');

        // Get the count of users
        $newUsersCount = $query->count();



        $redeemed = UserRedeemRequest::selectRaw('SUM(point) as total_points, SUM(amount) as total_amount')
        ->where('status', 1)
        ->whereBetween('paid_at', [$startDate, $endDate])
        ->first(); 
        $totalPoints = $redeemed ? $redeemed->total_points : 0;
        $totalAmount = $redeemed ? $redeemed->total_amount : 0;
     
        $totalPointsRedeemed =$totalPoints; 
        $totalAmountRedeemed =$totalAmount;  

        $message = view('mails.summary_report', [
            'month' => $lastMonth,
            'newUsersCount' => $newUsersCount,
            'totalPointsRedeemed' => $totalPointsRedeemed,
            'totalAmountRedeemed' => $totalAmountRedeemed 
            
        ]);


        $phpMail->AddAddress('shakil.raihan@ssgbd.com', 'Md. Rasheduzzaman-SM-IT');
        $phpMail->AddCC("abdullah.almamun@ssgbd.com","Abdullah  Al Mamun-DGM-MBD");
        $phpMail->AddCC("hossain.shahnewaz@ssgbd.com","Md. Hossain Shahnewaz-DGM-MBD");
        $phpMail->AddCC("rubab@ssgbd.com","Shamsul Arafin Rubab-M-MBD");
        $phpMail->AddCC("rashed.zzaman@ssgbd.com","Md. Rasheduzzaman-SM-IT");
        $phpMail->AddCC("sayed@ssgbd.com","Abu Sayed");

        $user = "Management Desk";
        $user_email = "management.desk@ssgbd.com";

        $msg = nl2br($message);

        $phpMail->FromName = $user;
        $phpMail->From = "management.desk@ssgbd.com";
        $phpMail->Sender = $user_email;
        $phpMail->IsHTML(true);
        $phpMail->Host = "mail.ssgbd.com:25";
        $phpMail->IsSMTP();
        $phpMail->Mailer  = "smtp";
        $phpMail->Subject = "Summary Report : SSG Apon";
        $phpMail->Body = $msg;
        $phpMail->SMTPAuth = false;

        if (!$phpMail->Send()) {
            echo "Message could not be sent.";
            echo "Mailer Error: " . $phpMail->ErrorInfo;
            // exit;
        }
        return   $message ;
        
   

    }
}
