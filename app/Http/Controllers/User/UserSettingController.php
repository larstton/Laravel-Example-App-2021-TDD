<?php

namespace App\Http\Controllers\User;

use App\Events\User\UserSettingsUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    public function __invoke(Request $request)
    {
        user_settings($user = current_user())->set($request->settings);
        
        UserSettingsUpdated::dispatch($user, $request->settings);

        return $this->accepted([
            'data' => user_settings($user)->get(),
        ]);
    }
}
