<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\SendRecipientEmailAddressConfirmationAction;
use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use App\Notifications\RecipientEmailAddressConfirmationNotification;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendRecipientEmailAddressConfirmationActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_send_email_address_confirmation_notification()
    {
        Notification::fake();

        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'media_type' => RecipientMediaType::Email(),
        ]);

        resolve(SendRecipientEmailAddressConfirmationAction::class)->execute($recipient);

        Notification::assertSentTo($recipient, RecipientEmailAddressConfirmationNotification::class);
    }

    /** @test */
    public function wont_send_if_not_email_type()
    {
        Notification::fake();

        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'media_type' => RecipientMediaType::Phonecall(),
        ]);

        resolve(SendRecipientEmailAddressConfirmationAction::class)->execute($recipient);

        Notification::assertNotSentTo(
            $recipient,
            RecipientEmailAddressConfirmationNotification::class
        );
    }
}
