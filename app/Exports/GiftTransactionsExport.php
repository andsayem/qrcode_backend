<?php
namespace App\Exports;

use App\Models\GiftTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GiftTransactionsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = GiftTransaction::with(['user', 'gift', 'policy','user.technician','user.technician.district','user.technician.thana']);

        // Apply filters
        if (!empty($this->filters['user'])) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->filters['user']}%")
                    ->orWhere('email', 'like', "%{$this->filters['user']}%");
            });
        }

        if (!empty($this->filters['gift_id'])) {
            $query->where('gift_id', $this->filters['gift_id']);
        }

        if (!empty($this->filters['policy_id'])) {
            $query->where('policy_id', $this->filters['policy_id']);
        }

        // Request status / Sent logic
        $status = $this->filters['request_status'] ?? null;

        if ($status === 'sent') {
            $query->where('delivery_status', 'sent');
        } elseif (isset($status) && $status !== '') {
            $query->where('request_status', $status);
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $query->whereBetween('requested_at', [
                $this->filters['from_date'] . ' 00:00:00',
                $this->filters['to_date'] . ' 23:59:59',
            ]);
        }

        // Fetch the data with related fields
        // return $query->get()->map(function ($transaction) {
        //     return [
        //         'ID' => $transaction->id,
        //         'User Name' => $transaction->user->name ?? 'N/A',
        //         'Gift' => $transaction->gift->gift_name ?? 'N/A',
        //         'Policy' => $transaction->policy->program_name ?? 'N/A',
        //         'Request Date' => $transaction->requested_at
        //             ? \Carbon\Carbon::parse($transaction->requested_at)->format('d M Y')
        //             : 'N/A',
        //         'Request Status' => match ($transaction->request_status) {
        //             0 => 'Pending',
        //             1 => 'Approved',
        //             2 => 'Rejected',
        //             default => 'Unknown',
        //         },
        //         'Delivery Status' => ucfirst($transaction->delivery_status),
        //     ];
        // });


        return $query->get()->map(function ($transaction) {
            $statuses = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];

            if ($transaction->user?->technician?->payment_gateway == 1){
                $gateway_type = "BKash";
            }else if($transaction->user?->technician?->payment_gateway == 2){
                $gateway_type = "Nagad";
            }else if($transaction->user?->technician?->payment_gateway == 3){
                $gateway_type = "Rocket";
            }            

            return [

                'ID' => $transaction->id,
                
                "User ID" => $transaction->user->id ?? 'N/A',

                'User Name' => $transaction->user->name ?? 'N/A',
                
                'Gatway Number' => $transaction->user?->technician?->gatway_number ?? 'N/A',
                'Gatway Type' => $gateway_type ?? 'N/A',
                'Point Name' => $transaction->user?->technician?->point_name ?? 'N/A',
                'Thana' => $transaction->user?->technician?->thana?->thana ?? 'N/A',
                'District' => $transaction->user?->technician?->district?->district ?? 'N/A',

                'Gift' => $transaction->gift->gift_name ?? 'N/A',
                'Point' => $transaction->gift->point_slab ?? 'N/A',
                'Policy' => $transaction->policy->program_name ?? 'N/A',

                'Request Date' => $transaction->requested_at
                    ? \Carbon\Carbon::parse($transaction->requested_at)->format('d M Y')
                    : 'N/A',

                'Request Status' => $statuses[$transaction->request_status] ?? 'Unknown',

                'Delivery Status' => ucfirst($transaction->delivery_status ?? 'N/A'),

            ];

        });

    }

    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'User Name',
            'Gatway Number',
            'Gatway Type',
            'Point Name',
            'Thana',
            'District',
            'Gift',
            'Point',
            'Policy',
            'Request Date',
            'Request Status',
            'Delivery Status',
        ];
    }
}