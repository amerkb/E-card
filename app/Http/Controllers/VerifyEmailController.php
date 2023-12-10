<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Response;

class VerifyEmailController extends Controller
{
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return response()->json(['message' => "Verified successfully"], 200);
    }

}
