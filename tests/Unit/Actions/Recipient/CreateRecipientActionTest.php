<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\CreateRecipientAction;
use App\Enums\RecipientMediaType;
use App\Events\Recipient\RecipientCreated;
use App\Models\Recipient;
use Database\Factories\RecipientDataFactory;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateRecipientActionTest extends TestCase
{
    /** @test */
    public function will_create_new_recipient()
    {
        $user = $this->createUser();
        $recipientData = RecipientDataFactory::make([
            'mediatype'        => RecipientMediaType::Email(),
            'sendto'           => 'fake@email.com',
            'option1'          => 'option1',
            'description'      => 'description',
            'comments'         => true,
            'warnings'         => true,
            'eventUuids'       => true,
            'alerts'           => true,
            'reminders'        => true,
            'recoveries'       => true,
            'active'           => true,
            'dailySummary'     => true,
            'dailyReports'     => true,
            'weeklyReports'    => true,
            'monthlyReports'   => true,
            'reminderDelay'    => 12345,
            'maximumReminders' => 15,
            'rules'            => null,
            'extraData'        => [],
            'verified'         => true,
        ]);

        $recipient = resolve(CreateRecipientAction::class)->execute($user, $recipientData);

        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertEquals($user->id, $recipient->user_id);
        $this->assertEquals($user->team_id, $recipient->team_id);
        $this->assertTrue($recipient->media_type->is(RecipientMediaType::Email()));
        $this->assertEquals('fake@email.com', $recipient->sendto);
        $this->assertEquals('option1', $recipient->option1);
        $this->assertEquals('description', $recipient->description);
        $this->assertTrue($recipient->comments);
        $this->assertTrue($recipient->warnings);
        $this->assertTrue($recipient->event_uuids);
        $this->assertTrue($recipient->alerts);
        $this->assertTrue($recipient->reminders);
        $this->assertTrue($recipient->recoveries);
        $this->assertTrue($recipient->active);
        $this->assertTrue($recipient->daily_summary);
        $this->assertTrue($recipient->daily_reports);
        $this->assertTrue($recipient->weekly_reports);
        $this->assertTrue($recipient->monthly_reports);
        $this->assertEquals(12345, $recipient->reminder_delay);
        $this->assertEquals(15, $recipient->maximum_reminders);
        $this->assertNull($recipient->rules);
        $this->assertEmpty($recipient->extra_data);
        $this->assertTrue($recipient->verified);
        $this->assertNotEmpty($recipient->verification_token);
        $this->assertEquals(0, $recipient->permanent_failures_last_24_h);
    }

    /** @test */
    public function will_return_null_if_recipient_already_exists_with_same_sendto_and_mediatype()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        Recipient::factory()->for($team)->create([
            'media_type' => RecipientMediaType::Email(),
            'sendto'     => $email = $this->faker->email,
        ]);

        $recipientData = RecipientDataFactory::make([
            'mediatype' => RecipientMediaType::Email(),
            'sendto'    => $email,
        ]);

        $recipient = resolve(CreateRecipientAction::class)->execute($user, $recipientData);

        $this->assertNull($recipient);
    }

    /** @test */
    public function will_dispatch_created_event()
    {
        Event::fake([
            RecipientCreated::class,
        ]);

        $user = $this->createUser();
        $recipientData = RecipientDataFactory::make();

        resolve(CreateRecipientAction::class)->execute($user, $recipientData);

        Event::assertDispatched(RecipientCreated::class);
    }
}
