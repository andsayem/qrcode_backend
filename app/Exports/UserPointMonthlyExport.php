<?php 
namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserPointMonthlyExport implements FromCollection, WithHeadings, WithMapping
{
    protected $months;

    public function __construct($startMonth, $endMonth)
    {
        $start = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $endMonth)->startOfMonth();

        while ($start <= $end) {
            $this->months[] = $start->format('Y-m');
            $start->addMonth();
        }
    }

    public function collection()
    {
        $select = [
            'users.name as user_name',
            'users.email',
            'geo_divisions.name as division_name',
            'geo_district.district',
            'geo_thana.thana',
        ];

        foreach ($this->months as $month) {
            $alias = 'points_' . str_replace('-', '_', $month);
            $select[] = DB::raw("
                SUM(
                    CASE 
                        WHEN DATE_FORMAT(user_points.created_at, '%Y-%m') = '{$month}'
                        THEN user_points.point ELSE 0
                    END
                ) as {$alias}
            ");
        }

        return DB::table('technicians')
            ->join('users', 'users.id', '=', 'technicians.user_id')
            ->leftJoin('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id')
            ->leftJoin('geo_district', 'geo_district.id', '=', 'technicians.district_id')
            ->leftJoin('geo_thana', 'geo_thana.id', '=', 'technicians.upazilla_id')
            ->leftJoin('user_points', 'user_points.user_id', '=', 'technicians.user_id')
            ->select($select)
            ->groupBy(
                'users.name',
                'users.email',
                'geo_divisions.name',
                'geo_district.district',
                'geo_thana.thana'
            )
            ->orderBy('users.name')
            ->get();
    }

    public function headings(): array
    {
        $headers = [
            'Name',
            'Email',
            'Division',
            'District',
            'Thana',
        ];

        foreach ($this->months as $month) {
            $headers[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
        }

        return $headers;
    }

    public function map($row): array
    {
        $data = [
            $row->user_name,
            $row->email,
            $row->division_name,
            $row->district,
            $row->thana,
        ];

        foreach ($this->months as $month) {
            $field = 'points_' . str_replace('-', '_', $month);
            $data[] = $row->$field ?? 0;
        }

        return $data;
    }
}
