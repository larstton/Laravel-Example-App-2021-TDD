<?php

namespace App\Actions\User;

use App\Models\User;

class ToggleSubscriptionAction
{
    public function execute(User $user): User
    {
        $user->team->makeCurrentTenant();
        $user->update([
            'product_news' => ! $user->product_news,
        ]);

        activity()
            ->on($user)
            ->causedBy($user)
            ->tap(fn ($activity) => $activity->team_id = $user->team_id)
            ->log(
                $user->product_news
                    ? 'Subscribed to marketing e-mails.'
                    : 'Unsubscribed from marketing e-mails.'
            );

        return $user;
    }
}
