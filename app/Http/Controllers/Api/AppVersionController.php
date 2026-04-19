<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppVersion; 
use App\Http\Resources\AppVersionResource;
class AppVersionController extends Controller
{
    public function index()
    {
        return new AppVersionResource(AppVersion::first());
        //   return AppVersionResource::collection(AppVersion::get());
    }

    public function store(Request $request)
    {
      $appVersion = AppVersion::create([ 
        'version' => $request->version, 
      ]);

      return new AppVersionResource($appVersion);
    }

    public function show(AppVersion $appVersion)
    {
      return new AppVersionResource($appVersion);
    }

    public function update(Request $request, AppVersion $appVersion)
    { 
      $appVersion->update($request->only(['version']));
      return new AppVersionResource($appVersion);
    }

    public function destroy(AppVersion $appVersion)
    {
      // $appVersion->delete();

      // return response()->json(null, 204);
    }
}
