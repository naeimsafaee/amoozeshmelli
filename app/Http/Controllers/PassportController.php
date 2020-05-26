<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class PassportController extends Controller{

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|iran_mobile|unique:users,phone',
            'grade_id' => 'required|exists:grades,id',
            'city_id' => 'required|exists:cities,id',
        ], [
            "phone.required" => "phone is required!",
            "phone.unique" => "phone already exists!",
            "grade_id.required" => "grade_id is required!",
            "city_id.required" => "city_id is required!",
            "grade_id.exists" => "grade_id does not exist!",
            "city_id.exists" => "city_id  does not exist!",
            "phone.iran_mobile" => "phone number is not valid!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $code = rand(1000, 9999);

        User::updateOrcreate([
            "phone" => $request->phone,
            "grade_id" => $request->grade_id,
            "city_id" => $request->city_id,
            "code" => $code,
        ]);

        return response()->json(["success" => ["message" => "sms code has been sent!", "code" => $code]], 200);
    }

    public function verify_sms(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required|iran_mobile|exists:users,phone',
            'code' => 'required',
            'is_app' => 'integer',
        ], [
            "phone.required" => "phone is required!",
            "code.required" => "code is required!",
            "phone.iran_mobile" => "phone number is not valid!",
            "phone.exists" => "phone number does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $user = User::where("phone", $request->phone)->first();

        if($request->code == $user->code){

            if($request->has("is_app")){
                $token = $user->createToken('TutsForbApp', ["user"])->accessToken;
            } else {
                $token = $user->createToken('TutsForWeb', ["user"])->accessToken;
            }

            $user->remember_token = $token;
            $user->save();
            return response()->json(["token" => $token], 200);
        }

        return response()->json(["error" => ["message" => "code is't valid!"]], 401);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required|iran_mobile',
        ], [
            "phone.required" => "phone is required!",
            "phone.iran_mobile" => "phone number is not valid!",
            "phone.exists" => "phone number does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $code = rand(1000, 9999);

        $user = User::where("phone", $request->phone);
        if($user == null){
            $user = User::updateOrcreate([
                "phone" => $request->phone,
                "code" => $code,
            ]);
            return response()->json(["code" => $code , "is_register" => 1], 200);
        } else {
            $user = $user->first();

            $user->code = $code;
            $user->save();
            return response()->json(["code" => $code , "is_register" => 0], 200);
        }

    }

    public function login_admin(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone',
            'password' => 'required',
        ], [
            "phone.required" => "phone is required!",
            "password.required" => "password is required!",
            "phone.exists" => "phone does not exist!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $user = User::where(["phone" => $request->phone, "is_admin" => 1])->first();

        if(!isset($user))
            return response()->json(["error" => ["message" => "admin not found!"]], 404);

        if(!Hash::check(request()->password, $user->password))
            return response()->json(["error" => ["message" => "password is wrong!"]], 403);

        $token = $user->createToken('Admin', ['admin'])->accessToken;
        $user->remember_token = $token;
        $user->save();
        return response()->json(["token" => $token], 200);
    }

    public function add_admin(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:users,phone',
            'password' => 'required',
        ], [
            "phone.required" => "phone is required!",
            "password.required" => "password is required!",
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }


        User::updateOrcreate([
            "phone" => $request->phone,
            "password" => Hash::make($request->password),
            "is_admin" => 1,
        ]);

        return response()->json(["success" => ["message" => "new admin added!"]], 200);
    }

    public function check_token(Request $request){
        return true;
    }

}
