<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Transformers\Json;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;

class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );
        if ($request->wantsJson()) {
            if ($response == Password::PASSWORD_RESET) {
                return response()->json(['data'=>trans('passwords.reset')]);
            } else {
                return response()->json(['email' => $request->input('email'), 'data'=>trans($response)]);
            }
        }
        $response == Password::PASSWORD_RESET
        ? $this->sendResetResponse($response)
        : $this->sendResetFailedResponse($request, $response);

        return  new RedirectResponse(env("yourdomain")+"?verified=$response");

    }
}
