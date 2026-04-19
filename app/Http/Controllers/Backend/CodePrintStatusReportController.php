<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CodeDetail;
use Illuminate\Http\Request;

class CodePrintStatusReportController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('code-print-status-list'), 403);

        $data['request'] = $request->all();
        $query = $this->getQuery($request);
        $data['tableData'] = $query->paginate(10);
        $data['download_url'] = route('admin.code-print-status-list.download') . '?' . http_build_query($request->all());

        return view('backend.print_status.index')->with($data);
    }

    public function downloadCSV(Request $request)
    {
        abort_if(!auth()->user()->can('code-print-status-list-download'), 403);

        ini_set('max_execution_time', 180000); // 30 mins
        ini_set('memory_limit', '100024M'); // 1024 MB

        $query = $this->getQuery($request);
        $data = $query->get()->map(function ($item, $key) {
            return [
                'Product SKU' => ($item->product->sku ?? '') . ' (' . ($item->product->product_name ?? '') . ')',
                'Serial' => $item->serial,
                'Code' => $item->final_unique_code,
                'Print Status' => $item->is_print === 1 ? 'Printed' : 'Not Print',
            ];
        });

        if (!$data->count()) {

            return redirect()->back()->with('fail', ['No Data Found For Download.']);
        }

        $arrayData = $data->toArray();

        $filename = 'code-print-status-report.csv';
        csvExport($arrayData, $filename);
    }

    private function getQuery($request)
    {
        $query = CodeDetail::with(['product'])->orderBy('id', 'desc');

        if (isset($request->code)) {
            $query->where('final_unique_code', $request->code);
        }

        if (isset($request->serial)) {
            $query->where('serial', $request->serial);
        }

        if (isset($request->is_print)) {
            $query->where('is_print', $request->is_print);
        }

        return $query;
    }
}
