<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthLoginController extends Controller
{
    use GeneralTrait;

    public function login(Request $request)
    {

        try {
            $rules = [
                "email" => "required",
                "password" => "required"
            ];

            $validator = Validator::make($request->all(), $rules);  //$request->all() اللي جاااي من الريكوست اللي هيدخل في البوست مان

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
        // login

            $credentials = $request->only(['email','password']); //generate token

            $token = Auth::guard('user-api')->attempt($credentials);

            if(!$token)
                return $this->returnError('E001','بيانات الدخول غير صحية');


            // return Token JWT


            $user = Auth::guard('user-api')->user(); // هيرجع كل البايانات الخاصة بالادمن ده والتوكن معااه
            $user->api_token = $token;
            return $this->returnData('user',$user,'تم جلب البايانات بنجاح'); //return json response

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function logout (Request $request) {

        $token = $request -> header('auth-token');

        if($token){
            try {

                JWTAuth::setToken($token)->invalidate(); //logout

            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){

                return  $this -> returnError('','some thing went wrongs');

            }

            return $this->returnSuccessMessage('Logged out successfully');

        }else{

            $this -> returnError('','some thing went wrongs');

        }
    }
}
