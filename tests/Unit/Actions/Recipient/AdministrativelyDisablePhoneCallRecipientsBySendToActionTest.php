<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\AdministrativelyDisablePhoneCallRecipientsBySendToAction;
use App\Enums\RecipientMediaType;
use App\Events\Recipient\RecipientAdministrativelyDisabled;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AdministrativelyDisablePhoneCallRecipientsBySendToActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_disable_recipients()
    {
        $team = $this->createTeam();
        $sendTo = $this->faker->phoneNumber;
        $recipients = Recipient::factory()->for($team)->count(2)->create([
            'sendto'                    => $sendTo,
            'media_type'                => RecipientMediaType::Phonecall(),
            'active'                    => true,
            'administratively_disabled' => false,
        ]);

        resolve(AdministrativelyDisablePhoneCallRecipientsBySendToAction::class)->execute($sendTo);

        $recipients->each->refresh();

        $this->assertTrue($team->isCurrentTenant());
        $this->assertFalse($recipients[0]->active);
        $this->assertFalse($recipients[1]->active);
        $this->assertTrue($recipients[0]->administratively_disabled);
        $this->assertTrue($recipients[1]->administratively_disabled);
    }

    /** @test */
    public function wont_disable_recipients_with_different_sendto()
    {
        $team = $this->createTeam();
        $sendTo = $this->faker->phoneNumber;
        $recipient1 = Recipient::factory()->for($team)->create([
            'sendto'                    => $sendTo,
            'media_type'                => RecipientMediaType::Phonecall(),
            'active'                    => true,
            'administratively_disabled' => false,
        ]);
        $recipient2 = Recipient::factory()->for($team)->create([
            'sendto'                    => $this->faker->phoneNumber,
            'media_type'                => RecipientMediaType::Phonecall(),
            'active'                    => true,
            'administratively_disabled' => false,
        ]);

        resolve(AdministrativelyDisablePhoneCallRecipientsBySendToAction::class)->execute($sendTo);

        $recipient1->refresh();
        $recipient2->refresh();

        $this->assertFalse($recipient1->active);
        $this->assertTrue($recipient2->active);
        $this->assertTrue($recipient1->administratively_disabled);
        $this->assertFalse($recipient2->administratively_disabled);
    }

    /** @test */
    public function will_dispatch_an_event_for_each_disabled_recipient()
    {
        $team = $this->createTeam();
        $sendTo = $this->faker->phoneNumber;
        Recipient::factory()->for($team)->count(2)->create([
            'sendto'                    => $sendTo,
            'media_type'                => RecipientMediaType::Phonecall(),
            'active'                    => true,
            'administratively_disabled' => false,
        ]);

        resolve(AdministrativelyDisablePhoneCallRecipientsBySendToAction::class)->execute($sendTo);

        Event::assertDispatched(RecipientAdministrativelyDisabled::class, 2);
    }

    /** @test */
    public function will_log_to_activity_for_each_disabled_recipient()
    {
        $team = $this->createTeam();
        $sendTo = $this->faker->phoneNumber;
        $recipient = Recipient::factory()->for($team)->create([
            'sendto'                    => $sendTo,
            'media_type'                => RecipientMediaType::Phonecall(),
            'active'                    => true,
            'administratively_disabled' => false,
        ]);

        resolve(AdministrativelyDisablePhoneCallRecipientsBySendToAction::class)->execute($sendTo);

        $this->assertDatabaseHas('activity_log', [
            'team_id'     => $team->id,
            'causer_id'   => null,
            'description' => sprintf("Recipient \"%s\" administratively disabled", $recipient->sendto),
        ]);
    }
}
