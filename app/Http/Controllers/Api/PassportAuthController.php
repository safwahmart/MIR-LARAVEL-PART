<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PassportAuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'unique:users'
        ]);

        if ($validator->fails()) {
            return response(['status' => false, 'errors' => $validator->errors()->all()], 422);
        }
        try {
            $otp = rand(100000, 999999);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'otp' => $otp
            ]);
            $phone = $request->phone;
            $token = $user->createToken('Laravel8PassportAuth')->accessToken;
            $msg = 'Your Otp is ' . $otp;
            $key = '4KsPda7iZ5gM17UQYu0fmx09xRgBDc4VFjf0xEUq';
            // $response = Http::withHeaders([
            //     'X-Auth-Token' => $key
            // ])->post('https://api.sms.net.bd/sendsms', [
            //     'to' => '+88' . $phone,
            //     'msg' => $msg,
            //     'check_schedule' => 0,
            // ]);
            // $response = Http::get('https://api.sms.net.bd/sendsms?api_key=' . $key . '&msg=' . $msg . '&to=88' . $request->phone);
            // return $response;
            // redirect()->route('email/verification-notification');
            $response = Http::get('https://api.sms.net.bd/sendsms?api_key=' . $key . '&msg=' . $msg . '&to=88' . $phone);
            return response()->json(['status' => true, 'token' => $token, 'user' => $user, 'response' => $response], 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function forgetPassPhone(Request $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            $otp = rand(100000, 999999);
            $msg = 'Your Otp is ' . $otp;
            $user->update([
                'otp' => $otp
            ]);
            $key = '4KsPda7iZ5gM17UQYu0fmx09xRgBDc4VFjf0xEUq';
            $phone = $request->phone;
            // return 'https://api.sms.net.bd/sendsms?api_key=' . $key . '&msg=' . $msg . '&to=88' . $phone;
            // $response = Http::get('https://api.sms.net.bd/sendsms?api_key=' . $key . '&msg=' . $msg . '&to=88' . $phone);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('api_key' => $key, 'msg' => $msg, 'to' => '88'. $phone),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return response()->json(['status' => true, 'message' => 'OTP is sent', 'user' => $user->id, 'response' => $response]);
        } else {
            return response()->json(['status' => false, 'message' => 'Phone Number is not matched']);
        }
    }
    /**
     * Login Req
     */
    public function checkOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $check = User::where('id', $request->user_id)->where('otp', $request->otp)->first();
        if (isset($check)) {
            $user = User::find($request->user_id);
            $user->update([
                "otp" => "",
                "status" => 1
            ]);
            return response()->json(['status' => true, 'message' => 'OTP is successfully matched']);
        } else {
            return response()->json(['status' => false, 'message' => 'OTP is not matched']);
        }
    }
    public function login(Request $request)
    {
        if ($request->social === false) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|max:255',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails()) {
                return response(['status' => true, 'errors' => $validator->errors()->all()], 422);
            }

            $user = User::where('email', $request->email)->orWhere('phone', $request->email)->first();
            if ($user) {
                $con = '';
                if (is_numeric($request->email)) {
                    $con = ['phone' => $request->email, 'password' => $request->password, 'status' => 1];
                } else {
                    $con = ['email' => $request->email, 'password' => $request->password, 'status' => 1];
                }
                if (Auth::attempt($con)) {
                    // if (Hash::check($request->password, $user->password)) {
                    $user = User::find(Auth::user()->id);
                    $permission = Permission::where('user_id', Auth::user()->id)->get();

                    $user_token = $user->createToken('appToken')->accessToken;
                    $response = ['status' => true, 'token' => $user_token, 'message' => 'Login Successfully', 'user' => $user, 'permissions' => $permission];
                    return response($response, 200);
                } else {
                    $response = ['status' => false, "message" => "Password mismatch"];
                    return response($response, 422);
                }
            } else {
                $response = ['status' => false, "message" => 'User does not exist'];
                return response($response, 422);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return response(['status' => true, 'errors' => $validator->errors()->all()], 422);
            }
            $user = User::where('email', $request->email)->Where('provider_token', $request->token)->where('provider', $request->provider)->first();
            if ($user) {
                $permission = Permission::where('user_id', $user->id)->get();
                $user_token = $user->createToken('appToken')->accessToken;
                $response = ['status' => true, 'token' => $user_token, 'message' => 'Login Successfully', 'user' => $user, 'permissions' => $permission];
                return response($response, 200);
            } else {
                $insert = User::create([
                    'email' => $request->email,
                    'password' => '',
                    'phone' => '',
                    'status' => 1,
                    'name' => $request->name,
                    'provider' => $request->provider,
                    'provider_token' => $request->token,
                ]);
                $permission = Permission::where('user_id', $insert->id)->get();
                $user_token = $insert->createToken('appToken')->accessToken;
                $response = ['status' => true, 'token' => $user_token, 'message' => 'Login Successfully', 'user' => $insert, 'permissions' => $permission];
                return response($response, 200);
            }
        }
    }

    public function userInfo()
    {

        $user = auth()->user();

        return response()->json(['user' => $user], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->delete();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $arr = array("status" => true, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                // if (isset($ex->error[2])) {
                //     $msg = $ex->errorInfo[2];
                // } else {
                $msg = $ex->getMessage();
                // }
                $arr = array("status" => false, "message" => $msg, "data" => array());
            }
        }
        return response()->json($arr, 200);
    }
    public function forgot_password(Request $request)
    {
        $input = $request->all();
        $user = User::find($request->user);
        $rules = array(
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('password'), $user->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id',$user->id)->update(['password' => Hash::make($input['password'])]);
                    $arr = array("status" => true, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                // if (isset($ex->error[2])) {
                //     $msg = $ex->errorInfo[2];
                // } else {
                $msg = $ex->getMessage();
                // }
                $arr = array("status" => false, "message" => $msg, "data" => array());
            }
        }
        return response()->json($arr, 200);
    }
}
