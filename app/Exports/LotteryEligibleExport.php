<?php

namespace App\Exports;

use App\Models\Lottery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LotteryEligibleExport implements FromCollection, WithHeadings, WithMapping
{
    protected $lottery;
    protected $rowNumber = 0;

    public function __construct(Lottery $lottery)
    {
        $this->lottery = $lottery;
    }

    public function collection()
    {
        return $this->lottery->eligibleUsersDetailQuery()->get();
    }

    public function headings(): array
    {
        return [
            'SL',
            'User ID',
            'Name',
            'Phone',
            'Division',
            'District',
            'Thana',
            'Total Points',
        ];
    }

    public function map($row): array
    {
        return [
            ++$this->rowNumber,
            $row->user_id,
            $row->name,
            $row->phone_number ?: $row->email,
            $row->division_name,
            $row->district,
            $row->thana,
            $row->total_points,
        ];
    }
}
