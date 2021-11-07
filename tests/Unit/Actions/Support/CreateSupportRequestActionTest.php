<?php

namespace Tests\Unit\Actions\Support;

use App\Actions\Support\CreateSupportRequestAction;
use App\Enums\SupportRequestState;
use App\Events\Support\SupportMessageCreated;
use App\Models\SupportRequest;
use Database\Factories\CreateSupportRequestDataFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateSupportRequestActionTest extends TestCase
{
    /** @test */
    public function can_create_new_support_request()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateSupportRequestDataFactory::make([
            'subject'    => '<strong>subject</strong>',
            'body'       => "<strong>body</strong>",
            'attachment' => [],
        ]);

        $supportRequest = resolve(CreateSupportRequestAction::class)->execute($user, $data);

        $this->assertInstanceOf(SupportRequest::class, $supportRequest);
        $this->assertEquals($user->id, $supportRequest->user_id);
        $this->assertEquals($team->id, $supportRequest->team_id);
        $this->assertEquals($user->email, $supportRequest->email);
        $this->assertEquals('subject', $supportRequest->subject);
        $this->assertEquals('body', $supportRequest->body);
        $this->assertTrue($supportRequest->state->is(SupportRequestState::Open()));
        $this->assertNull($supportRequest->attachment);
    }

    /** @test */
    public function will_dispatch_event()
    {
        Event::fake([
            SupportMessageCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateSupportRequestDataFactory::make([
            'subject'    => '<strong>subject</strong>',
            'body'       => "<strong>body</strong>",
            'attachment' => [],
        ]);

        resolve(CreateSupportRequestAction::class)->execute($user, $data);

        Event::assertDispatched(SupportMessageCreated::class);
    }

    /** @test */
    public function will_handle_attachments()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateSupportRequestDataFactory::make([
            'attachment' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
        ]);

        Str::createUuidsUsing(function () {
            return new class {
                static $uuid = 1;

                public function toString()
                {
                    return (string) static::$uuid++;
                }

                public function __toString()
                {
                    return $this->toString();
                }
            };
        });

        Storage::fake('local');

        $supportRequest = resolve(CreateSupportRequestAction::class)->execute($user, $data);

        $this->assertEquals([
            '1' => 'image1.jpg',
            '2' => 'image2.jpg',
        ], $supportRequest->attachment);

        Storage::disk('support_attachments')->assertExists([
            "{$supportRequest->id}/image1.jpg",
            "{$supportRequest->id}/image2.jpg",
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Str::createUuidsNormally();
    }
}
