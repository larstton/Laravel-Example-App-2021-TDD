<?php

namespace Tests\Unit\Actions\Recipient;

use App\Actions\Recipient\SendTestMessageAction;
use App\Data\Recipient\TestMessageData;
use App\Enums\RecipientMediaType;
use App\Support\NotifierService;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SendTestMessageActionTest extends TestCase
{
    use WithoutEvents;

    private $notifierService;

    /** @test */
    public function will_send_test_message()
    {
        $this->createTeam();

        $data = new TestMessageData([
            'mediatype' => RecipientMediaType::Email(),
            'sendto'    => $this->faker->email,
            'option1'   => 'option1',
            'message'   => 'message',
            'extraData' => [],
        ]);

        resolve(SendTestMessageAction::class)->execute($data);

        $this->notifierService
            ->shouldReceive('sendTestMessage', [
                'mediatype' => $data->mediatype->value,
                'sendto'    => $data->sendto,
                'option1'   => $data->option1,
                'message'   => $data->message,
                'extraData' => $data->extraData,
            ])
            ->andReturnTrue();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
    }
}
