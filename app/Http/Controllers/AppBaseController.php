<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    public function sendSuccess($message)
    {
        return Response::json([
            'success' => true,
            'message' => $message
        ], 200);
    }


    function uploadFile($file, $upload_path='', $prefix='', $height='', $width=''){
        $file_name = $prefix.time() . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('uploads/'.$upload_path), $file_name);
        return $file_name;
    }
}
