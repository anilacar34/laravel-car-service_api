<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;


class BaseController extends Controller
{
    public function sendResponse($message,$data = []):JsonResponse
    {
        $response = [
            'success'   => true,
            'message'   => $message,
        ];

        if(!empty($data)){
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    public function sendError($message,$data = [],$code = 404):JsonResponse
    {
        $response = [
            'success'   => false,
            'message'   => $message,
        ];

        if(!empty($data)){
            $response['data'] = $data;
        }

        return response()->json($response,$code,[]);
    }

    public function sendInternalError(){
        $response = [
            'success'   => false,
            'message'   => 'Internal error, your request is not processed!',
        ];

        return response()->json($response,500,[]);
    }
}
