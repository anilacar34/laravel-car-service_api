<?php

namespace App\Http\Controllers\Api\v1\Car;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Resources\CarServiceResource;
use App\Models\CarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarServiceController extends BaseController
{
    public function getServices(Request $request){
        $status = false;
        $message = 'Failed, service(s) not found !';
        $responseCode = 400;

        $requestBody = $request->query();

        $validator = Validator::make($requestBody,
            [
                'service_ids' => ['regex:/^(\d{1,20})(,\d{1,20})*$/'],
                'limit'      => 'numeric|max:100',
                'page'       => 'numeric|int',
            ]
        );

        $page = (int) ($requestBody['page'] ?? 1);
        $limit = (int) ($requestBody['limit'] ?? 100);

        if ($validator->fails()) {
            $validationErrors = $validator->errors();
        } else {

            if ($requestBody['service_ids'] ?? null) {
                $services = CarService::whereIn('id',explode(',', $requestBody['service_ids']))->paginate($limit,page:$page);
            }else{
                $services = CarService::paginate($limit,page:$page);;
            }

            $services = CarServiceResource::collection($services);

            $message = 'Success, service(s) retrieved !';
            $status = true;
            $responseCode = 200;
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'paginate' => array_filter([
                        'limit'      => ($services ?? []) ? $limit : 0,
                        'page'       => ($services ?? []) ? $page : 0,
                        'page_count' => ($services ?? []) ? $services->count() : 0
                    ]),
                    'services'=> $services ?? []
                ]),
                'failed' => array_filter([
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }
}
