<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Repositories\CampaignRepository;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Product;
use Flash;
use Illuminate\Support\Str;
use Response;

class CampaignController extends AppBaseController
{
    /** @var CampaignRepository $campaignRepository*/
    private $campaignRepository;

    public function __construct(CampaignRepository $campaignRepo)
    {
        $this->campaignRepository = $campaignRepo;
    }


    public function index(Request $request)
    {

        $campaigns = $this->campaignRepository->allQuery()->with('product','category')
            ->orderBy('id','desc')
            ->paginate(50);

        return view('campaigns.index')
            ->with('campaigns', $campaigns);
    }


    public function create()
    {

         $product =  Product::where('status', 1)
            ->orderBy('sku', 'asc')
            ->pluck('product_name', 'id')
            ->toArray();

        $types = ['image'=>'Image','link'=>'Youtube Link'];
        $campaignType = ['generale'=>'Generale','campaign_with_product'=>'Campaign with product'];
        $data['parentproducts'] =  $product  ;

        return view('campaigns.create',[
            'campaignTypes' => $campaignType,
            'types' => $types,
        ])->with($data);
    }

    /**
     * Store a newly created Campaign in storage.
     *
     * @param CreateCampaignRequest $request
     *
     */
    public function store(CreateCampaignRequest $request)
    {
        $input = $request->only('title','campaign_type','product_id','point','content_type','link');

        if ($request->hasFile('image')) {
            $newFileName = Str::random(64) . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('public/campaign', $newFileName, 'local');
            $input['image'] = $newFileName ;
        }

        $input['start_date'] = Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
        $input['end_date']   = Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');


        $this->campaignRepository->create($input);

        Flash::success('Campaign saved successfully.');

        return redirect(route('campaigns.index'));
    }

    /**
     * Display the specified Campaign.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $campaign = $this->campaignRepository->find($id);

        if (empty($campaign)) {
            Flash::error('Campaign not found');

            return redirect(route('campaigns.index'));
        }

        return view('campaigns.show')->with('campaign', $campaign);
    }

    /**
     * Show the form for editing the specified Campaign.
     *
     * @param int $id
     *
     */
    public function edit($id)
    {
        $campaign = $this->campaignRepository->find($id);

        $product =  Product::where('status', 1)
            ->orderBy('sku', 'asc')
            ->pluck('product_name', 'id')
            ->toArray();

        $types = ['image'=>'Image','link'=>'Youtube Link'];
        $campaignType = ['generale'=>'Generale','campaign_with_product'=>'Campaign with product'];
        $data['parentproducts'] =  $product  ;


        if (empty($campaign)) {
            Flash::error('Campaign not found');
            return redirect(route('campaigns.index'));
        }


        return view('campaigns.edit',[
            'campaignTypes' => $campaignType,
            'types' => $types,
            'campaign'=>$campaign,
        ])->with($data);
    }

    /**
     * Update the specified Campaign in storage.
     *
     * @param int $id
     * @param UpdateCampaignRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCampaignRequest $request)
    {
       // dd($request->all());
//        $campaign = $this->campaignRepository->find($id);
//
//        if (empty($campaign)) {
//            Flash::error('Campaign not found');
//            return redirect(route('campaigns.index'));
//        }
//
//         $this->campaignRepository->update($request->all(), $id);

        $input = $request->only('title','campaign_type','product_id','point','content_type','link');

        if ($request->hasFile('image')) {
            $newFileName = Str::random(64) . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('public/campaign', $newFileName, 'local');
            $input['image'] = $newFileName ;
        }

        if($request->campaign_type == 'generale'){
            $input['product_id'] = NULL;
            $input['point'] = 0;
        }

        if($request->content_type == 'image'){
            $input['link'] = NULL;
        }else{
            $input['image'] = NULL;
        }

        $input['start_date'] = Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
        $input['end_date']   = Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');

        $this->campaignRepository->update($input,$id);

        Flash::success('Campaign updated successfully.');

        return redirect(route('campaigns.index'));
    }

    /**
     * Remove the specified Campaign from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $campaign = $this->campaignRepository->find($id);

        if (empty($campaign)) {
            Flash::error('Campaign not found');

            return redirect(route('campaigns.index'));
        }

        $this->campaignRepository->delete($id);

        Flash::success('Campaign deleted successfully.');

        return redirect(route('campaigns.index'));
    }
}
