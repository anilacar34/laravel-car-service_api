<?php

namespace App\Http\Controllers\Api\v1\Order;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Resources\CarModelYearResource;
use App\Http\Resources\OrderResource;
use App\Models\Car;
use App\Models\CarModelYear;
use App\Models\CarService;
use App\Models\Order;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    public function getAll(Request $request){
        $status = false;
        $message = 'Failed, order(s) not found !';
        $responseCode = 400;

        $requestBody = $request->query();

        $validator = Validator::make($requestBody,
            [
                'order_ids'=> ['regex:/^(\d{1,20})(,\d{1,20})*$/'],
                'status'         => 'in:ongoing,completed,cancel',
                'limit'          => 'numeric|max:100',
                'page'           => 'numeric|int',
            ]
        );

        $page = (int) ($requestBody['page'] ?? 1);
        $limit = (int) ($requestBody['limit'] ?? 100);

        if ($validator->fails()) {
            $validationErrors = $validator->errors();
        } else {

            $order = Order::with(['carModel','carService','transaction','carModel.car','carModel.car.brand'])
                ->where(['created_by' => auth()->user()->id]);

            if ($requestBody['order_ids'] ?? null) {
                $order->whereIn('id',explode(',', $requestBody['order_ids']));
            }

            if(!empty($requestBody['status'])){
                $order->where('status',$requestBody['status']);
            }

            $order = $order->paginate($limit,page:$page);
            $order = OrderResource::collection($order);

            $message = 'Success, order(s) retrieved !';
            $status = true;
            $responseCode = 200;
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'paginate' => array_filter([
                        'limit'      => ($order ?? []) ? $limit : 0,
                        'page'       => ($order ?? []) ? $page : 0,
                        'page_count' => ($order ?? []) ? $order->count() : 0
                    ]),
                    'orders'=> $order ?? []
                ]),
                'failed' => array_filter([
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

    public function create(Request $request){
        $status = false;
        $message = 'Failed, place order !';
        $responseCode = 400;
        $validationErrors = [];

        $validator = Validator::make($request->all(), [//todo redis
//            'car_service_id' => 'required|numeric|exists:App\Models\CarService,id',
//            'car_id'         => 'required|numeric|exists:App\Models\Car,id',
//            'car_model_year' => ['numeric','int','max:'.Carbon::now()->year, 'exists:App\Models\CarModelYear,year,car_id,'.($request->all()['car_id'] ?? null)],

            'car_service_id' => 'required|numeric',
            'car_id'         => 'required|numeric',
            'car_model_year' => ['numeric','int','max:'.Carbon::now()->year],
        ]);

        if($validator->fails()){
            $validationErrors = $validator->errors();
        }else{
            $requestBody = $request->all();

            $carService = CarService::find($requestBody['car_service_id'])->first();
            if(!$carService){
                $validationErrors['car_service_id'] = 'The '.$requestBody['car_service_id'].' has not found.';
            }

            $car = Car::find($requestBody['car_id'])->first();
            if(!$car){
                $validationErrors['car_id'] = 'The '.$requestBody['car_id'].' has not found.';
            }

            $carModelYear = CarModelYear::where(['car_id'=>$requestBody['car_service_id'],'year'=>$requestBody['car_model_year']])->first();
            if(!$carModelYear){
                $validationErrors['car_model_year'] = 'The '.$requestBody['car_model_year'].' has not found.';
            }

            if(!$validationErrors){

                DB::beginTransaction();
                try{
                    $payout = $this->payout($carService->price);

                    if($payout['status']){

                        $order = new Order;
                        $order->model_id = $carModelYear->id;
                        $order->service_id = $requestBody['car_service_id'];
                        $order->transaction_id = $payout['transaction_id'];
                        $order->status = 'ongoing';
                        $order->is_paid = 1;
                        $order->save();

                        if($order->id){
                            $status = true;
                            $message = 'Success, placed order !';
                            $responseCode = 200;
                        }
                    }else{
                        $status = false;
                        $message = $payout['message'];
                        $responseCode = 400;
                    }
                    DB::commit();
                }catch (\Exception $e) {
                    DB::rollback();
                    return $this->sendInternalError();
                }

            }
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'order_id'     => $order->id ?? null,
                    'order_status' => $order->status ?? null,
                    'amount'       => ($payout['status']) ? $carService->price : '',
                    'service'       => ($payout['status']) ? $carService->name : '',
                ]),
                'failed' => array_filter([
                    'payment_error' => (!$payout['status']) ? $payout['message'] : '',
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

    private function payout($amount){
        $status = false;

        $validator = Validator::make([$amount], [
            'required|gte:1|regex:/^(([0-9]*)(\.([0-9]+))?)$/'
        ]);

        if($validator->fails()){
            $message = 'invalid_amount';
        }else{
            $wallet = Wallet::where(['user_id'=>auth()->user()->id])->first();

            if($wallet){
                if($wallet->balance < $amount){
                    $message = 'insufficient_balance';
                }else{
                    DB::beginTransaction();
                    try{
                        $wallet->balance = $wallet->balance - $amount;
                        $oldBalance = $wallet->getOriginal('balance');
                        $wallet->save();

                        $transactionHistory = new TransactionHistory;
                        $transactionHistory->wallet_id = $wallet->id;
                        $transactionHistory->amount = $amount;
                        $transactionHistory->balance_before = $oldBalance;
                        $transactionHistory->balance_after = $wallet->balance;
                        $transactionHistory->process_type = 'payout';
                        $transactionHistory->save();
                        DB::commit();
                    }catch (\Exception $e) {
                        DB::rollback();
                        return $this->sendInternalError();
                    }
                    $message = 'payout';
                    $status = true;
                }
            }else{
                $message = 'wallet_not_found';
            }
        }

        return [
            'status'         => $status,
            'message'        => $message,
            'transaction_id' => $transactionHistory->id ?? null
        ];

    }

    public function orderComplete(){

        DB::beginTransaction();
        try{
            $order = Order::where(['status'=>'ongoing','is_paid'=>true])
                ->update(['status'=>'completed']);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            return 'error'; // log
        }

        return $order;
    }
}
