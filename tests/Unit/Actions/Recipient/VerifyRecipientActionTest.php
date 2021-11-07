<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\VerifyRecipientAction;
use App\Events\Recipient\RecipientUpdated;
use App\Events\Recipient\RecipientVerified;
use App\Models\Recipient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class VerifyRecipientActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_verify_recipient()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam([], false);
        $token = Str::random(8);
        $recipient = Recipient::factory()->for($team)->create([
            'verification_token'        => $token,
            'verified'                  => false,
            'active'                    => false,
            'administratively_disabled' => true,
            'verified_at'               => null,
        ]);

        $recipient = resolve(VerifyRecipientAction::class)->execute($recipient, sha1($token));

        $this->assertTrue($team->isCurrentTenant());
        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertTrue($recipient->verified);
        $this->assertTrue($recipient->active);
        $this->assertFalse($recipient->administratively_disabled);
        $this->assertDateTimesMatch($now, $recipient->verified_at);
    }

    /** @test */
    public function will_dispatch_update_event()
    {
        Event::fake([
            RecipientUpdated::class,
            RecipientVerified::class,
        ]);

        $team = $this->createTeam([], false);
        $token = Str::random(8);
        $recipient = Recipient::factory()->for($team)->create([
            'verification_token'        => $token,
            'verified'                  => false,
            'active'                    => false,
            'administratively_disabled' => true,
            'verified_at'               => null,
        ]);

        resolve(VerifyRecipientAction::class)->execute($recipient, sha1($token));

        Event::assertDispatched(RecipientUpdated::class);
        Event::assertDispatched(RecipientVerified::class);
    }

    /** @test */
    public function will_throw_exception_if_tokens_dont_match()
    {
        $team = $this->createTeam([], false);
        $token = Str::random(8);
        $recipient = Recipient::factory()->for($team)->create([
            'verification_token'        => $token,
            'verified'                  => false,
            'active'                    => false,
            'administratively_disabled' => true,
            'verified_at'               => null,
        ]);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('This action is unauthorized.');

        resolve(VerifyRecipientAction::class)->execute($recipient, sha1(Str::random(8)));
    }
}
