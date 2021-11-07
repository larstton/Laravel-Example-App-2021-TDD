<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\UpdateRecipientAction;
use App\Enums\RecipientMediaType;
use App\Events\Recipient\RecipientUpdated;
use App\Exceptions\RecipientException;
use App\Models\Recipient;
use Database\Factories\RecipientDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateRecipientActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_create_new_recipient()
    {
        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'media_type'        => RecipientMediaType::Email(),
            'sendto'            => $email = $this->faker->unique()->email,
            'description'       => $this->faker->sentence(),
            'option1'           => 'option1',
            'reminder_delay'    => 600,
            'maximum_reminders' => 3,
            'reminders'         => false,
            'daily_reports'     => false,
            'monthly_reports'   => false,
            'daily_summary'     => false,
            'weekly_reports'    => false,
            'comments'          => false,
            'alerts'            => false,
            'warnings'          => false,
            'event_uuids'       => false,
            'recoveries'        => false,
            'rules'             => null,
            'extra_data'        => null,
        ]);
        $recipientData = RecipientDataFactory::make([
            'mediatype'        => RecipientMediaType::Email(),
            'sendto'           => $email,
            'option1'          => 'new option1',
            'description'      => 'new description',
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
        ]);

        $recipient = resolve(UpdateRecipientAction::class)->execute($recipient, $recipientData);

        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertTrue($recipient->media_type->is(RecipientMediaType::Email()));
        $this->assertEquals($email, $recipient->sendto);
        $this->assertEquals('new option1', $recipient->option1);
        $this->assertEquals('new description', $recipient->description);
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
    }

    /** @test */
    public function will_throw_exception_if_changing_email()
    {
        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'media_type' => RecipientMediaType::Email(),
            'sendto'     => $this->faker->unique()->email,
        ]);
        $recipientData = RecipientDataFactory::make([
            'mediatype' => RecipientMediaType::Email(),
            'sendto'    => $this->faker->unique()->email,
        ]);

        $this->expectException(RecipientException::class);
        $this->expectExceptionMessage('Editing e-mail address is forbidden');

        resolve(UpdateRecipientAction::class)->execute($recipient, $recipientData);
    }

    /** @test */
    public function will_dispatch_update_event()
    {
        Event::fake([
            RecipientUpdated::class,
        ]);

        $team = $this->createTeam();
        $recipient = Recipient::factory()->for($team)->create([
            'media_type' => RecipientMediaType::Email(),
            'sendto'     => $email = $this->faker->unique()->email,
        ]);
        $recipientData = RecipientDataFactory::make([
            'mediatype' => RecipientMediaType::Email(),
            'sendto'    => $email,
        ]);

        resolve(UpdateRecipientAction::class)->execute($recipient, $recipientData);

        Event::assertDispatched(RecipientUpdated::class);
    }
}
