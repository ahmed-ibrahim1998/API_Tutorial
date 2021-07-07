<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller
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

            $credentials = $request->only(['email','password']);

            $token = Auth::guard('admin-api')->attempt($credentials);

            if(!$token)
                return $this->returnError('E001','بيانات الدخول غير صحية');


            // return Token JWT


            $admin = Auth::guard('admin-api')->user(); // هيرجع كل البايانات الخاصة بالادمن ده والتوكن معااه
            $admin->api_token = $token;
            return $this->returnData('admin',$admin,'تم جلب البايانات بنجاح');

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function logout (Request $request) {

        $token = $request->header('auth-token');

        if ($token) {

            try {

                JWTAuth::setToken($token)->invalidate(); //logout

            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $ex) {

                return $this->returnError('E001','Some Thing Wrong');
            }

            return $this->returnSuccessMessage('Logged out Successfully');

        } else {
            return $this->returnError('E001','Some Thing Wrong');
        }
    }
}
