<?php

namespace App\Http\Controllers\Backend;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        if (!auth()->user()->can('category-list')) {
            abort(403);
        }
        $categories = $this->categoryFilterProcess(new Request($request->all()));
        $data['categories'] = $categories->orderBy('id', 'desc')->paginate(10);
        /*$data['parentcategories'] = Category::orderBy('category_name', 'asc')->pluck('category_name', 'id')->toArray();*/

        return view('backend.category.index')->with($data);
    }

    public function categoryFilterProcess(Request $request)
    {
        $categories = Category::where('id', '>', 0);
        if (isset($request->category_name)) {
            $categories = $categories->where('category_name', 'like', '%' . $request->category_name . '%');
        }
        if (isset($request->parent_id)) {
            $categories = $categories->where('parent_id', $request->parent_id);
        }
        if (isset($request->status)) {
            $categories = $categories->where('status', $request->status);
        }

        return $categories;
    }

    public function create()
    {
        if (!auth()->user()->can('category-create')) {
            abort(403);
        }

        $data['parentcategories'] = Category::orderBy('category_name', 'asc')->pluck('category_name', 'id')->toArray();

        return view('backend.category.create')->with($data);
    }


    public function store(CategoryRequest $request)
    {
        if (!auth()->user()->can('category-create')) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $request['created_by'] = Auth::user()->id;
            Category::create($request->all());
            DB::commit();
            return redirect()->route('admin.categories.index')->with('success', ['Category created successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('category-edit')) {
            abort(403);
        }

        $data['parentcategories'] = Category::where('id', '!=', $id)->orderBy('category_name', 'asc')->pluck('category_name', 'id')->toArray();

        $data['editModeData'] = Category::findOrFail($id);
        return view('backend.category.edit')->with($data);
    }


    public function update(CategoryRequest $request, $id)
    {
        if (!auth()->user()->can('category-edit')) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $request['updated_by'] = Auth::user()->id;
            $category = Category::findOrFail($id);
            $category->update($request->all());

            DB::commit();
            return redirect()->route('admin.categories.index')->with('success', ['Category updated successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }


}
