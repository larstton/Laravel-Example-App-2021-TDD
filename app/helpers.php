<?php

use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use CloudRadar\LaravelSettings\LaravelSettings;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

if (! function_exists('current_user')) {
    /**
     * Returns currently authenticated user.
     *
     * @param  string|null  $guard
     * @return Authenticatable|User|null
     */
    function current_user($guard = null): ?Authenticatable
    {
        return auth($guard)->user();
    }
}

if (! function_exists('current_team')) {
    /**
     * Returns the team of the currently authenticated user.
     *
     * @param  string|null  $guard
     * @return Team|null
     */
    function current_team($guard = null): ?Team
    {
        return optional(auth($guard)->user())->team;
    }
}

if (! function_exists('user_settings')) {
    function user_settings(User $user)
    {
        $setting = app('laravel-settings-user');

        /* @var LaravelSettings $setting */
        return $setting->baseKey('user-settings')
            ->forEntity($user->id);
    }
}

if (! function_exists('team_settings')) {
    /**
     * @param  Team  $team
     * @return LaravelSettings
     */
    function team_settings(Team $team)
    {
        $setting = app('laravel-settings-team');

        /* @var LaravelSettings $setting */
        return $setting->baseKey('team-settings')
            ->forEntity($team->id);
    }
}

if (! function_exists('is_cloud_radar_support_email')) {
    function is_cloud_radar_support_email(string $email)
    {
        return preg_match('/\Asupport\+[0-9]+@cloudradar.co\Z/si', $email);
    }
}

if (! function_exists('format_carbon_to_team_format')) {
    function format_carbon_to_team_format(Team $team, Carbon $carbon)
    {
        $format = config('datetime-formats')[$team->date_format];

        return $carbon->format($format['dateFormat'].' '.$format['timeFormat']);
    }
}

if (! function_exists('fail_validation')) {
    /**
     * @param  array|string  $key
     * @param  string|null  $message
     * @throws ValidationException
     */
    function fail_validation($key, ?string $message = null)
    {
        $messages = $key;
        if (is_string($messages)) {
            $messages = [$key => $message];
        }
        throw ValidationException::withMessages($messages);
    }
}

if (! function_exists('str')) {
    /**
     * Get a new stringable object from the given string.
     *
     * @param  string  $string
     * @return \Illuminate\Support\Stringable
     */
    function str(string $string)
    {
        return Str::of($string);
    }
}

if (! function_exists('get_commit_hash')) {
    /**
     * Checks to see if we have a .commit_hash file or .git repo and return the hash if we do.
     *
     * @return null|string
     */
    function get_commit_hash(): ?string
    {
        $commitHash = base_path('.commit_hash');
        if (file_exists($commitHash)) {
            // See if we have the .commit_hash file
            return trim(exec(sprintf('cat %s', $commitHash)));
        } elseif (is_dir(base_path('.git'))) {
            // Do we have a .git repo?
            return trim(exec('git log --pretty="%h" -n1 HEAD'));
        } else {
            // ¯\_(ツ)_/¯
            return null;
        }
    }
}

if (! function_exists('get_previous_commit_hash')) {
    function get_previous_commit_hash(): ?string
    {
        $commitHash = base_path('.commit_hash_previous');
        if (file_exists($commitHash)) {
            // See if we have the .commit_hash_previous file
            return trim(exec(sprintf('cat %s', $commitHash)));
        } else {
            // ¯\_(ツ)_/¯
            return null;
        }
    }
}

