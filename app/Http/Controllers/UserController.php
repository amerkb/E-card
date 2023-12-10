<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ProfilyResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Email\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function show($uuid) {
        $user = User::where('uuid',$uuid)->with(['profile','profile.links','profile.primary','profile.sections'])->first();
        return UserResource::make($user);
        
    }
    public function index() {
        return UserResource::collection(User::all());
    }

    public function store(RegisterRequest $request) {

        $request->validated();
        
        $user = User::create(array_merge($request->except('password'),
            ['password' => bcrypt($request->password)]
        ));
        
        return response(['user' => UserResource::make($user)]);
        
    }

    public function update(EditUserRequest $request , User $user) {
        $this->authorize('update',[User::class , $user]);
            $user->update($request->validated());
            $user->sendEmailVerificationNotification();
        return response([ 'message' => 'check Your Email To verfiy']);
    }


    // public function update(Request $request ,User $user) {
    //     $this->authorize('update',[User::class , $user]);
    //         $recipientEmail = $request->user;
    //         $userCode = Str::uuid();
    //         $userName = $user->userName;
    //         $userEmail = $user->email;
    //         $userPassword = $user->password;
    //         EmailService::sendHtmlEmail($recipientEmail,$userCode,$userName,$userEmail, $userPassword);
    //         $user->update(['code' => $user->userCode]);
    //     return response([ 'message' => 'check Your Email To verfiy']);
    // }


    // public function generate_code(Request $request) {
    //     $code = Str::uuid();

    // }


    // public function generate_code(Request $request,User $user)
    // {
    //     DB::beginTransaction();
    //     // $user = User::where('email', $request['email'])->first();
    //     try {
    //     // $user->update([
    //     // 'email_verified_at' => null,
    //     // ]);
    //     $this->generatecode();
    //     $userCode = $user->code;
    //     $userEmail = $user->email;
    //     $userPassword = $user->reset_password;
    //     $recipientEmail = $userEmail;
    //     $userName = $user->userName;
    //     EmailService::sendHtmlEmail($recipientEmail,$userCode,$userName,$userEmail, $userPassword);
    //     $user->update([
    //         'code' => $user->code
    //     ]);
    //     DB::commit();

    //     return response()->json(['message' => "Code Generated Successfully"], 200);
    //     } catch (\Exception $e) {
    //     DB::rollback();
    //     return response()->json(['message' => $e->getMessage(), $e->getCode()]);
    //     }
    // }

    public function destroy(User $user) {
        $user->delete();
        return response()->json(['message' => 'Successfully Deleted'] ,200);
    }

}
