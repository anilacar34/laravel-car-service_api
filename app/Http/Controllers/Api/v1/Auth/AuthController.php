<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends BaseController
{
    use HasApiTokens;

    public function createUser(Request $request){
        $status = false;
        $message = 'Failed, user registration unsuccessfully !';
        $responseCode = 400;
        $tokenData = [];

        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'email'      => 'required|email|unique:users',
            'password'   => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            $validationErrors = $validator->errors();
        }else{
            $requestBody = $request->all();
            $requestBody['password'] = bcrypt($requestBody['password']);
            $user = User::create([
                'name'  => $requestBody['name'],
                'email'  => $requestBody['email'],
                'password'  => $requestBody['password'],
            ]);

            $userToken = $user->createToken('CarServiceAppV1');

            $tokenData[] = [
                'user'  => $user->name,
                'token' => [
                    'access_token' => $userToken->plainTextToken,
                    'expires_in'   => 3600,
                    'token_type'   => 'Bearer',
                    'created_at'   => $userToken->accessToken->created_at,
                ],
                'app'   => $userToken->accessToken->name
            ];

            $message = 'User registration successfully !';
            $status = true;
            $responseCode = 200;
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => $tokenData,
                'failed'  => array_filter([
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

    public function login(Request $request){

        $status = false;
        $message = 'Failed, Token can not be retrieved!';
        $responseCode = 400;
        $tokenData = [];

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            $validationErrors = $validator->errors();
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $userToken = $user->createToken('CarServiceAppV1');

            $tokenData = [
                'user'  => $user->name,
                'token' => [
                    'access_token' => $userToken->plainTextToken,
                    'expires_in'   => 3600,
                    'token_type'   => 'Bearer',
                    'created_at'   => $userToken->accessToken->created_at,
                ],
                'app'   => $userToken->accessToken->name
            ];

            $message = 'Token successfully retrieved!';
            $status = true;
            $responseCode = 200;
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => array_filter([
                'success' => $tokenData,
                'failed'  => array_filter([
                    'payload_error' => $validationErrors ?? [],
                ])
            ])
        ];

        return response()->json($result, $responseCode, []);
    }

    public function logout(Request $request){
        $status = false;
        $message = 'Failed, Token can not be revoked!';
        $responseCode = 400;

        $revokeToken = $request->user()->currentAccessToken()->delete();

        if($revokeToken){
            $message = 'Token successfully revoked!';
            $status = true;
            $responseCode = 200;
        }

        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => []
        ];

        return response()->json($result, $responseCode, []);
    }
}
