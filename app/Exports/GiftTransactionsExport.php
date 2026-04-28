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
        $query = GiftTransaction::with(['user', 'gift', 'policy']);

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
        return $query->get()->map(function ($transaction) {
            return [
                'ID' => $transaction->id,
                'User Name' => $transaction->user->name ?? 'N/A',
                'Gift' => $transaction->gift->gift_name ?? 'N/A',
                'Policy' => $transaction->policy->program_name ?? 'N/A',
                'Request Date' => $transaction->requested_at
                    ? \Carbon\Carbon::parse($transaction->requested_at)->format('d M Y')
                    : 'N/A',
                'Request Status' => match ($transaction->request_status) {
                    0 => 'Pending',
                    1 => 'Approved',
                    2 => 'Rejected',
                    default => 'Unknown',
                },
                'Delivery Status' => ucfirst($transaction->delivery_status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Gift',
            'Policy',
            'Request Date',
            'Request Status',
            'Delivery Status',
        ];
    }
}