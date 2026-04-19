<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Learning;
use App\Models\LearningImage;
use App\Repositories\LearningRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearningController extends AppBaseController
{

    /** @var  LearningRepository */
    private $learningRepository;

    public function __construct(LearningRepository $_learningRepository)
    {
        $this->learningRepository = $_learningRepository;
    }

    /**
     * Display a listing of the Feedback.
     * GET|HEAD /feedback
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $this->learningRepository->allQuery()->with('images')
            ->orderBy('id','desc')
            ->paginate(10); //->orderBy($columns[$column], $dir);

        return view('backend.learnings.index',[
            'learnings' => $data
        ]);
    }



    public function create()
    {
        $types = ['image'=>'Image','link'=>'Youtube Link'];
        return view('backend.learnings.create',[
            'types' => $types
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'is_active' => 'required',
        ]);

        try{
            DB::beginTransaction();
            $input = $request->only('title','type','description','is_active','path');
            $input['created_by'] = Auth::user()->id;
            $learning = Learning::create($input);

            if($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $image) {
                    $extension = $image->getClientOriginalExtension();
                    $fileName = time() . $index . '.' . $extension;
                    $path = $image->storeAs('uploads', $fileName, 'public');

                    LearningImage::create([
                        'learning_id' => $learning->id,
                        'path' => $path,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.learnings.index')->with('success', ['Data saved successfully']);

        }catch (\Exception $exception){
            DB::rollBack();
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.-'.$exception->getMessage()]);
        }
    }


    public function show($id)
    {

    }


    public function edit($id)
    {
        $types = ['image'=>'Image','link'=>'Youtube Link'];
        $data = $this->learningRepository->find($id);
        return view('backend.learnings.edit',[
            'types' => $types,
            'data' => $data
        ]);
    }


    public function update($id, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'is_active' => 'required',
        ]);


        try{
            DB::beginTransaction();

            $input = $request->only('title','type','description','is_active','path');
            $input['created_by'] = Auth::user()->id;
            Learning::where(['id'=>$id])->update($input);

            if($request->hasFile('files')) {
                LearningImage::where(['learning_id' => $id])->delete();
                foreach ($request->file('files') as $index => $image) {
                    $extension = $image->getClientOriginalExtension();
                    $fileName = time().$index. '.' . $extension;
                    $path = $image->storeAs('uploads', $fileName, 'public');

                    LearningImage::create([
                        'learning_id' => $id,
                        'path' => $path,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.learnings.index')->with('success', ['Data updated successfully']);

        }catch (\Exception $exception){
            DB::rollBack();
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.-'.$exception->getMessage()]);
        }
    }


    public function destroy($id)
    {

        $data = $this->learningRepository->find($id);

        if (empty($data)) {
            return $this->sendError('Learning and Tutorial data not found');
        }

        $data->delete();

        return redirect(route('admin.learnings.index'));
    }
}