<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{

    public function sendVerificationEmail(Request $request)
    {
        $user = User::find($request->userId);
        if ($user->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified'
            ];
        }

        $test = $user->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Email already verified'
            ];
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return [
            'message'=>'Email has been verified'
        ];
    }
}