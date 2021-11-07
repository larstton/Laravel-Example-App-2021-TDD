<?php

namespace Actions\WebCheck;

use App\Actions\WebCheck\UpdateWebCheckAction;
use App\Enums\CheckLastSuccess;
use App\Events\WebCheck\WebCheckUpdated;
use App\Exceptions\WebCheckException;
use App\Models\WebCheck;
use App\Support\Preflight\Contract\CheckPreflight;
use Database\Factories\WebCheckDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateWebCheckActionTest extends TestCase
{
    use WithoutEvents;

    private $checkPreflight;

    /** @test */
    public function will_update_web_check_using_supplied_data()
    {
        Carbon::setTestNow($now = now());

        $team = $this->createTeam();
        $user = $this->createUser($team);
        $host = $this->createHost($team);

        $webCheck = WebCheck::factory()->for($host)->for($user)->create([
            'path'                      => '/',
            'protocol'                  => 'https',
            'port'                      => 80,
            'expected_pattern'          => null,
            'expected_pattern_presence' => 'present',
            'expected_http_status'      => 200,
            'search_html_source'        => false,
            'time_out'                  => 5.0,
            'ignore_ssl_errors'         => false,
            'check_interval'            => 60,
            'dont_follow_redirects'     => false,
            'method'                    => 'GET',
            'active'                    => true,
            'in_progress'               => false,
            'last_success'              => CheckLastSuccess::Success(),
            'last_message'              => null,
            'post_data'                 => null,
            'headers'                   => null,
            'headers_md5_sum'           => null,
            'last_checked_at'           => null,
        ]);

        $data = WebCheckDataFactory::make([
            'path'                    => '/path',
            'protocol'                => 'http',
            'port'                    => 81,
            'method'                  => 'POST',
            'expectedPattern'         => 'expectedPattern',
            'expectedPatternPresence' => 'absent',
            'expectedHttpStatus'      => 201,
            'ignoreSSLErrors'         => true,
            'timeOut'                 => 10.0,
            'dontFollowRedirects'     => true,
            'searchHtmlSource'        => true,
            'checkInterval'           => 100,
            'active'                  => false,
            'preflight'               => false,
            'postData'                => 'postData',
            'headers'                 => [],
            'headersMD5'              => $md5 = md5(Str::random()),
        ]);

        $webCheck = resolve(UpdateWebCheckAction::class)->execute($webCheck, $host, $data);

        $webCheck->refresh();

        $this->assertInstanceOf(WebCheck::class, $webCheck);
        $this->assertEquals($host->id, $webCheck->host_id);
        $this->assertEquals($user->id, $webCheck->user_id);
        $this->assertEquals('/path', $webCheck->path);
        $this->assertEquals('http', $webCheck->protocol);
        $this->assertEquals(81, $webCheck->port);
        $this->assertEquals('POST', $webCheck->method);
        $this->assertEquals('expectedPattern', $webCheck->expected_pattern);
        $this->assertEquals('absent', $webCheck->expected_pattern_presence);
        $this->assertEquals(201, $webCheck->expected_http_status);
        $this->assertTrue($webCheck->ignore_ssl_errors);
        $this->assertEquals(10.0, $webCheck->time_out);
        $this->assertTrue($webCheck->dont_follow_redirects);
        $this->assertTrue($webCheck->search_html_source);
        $this->assertEquals(100, $webCheck->check_interval);
        $this->assertFalse($webCheck->active);
        $this->assertEquals(0, $webCheck->in_progress);
        $this->assertTrue($webCheck->last_success->is(CheckLastSuccess::Pending()));
        $this->assertNull($webCheck->last_message);
        $this->assertEquals('postData', $webCheck->post_data);
        $this->assertEquals([], $webCheck->headers);
        $this->assertEquals($md5, $webCheck->headers_md5_sum);
        $this->assertNull($webCheck->last_checked_at);
        $this->assertDateTimesMatch($now, $webCheck->updated_at);
    }

    /** @test */
    public function will_throw_exception_if_webcheck_exists_for_team()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $md5 = md5(Str::random());
        WebCheck::factory()->for($host)->for($user)->create([
            'path'            => '/',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5,
        ]);
        $webCheck = WebCheck::factory()->for($host)->for($user)->create([
            'path'            => '/different',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5,
        ]);

        $data = WebCheckDataFactory::make([
            'path'       => '/',
            'method'     => 'GET',
            'port'       => 80,
            'headersMD5' => $md5,
        ]);

        $this->expectException(WebCheckException::class);

        resolve(UpdateWebCheckAction::class)->execute($webCheck, $host, $data);
    }

    /** @test */
    public function will_dispatch_updated_event()
    {
        Event::fake([
            WebCheckUpdated::class,
        ]);

        $team = $this->createTeam();
        $host = $this->createHost($team);

        $webCheck = WebCheck::factory()->for($host)->create();
        $data = WebCheckDataFactory::make();

        resolve(UpdateWebCheckAction::class)->execute($webCheck, $host, $data);

        Event::assertDispatched(WebCheckUpdated::class);
    }

    /** @test */
    public function will_perform_preflight()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $webCheck = WebCheck::factory()->for($host)->for($user)->create();
        $data = WebCheckDataFactory::make([
            'preflight' => true,
        ]);

        $this->checkPreflight->shouldReceive('webCheck', [$host, $data])
            ->andReturnTrue();

        resolve(UpdateWebCheckAction::class)->execute($webCheck, $host, $data);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkPreflight = $this->mock(CheckPreflight::class)->shouldIgnoreMissing();
    }
}
