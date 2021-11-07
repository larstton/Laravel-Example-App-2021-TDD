<?php /** @noinspection PhpIncompatibleReturnTypeInspection */

namespace App\Actions\Frontman;

use App\Exceptions\FrontmanException;
use App\Models\Frontman;
use App\Models\User;
use Illuminate\Support\Str;

class CreateFrontmanAction
{
    public function execute(User $user, $location): Frontman
    {
        throw_if(
            $user->team->frontmen()->count() >= $user->team->max_frontmen,
            FrontmanException::maximumFrontmenReached($user->team)
        );

        return $user->team->frontmen()->create([
            'location' => $location,
            'password' => Str::random(12),
            'user_id'  => $user->id,
        ]);
    }
}
