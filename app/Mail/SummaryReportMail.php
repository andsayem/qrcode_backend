<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SummaryReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $month;
    public $newUsersCount;
    public $totalPointsRedeemed;
    public $totalAmountRedeemed;

    public function __construct($month, $newUsersCount, $totalPointsRedeemed, $totalAmountRedeemed)
    {
        $this->month = $month;
        $this->newUsersCount = $newUsersCount;
        $this->totalPointsRedeemed = $totalPointsRedeemed;
        $this->totalAmountRedeemed = $totalAmountRedeemed;
    }

    public function build()
    {
        return $this->view('mails.summary_report')
            ->subject('Summary Report : SSG Apon');
    }
}
