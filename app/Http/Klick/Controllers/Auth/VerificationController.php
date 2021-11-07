<?php

namespace App\Http\Klick\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    use VerifiesEmails {
        verify as baseVerify;
    }

    public function __invoke(Request $request, User $user)
    {
        $request->setUserResolver(fn () => $user);

        return $this->baseVerify($request);
    }

    public function redirectTo()
    {
        return route('web.register.email.verified.fail');
    }

    public function verified(Request $request)
    {
        //@TODO pass UTM-parameters further
        $utms = collect($request->all())->filter(function ($value, $key){
            return Str::of($key)->startsWith('utm');
        });

        if ($request->user()->team->partner === 'plesk') {
            return redirect(
                URL::signedRoute('klick.register.email.success.plesk', $utms->all() + [
                    'user' => $request->user()->id,
                ])
            );
        }

        return redirect(route('web.register.email.verified.success', $utms->all() + [
            'email'   => urlencode($request->user()->email),
        ]));
    }
}
