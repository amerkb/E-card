<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\user\VerifiedEmail\CreateCodeRequest;
use App\Http\Requests\user\VerifiedEmail\VerifiedCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Email\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();
        if ($user && $user->email) {
            return response([
                'msg' => 'enter email',
            ], 422);
        }
        if (! $user) {
            $user = User::where('email', $request->username)->first();
        }
        if (! $user || md5($request->password) != $user->password) {
            return response([
                'msg' => 'Invalid username or password',
            ], 401);
        }
        $token = $user->createToken('apiToken')->plainTextToken;
        $res = [
            'user' => new UserResource($user),
            'token' => $token,
        ];

        return response($res, 201);

        //        return response()->json(['message' => Debugbar::getData()["queries"]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function create_code(CreateCodeRequest $request)
    {
        // Retrieve the user based on the email provided in the request

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            $user = User::where('username', $request->email)->first();
        }

        // Check if the user exists
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404); //
        }

        // Generate a random code
        $code = mt_rand(10000, 99999);

        // Update the user with the new code
        $user->update(['code' => $code]);

        // Send the email with the code
        EmailService::sendHtmlEmail($code, $request->email);

        return response()->json(['message' => 'Code sent']);
    }

    public function check_code(VerifiedCodeRequest $request)
    {
        // Retrieve the user based on the email and code provided in the request
        $user = User::where('email', $request->email)->where('code', $request->code)->first();
        if (! $user) {
            $user = User::where('username', $request->username)->where('code', $request->code)->first();
        }
        if ($user) {
            $user->update(['email_verified_at' => Carbon::now()]);

            return response()->json(['message' => true]);
        } else {
            return response()->json(['message' => false], 404);
        }
    }

    public function check(ChangeEmailRequest $request)
    {

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function change_email(ChangeEmailRequest $request)
    {
        $user = User::where('id', Auth::id())->whereNotNull('email_verified_at')->first();
        $user->update(['email' => $request->email, 'email_verified_at' => null]);

        return response(['message' => 'success']);
    }

    public function change_password(ChangePasswordRequest $request)
    {
        // Retrieve the user based on the email provided in the request
        $user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update the user's password - make sure to hash it
        $user->update(['password' => md5($request->password), 'email_verified_at' => null]);

        return response()->json(['message' => 'Password changed successfully']);
    }
}
