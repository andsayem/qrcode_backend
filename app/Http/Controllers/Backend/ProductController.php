<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\Category;
use App\Models\Channel;
use App\Utilities\Enum\StatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        if (!auth()->user()->can('product-list')) {
            abort(403);
        }
        $products = $this->productFilterProcess(new Request($request->all()));
        $data['products'] = $products->orderBy('id', 'desc')->paginate(10);
        $data['parentcategories'] = Category::orderBy('category_name', 'asc')
            ->where('status', StatusEnum::Active)
            ->pluck('category_name', 'id')
            ->toArray();

        return view('backend.product.index')->with($data);
    }

    public function download(Request $request)
    {
        if (!auth()->user()->can('product-list')) {
            abort(403);
        }

        // Apply same filters as index
        $products = $this->productFilterProcess(new Request($request->all()))
            ->orderBy('id', 'desc')
            ->get();

        $filename = 'products_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            // CSV header
            fputcsv($file, [
                'ID',
                'Product Name',
                'Description',
                'Category',
                'Status',
                'Sap Code',
                'Point'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->product_name ?? '',
                    $product->desc ?? '',
                    $product->category->category_name ?? '',
                    $product->status == StatusEnum::Active ? 'Active' : 'Inactive',
                    $product->sap_code ?? '',
                    $product->point_slab ?? 0
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function upload(Request $request)
    {
        if (!auth()->user()->can('product-list')) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        // Skip header row
        fgetcsv($file);

        $updated = 0;

        while (($row = fgetcsv($file)) !== false) { 
            $productId = $row[0] ?? null; // ID column
            $sap_code = $row[5] ?? null; // Sap Code column
            $point = isset($row[6]) ? floatval($row[6]) : 0.0;

            if (!$productId ) {
                continue;
            }

            $updated += Product::where('id', $productId)
                ->update([
                    'point_slab' => $point,
                    'sap_code' => $sap_code,
                ]);
        }

        fclose($file);

        return back()->with('success', " product points updated successfully.");
    }


    public function productFilterProcess(Request $request)
    {
        $products = Product::with(['category'])->where('id', '>', 0);
        if (isset($request->product_name)) {
            $products = $products->where('product_name', 'like', '%' . $request->product_name . '%');
        }
        if (isset($request->category_id)) {
            $products = $products->where('category_id', $request->category_id);
        }
        if (isset($request->sku)) {
            $products = $products->where('sku', 'like', '%' . $request->sku . '%');
        }

        return $products;
    }

    public function create()
    {
        if (!auth()->user()->can('product-create')) {
            abort(403);
        }

        $data['channels'] = Channel::orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();
        $data['parentcategories'] = Category::orderBy('category_name', 'asc')
            ->where('status', StatusEnum::Active)
            ->pluck('category_name', 'id')
            ->toArray();

        return view('backend.product.create')->with($data);
    }


    public function store(ProductRequest $request)
    {
        if (!auth()->user()->can('product-create')) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $request['created_by'] = Auth::user()->id;
            Product::create($request->all());
            DB::commit();
            return redirect()->route('admin.products.index')->with('success', ['Product created successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('product-edit')) {
            abort(403);
        }
        $data['channels'] = Channel::orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();
        $data['parentcategories'] = Category::orderBy('category_name', 'asc')->pluck('category_name', 'id')->toArray();

        $data['editModeData'] = Product::findOrFail($id);
        return view('backend.product.edit')->with($data);
    }


    public function update(ProductRequest $request, $id)
    {
        if (!auth()->user()->can('product-edit')) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $request['updated_by'] = Auth::user()->id;
            $product = Product::findOrFail($id);
            $product->update($request->except(['sku']));

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', ['Product updated successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }


}
