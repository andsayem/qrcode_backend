<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CampaignRequest;
use App\Repositories\CommonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use DB;
use Hash;
use App\Traits\FileHandlerTrait;

class CampaignController extends Controller
{
    use FileHandlerTrait;

    private $view_path = 'backend.campaigns.';
    private $route_path = 'campaignss';
    private $commonRepository;
    private $campaign;

    public function __construct(Request $request, CommonRepository $commonRepository)
    {
        $this->campaign = new Campaign;
        $this->commonRepository = $commonRepository;
    }


    public function index(Request $request)
    { 
       $data['tableData'] =  $query = Campaign::all();  

       return view('backend.campaigns.index')->with($data);
    }

    /**
     * Show the form for creating a new Campaign.
     *
     * @return Response
     */
    public function create()
    {   
      
        $product =  Product::where('status', 1)
            ->orderBy('sku', 'asc')
            ->pluck('sku', 'id')
            ->toArray(); 
        $data['parentproducts'] =  $product  ;

        return view('backend.campaigns.create')->with($data);
        
    }

    /**
     * Store a newly created Campaign in storage.
     *
     * @param CreateCampaignRequest $request
     *
     * @return Response
     */
    public function store(CreateCampaignRequest $request)
    {
        $input = $request->all();

        $campaign = $this->campaignRepository->create($input);

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
     * @return Response
     */
    public function edit($id)
    {
        $campaign = $this->campaignRepository->find($id);

        if (empty($campaign)) {
            Flash::error('Campaign not found');

            return redirect(route('campaigns.index'));
        }

        return view('campaigns.edit')->with('campaign', $campaign);
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
        $campaign = $this->campaignRepository->find($id);

        if (empty($campaign)) {
            Flash::error('Campaign not found');

            return redirect(route('campaigns.index'));
        }

        $campaign = $this->campaignRepository->update($request->all(), $id);

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
