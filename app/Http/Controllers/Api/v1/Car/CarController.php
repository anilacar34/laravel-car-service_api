<?php

namespace App\Http\Controllers\Api\v1\Car;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModelYear;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CarController extends BaseController
{
    const FILLABLE = [
        'car' => [
            'url',
            'option',
            'engine_cylinders',
            'engine_displacement',
            'engine_power',
            'engine_torque',
            'engine_fuel_system',
            'engine_fuel',
            'engine_c2o',
            'performance_top_speed',
            'performance_acceleration',
            'fuel_economy_city',
            'fuel_economy_highway',
            'fuel_economy_combined',
            'transmission_drive_type',
            'transmission_gearbox',
            'brakes_front',
            'brakes_rear',
            'tires_size',
            'dimensions_length',
            'dimensions_width',
            'dimensions_height',
            'dimensions_front_rear_track',
            'dimensions_wheelbase',
            'dimensions_ground_clearance',
            'dimensions_cargo_volume',
            'dimensions_cd',
            'weight_unladen',
            'weight_limit',
        ]
    ];

    public function saveCars(){
        ini_set('memory_limit',-1);
        set_time_limit(0);

        $client = new Client(['verify' => false]);
        $responseApi = $client->request('GET', 'https://static.novassets.com/automobile.json', [
            'headers'        => ['Accept' => 'application/json'],
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        if($responseApi->getStatusCode() == 200){
            $responseData = json_decode($responseApi->getBody());

            foreach($responseData->RECORDS as $carItem){
                $carDb = Car::where(['car_id' => $carItem->id])->first();
                if(!$carDb){

                    // brand
                    $carBrand = CarBrand::where('name',trim($carItem->brand))->first();
                    if(!$carBrand){
                        $carBrand = new CarBrand;
                        $carBrand->name = trim($carItem->brand);
                        $carBrand->save();
                    }

                    // car model
                    $carModelName = substr($carItem->model,(mb_strlen($carItem->brand) + 2),-(mb_strlen($carItem->year) + 2));

                    $car = new Car;
                    $car->car_id = (int)$carItem->id;
                    $car->brand_id = (int)$carBrand->id;
                    $car->model = $carModelName;
                    $car->fillable(self::FILLABLE['car']);
                    $car->fill((array)$carItem);
                    $car->save();

                    // car model year
                    $carModelYearList = array_map('intval', explode(' - ',$carItem->year));
                    if(isset($carModelYearList[1])){
                        if($carModelYearList[1] === 0){ // equal to 'present'
                            $carModelYearList[1] = Carbon::now()->year;
                        }
                        for($i=$carModelYearList[0] + 1; $i < $carModelYearList[1]; $i++){
                            $carModelYearList[] = $i;
                        }
                        $carModelYearList = collect($carModelYearList)->sort();
                    }

                    foreach ($carModelYearList as $carModelYear){
                        CarModelYear::firstOrCreate([
                            'car_id' => (int)$car->id,
                            'year'   => $carModelYear
                        ]);
                    }
                }
            }
        }else{
            $failed = [
                'status_code'   => $responseApi->getStatusCode(),
                'responseData'  => json_decode($responseApi->getBody() ?? [])
            ];

            //todo log
        }
    }

    public function getCars(Request $request){
        $status = false;
        $message = 'Failed, cars(s) not found !';
        $responseCode = 400;

        $requestBody = $request->query();

        $validator = Validator::make($requestBody,
            [
                'car_ids'        => ['regex:/^(\d{1,20})(,\d{1,20})*$/'],
//                'model'          => 'string',
//                'year'           => ['numeric','int','max:'.Carbon::now()->year],
                'limit'          => 'numeric|max:100',
                'page'           => 'numeric|int',
            ]
        );

        $page = (int) ($requestBody['page'] ?? 1);
        $limit = (int) ($requestBody['limit'] ?? 100);

        if ($validator->fails()) {
            $validationErrors = $validator->errors();
        } else {

            $carDb = Car::with(['brand','years']);

            if ($requestBody['car_ids'] ?? null) {
                $carDb = $carDb->whereIn('id',explode(',', $requestBody['car_ids']));
            }

            $carDb = $carDb->paginate($limit,page:$page);
            $carDb = CarResource::collection($carDb);

            $message = 'Success, car(s) retrieved !';
            $status = true;
            $responseCode = 200;
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'paginate' => array_filter([
                        'limit'      => ($carDb ?? []) ? $limit : 0,
                        'page'       => ($carDb ?? []) ? $page : 0,
                        'page_count' => ($carDb ?? []) ? $carDb->count() : 0
                    ]),
                    'cars'=> $carDb ?? []
                ]),
                'failed' => array_filter([
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

}
