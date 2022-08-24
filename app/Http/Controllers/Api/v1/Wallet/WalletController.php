<?php

namespace App\Http\Controllers\Api\v1\Wallet;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Resources\TransactionHistoryResource;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends BaseController
{
    public function addBalance(Request $request){
        $status = false;
        $message = 'Failed, user balance increase process unsuccessfully !';
        $responseCode = 400;

        $validator = Validator::make($request->all(), [
            'balance'      => 'required|gte:1|regex:/^(([0-9]*)(\.([0-9]+))?)$/',
        ]);

        if($validator->fails()){
            $validationErrors = $validator->errors();
        }else{
            $requestBody = $request->all();

            $wallet = Wallet::where(['user_id'=>auth()->user()->id])->first();

            if($wallet){

                DB::beginTransaction();
                try{
                    $wallet->balance = $wallet->balance + $requestBody['balance'];
                    $oldBalance = $wallet->getOriginal('balance');
                    $wallet->save();

                    $transactionHistory = new TransactionHistory;
                    $transactionHistory->wallet_id = $wallet->id;
                    $transactionHistory->amount = $requestBody['balance'];
                    $transactionHistory->balance_before = $oldBalance;
                    $transactionHistory->balance_after = $wallet->balance;
                    $transactionHistory->process_type = 'add_balance';
                    $transactionHistory->save();
                    DB::commit();
                }catch (\Exception $e) {
                    DB::rollback();
                    return $this->sendInternalError();
                }

                $message = 'User balance increase process successfully !';
                $status = true;
                $responseCode = 200;
            }else{
                $walletError = 'You dont have a wallet or holdings in your account to perform a transaction!';
            }
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'balance' => $wallet->balance ?? null,
                    'currency'=> $wallet->currency ?? null
                ]),
                'failed' => array_filter([
                    'wallet_error'  => $walletError ?? '',
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

    public function getBalance(){
        $status = false;
        $message = 'Failed, balance not retrieved !';
        $responseCode = 400;

        $wallet = Wallet::where(['user_id'=>auth()->user()->id])->first();

        if($wallet){
            $status = true;
            $message = 'Success, balance successfuly retrieved !';
            $responseCode = 200;
        }else{
            $walletError = 'You dont have a wallet or holdings in your account to perform a transaction!';
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'balance' => $wallet->balance ?? null,
                    'currency'=> $wallet->currency ?? null
                ]),
                'failed'  => array_filter([
                    'transaction_error' => $walletError ?? '',
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

    public function getBalanceHistory(Request $request){
        $status = false;
        $message = 'Failed, transaction(s) not found !';
        $responseCode = 400;

        $requestBody = $request->query();

        $validator = Validator::make($requestBody,
            [
                'transaction_ids'=> ['regex:/^(\d{1,20})(,\d{1,20})*$/'],
                'limit'          => 'numeric|max:100',
                'page'           => 'numeric|int',
                'type'           => 'in:add_balance,payout,cancel'
            ]
        );

        $page = (int) ($requestBody['page'] ?? 1);
        $limit = (int) ($requestBody['limit'] ?? 100);

        if ($validator->fails()) {
            $validationErrors = $validator->errors();
        } else {
            $transactionHistory = TransactionHistory::whereHas('wallet',function (Builder $query){
                $query->where(['user_id' => auth()->user()->id]);
            });

            if ($requestBody['transaction_ids'] ?? null) {
                $transactionHistory->whereIn('id',explode(',', $requestBody['transaction_ids']));
            }

            if(!empty($requestBody['type'])){
                $transactionHistory->where('process_type',$requestBody['type']);
            }

            $transactionHistory = $transactionHistory->paginate($limit,page:$page);
            $transactionHistory = TransactionHistoryResource::collection($transactionHistory);

            $message = 'Success, transaction(s) retrieved !';
            $status = true;
            $responseCode = 200;
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'paginate' => array_filter([
                        'limit'      => ($transactionHistory ?? []) ? $limit : 0,
                        'page'       => ($transactionHistory ?? []) ? $page : 0,
                        'page_count' => ($transactionHistory ?? []) ? $transactionHistory->count() : 0
                    ]),
                    'transactions'=> $transactionHistory ?? []
                ]),
                'failed' => array_filter([
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

    public function payout(Request $request){
        $status = false;
        $message = 'Failed, user balance decrease process unsuccessfully !';
        $responseCode = 400;

        $validator = Validator::make($request->all(), [
            'amount'    => 'required|gte:1|regex:/^(([0-9]*)(\.([0-9]+))?)$/',
        ]);

        if($validator->fails()){
            $validationErrors = $validator->errors();
        }else{
            $requestBody = $request->all();

            $wallet = Wallet::where(['user_id'=>auth()->user()->id])->first();

            if($wallet){
                if($wallet->balance < $requestBody['amount']){
                    $paymentError = 'insufficient balance !';
                }else{

                    DB::beginTransaction();
                    try{
                        $wallet->balance = $wallet->balance - $requestBody['amount'];
                        $oldBalance = $wallet->getOriginal('balance');
                        $wallet->save();

                        $transactionHistory = new TransactionHistory;
                        $transactionHistory->wallet_id = $wallet->id;
                        $transactionHistory->amount = $requestBody['amount'];
                        $transactionHistory->balance_before = $oldBalance;
                        $transactionHistory->balance_after = $wallet->balance;
                        $transactionHistory->process_type = 'payout';
                        $transactionHistory->save();
                        DB::commit();
                    }catch (\Exception $e) {
                        DB::rollback();
                        return $this->sendInternalError();
                    }

                    $message = 'User balance decrease process successfully !';
                    $status = true;
                    $responseCode = 200;
                }
            }else{
                $walletError = 'You dont have a wallet or holdings in your account to perform a transaction!';
            }
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => array_filter([
                    'balance' => $wallet->balance ?? null,
                    'currency'=> $wallet->currency ?? null
                ]),
                'failed' => array_filter([
                    'payment_error' => $paymentError ?? '',
                    'wallet_error'  => $walletError ?? '',
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }
}
