<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\ActivateTeamAdminRecipientAction;
use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ActivateTeamAdminRecipientActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_activate_email_recipient_of_user()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $recipient = Recipient::factory()->for($team)->create([
            'sendto'                    => $user->email,
            'media_type'                => RecipientMediaType::Email(),
            'verified'                  => false,
            'active'                    => false,
            'administratively_disabled' => true,
            'verified_at'               => null,
        ]);

        resolve(ActivateTeamAdminRecipientAction::class)->execute($user);

        $recipient->refresh();

        $this->assertTrue($recipient->verified);
        $this->assertTrue($recipient->active);
        $this->assertFalse($recipient->administratively_disabled);
        $this->assertDateTimesMatch($now, $recipient->updated_at);
    }

    /** @test */
    public function will_do_nothing_if_user_has_no_email_recipient()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);
        $recipient = Recipient::factory()->for($team)->create([
            'sendto'                    => '01234567899',
            'media_type'                => RecipientMediaType::Sms(),
            'verified'                  => false,
            'active'                    => false,
            'administratively_disabled' => true,
            'verified_at'               => null,
        ]);

        resolve(ActivateTeamAdminRecipientAction::class)->execute($user);

        $recipient->refresh();

        $this->assertFalse($recipient->verified);
        $this->assertFalse($recipient->active);
        $this->assertTrue($recipient->administratively_disabled);
    }
}
