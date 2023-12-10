<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeCodeRequest;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Email\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Type\Integer;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->orWhere('email', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
               'msg' => 'Invalid username or password'
            ], 401);
        }
        $token = $user->createToken('apiToken')->plainTextToken;
        $res = [
            'user' => new UserResource($user),
            'token' => $token
        ];

        return response($res, 201);
    }
    public function logout(Request $request) {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out'] ,200);
    }
    public function create_code(ChangeEmailRequest $request){
       $user= User::find(Auth::id());
        $code =mt_rand(10000, 99999);
       $user->update(["code"=>$code]);
       EmailService::sendHtmlEmail($code,$request->email);
       return response(["message"=>"send code"]);
    }
    public function check_code(ChangeCodeRequest $request){
        $user= User::where("id",Auth::id())->where("code",$request->code)->first();
        if($user)
        {
            return response(["message"=>true]);
        }
        elseif (!$user)
        {
            return response(["message"=>false],404);
        }

    }
    public function change_email(ChangeEmailRequest $request){
        $user= User::find(Auth::id());
        $user->update(["email"=>$request->email]);
        return response(["message"=>"success"]);
    }


}
