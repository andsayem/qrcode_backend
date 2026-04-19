<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use App\Repositories\OfferRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferController extends AppBaseController
{

    private $offerRepository;

    public function __construct(OfferRepository $_offerRepository)
    {
        $this->offerRepository = $_offerRepository;
    }


    public function index(Request $request)
    {
        $data = $this->offerRepository->allQuery()
            ->orderBy('id','desc')
            ->paginate(100);

        return view('backend.offers.index',[
            'offers' => $data
        ]);
    }



    public function create()
    {
        return view('backend.offers.create');
    }



    public function store(CreateOfferRequest $request)
    {

        try{

            $input = $request->only('title','description','is_active','point_value');
            $input['created_by'] = Auth::user()->id;

            if($request->hasFile('file')) {
                $image = $request->file('file');
                $extension = $image->getClientOriginalExtension();
                $fileName = time() .'.'. $extension;
                $image->storeAs('offer', $fileName, 'public');
                $input['image'] = $fileName;
            }

            Offer::create($input);

            return redirect()->route('admin.offers.index')->with('success', ['Data saved successfully']);

        }catch (\Exception $exception){
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.-'.$exception->getMessage()]);
        }
    }


    public function show($id)
    {

    }


    public function edit($id)
    {
        $data = $this->offerRepository->find($id);
        return view('backend.offers.edit',[
            'data' => $data
        ]);
    }


    public function update($id, UpdateOfferRequest $request)
    {

        try{
            $input = $request->only('title','description','is_active','point_value');
            $input['created_by'] = Auth::user()->id;

            if($request->hasFile('file')) {
                $image = $request->file('file');
                $extension = $image->getClientOriginalExtension();
                $fileName = time() .'.'. $extension;
                $image->storeAs('offer', $fileName, 'public');
                $input['image'] = $fileName;
            }

            Offer::where(['id'=>$id])->update($input);

            return redirect()->route('admin.offers.index')->with('success', ['Data updated successfully']);

        }catch (\Exception $exception){
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.-'.$exception->getMessage()]);
        }
    }


    public function destroy($id)
    {

        $data = $this->offerRepository->find($id);

        if (empty($data)) {
            return $this->sendError('Offer not found');
        }

        $data->delete();

        return redirect(route('admin.offers.index'));
    }


}