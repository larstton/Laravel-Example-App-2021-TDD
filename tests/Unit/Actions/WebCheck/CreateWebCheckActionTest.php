<?php

namespace Actions\WebCheck;

use App\Actions\WebCheck\CreateWebCheckAction;
use App\Enums\CheckLastSuccess;
use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleOperator;
use App\Events\Rule\RuleCreated;
use App\Events\WebCheck\WebCheckCreated;
use App\Exceptions\WebCheckException;
use App\Models\Rule;
use App\Models\WebCheck;
use App\Support\Preflight\Contract\CheckPreflight;
use Database\Factories\WebCheckDataFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateWebCheckActionTest extends TestCase
{
    private $checkPreflight;

    /** @test */
    public function can_create_web_check_for_host()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = WebCheckDataFactory::make([
            'path'                    => 'path',
            'protocol'                => 'https',
            'port'                    => 80,
            'method'                  => 'GET',
            'expectedPattern'         => null,
            'expectedPatternPresence' => 'present',
            'expectedHttpStatus'      => 200,
            'ignoreSSLErrors'         => false,
            'timeOut'                 => 5.0,
            'dontFollowRedirects'     => false,
            'searchHtmlSource'        => false,
            'checkInterval'           => 60,
            'active'                  => true,
            'preflight'               => false,
            'postData'                => null,
            'headers'                 => null,
            'headersMD5'              => null,
        ]);

        $webCheck = resolve(CreateWebCheckAction::class)->execute($user, $host, $data);

        $webCheck->refresh();

        $this->assertInstanceOf(WebCheck::class, $webCheck);
        $this->assertEquals($host->id, $webCheck->host_id);
        $this->assertEquals($user->id, $webCheck->user_id);
        $this->assertEquals('path', $webCheck->path);
        $this->assertEquals('https', $webCheck->protocol);
        $this->assertEquals(80, $webCheck->port);
        $this->assertEquals('GET', $webCheck->method);
        $this->assertNull($webCheck->expected_pattern);
        $this->assertEquals('present', $webCheck->expected_pattern_presence);
        $this->assertEquals(200, $webCheck->expected_http_status);
        $this->assertFalse($webCheck->ignore_ssl_errors);
        $this->assertEquals(5.0, $webCheck->time_out);
        $this->assertFalse($webCheck->dont_follow_redirects);
        $this->assertFalse($webCheck->search_html_source);
        $this->assertEquals(60, $webCheck->check_interval);
        $this->assertTrue($webCheck->active);
        $this->assertEquals(0, $webCheck->in_progress);
        $this->assertTrue($webCheck->last_success->is(CheckLastSuccess::Pending()));
        $this->assertNull($webCheck->last_message);
        $this->assertNull($webCheck->post_data);
        $this->assertNull($webCheck->headers);
        $this->assertNull($webCheck->headers_md5_sum);
        $this->assertNull($webCheck->last_checked_at);
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

        $data = WebCheckDataFactory::make([
            'path'       => '/',
            'method'     => 'GET',
            'port'       => 80,
            'headersMD5' => $md5,
        ]);

        $this->expectException(WebCheckException::class);

        resolve(CreateWebCheckAction::class)->execute($user, $host, $data);
    }

    /** @test */
    public function will_dispatch_created_event()
    {
        Event::fake([
            WebCheckCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = WebCheckDataFactory::make();

        resolve(CreateWebCheckAction::class)->execute($user, $host, $data);

        Event::assertDispatched(WebCheckCreated::class);
    }

    /** @test */
    public function will_perform_preflight()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = WebCheckDataFactory::make([
            'preflight' => true,
        ]);

        $this->checkPreflight->shouldReceive('webCheck', [$host, $data])
            ->andReturnTrue();

        resolve(CreateWebCheckAction::class)->execute($user, $host, $data);
    }

    /** @test */
    public function will_make_http_performance_rule()
    {
        Carbon::setTestNow($now = now());

        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = WebCheckDataFactory::make();

        resolve(CreateWebCheckAction::class)->execute($user, $host, $data);

        $this->assertDatabaseHas('rules', [
            'team_id'       => $user->team_id,
            'action'        => RuleAction::Warn(),
            'check_type'    => RuleCheckType::WebCheck(),
            'function'      => RuleFunction::Average(),
            'operator'      => RuleOperator::GreaterThan(),
            'check_key'     => 'http.*.performance_s',
            'threshold'     => 20,
            'results_range' => 5,
            'updated_at'    => $now,
            'created_at'    => $now,
        ]);

        Event::assertDispatched(RuleCreated::class);
    }

    /** @test */
    public function wont_create_rule_if_it_exists()
    {
        Event::fake([
            RuleCreated::class,
        ]);

        $team = $this->createTeam();
        $user = $this->createUser($team, false);
        $host = $this->createHost($team);
        $data = WebCheckDataFactory::make();

        Event::fakeFor(function () use ($team) {
            tap(
                Rule::factory()->makeHttpPerformanceWarningRule()->for($team)->make()
            )->calculateChecksum()->save();
        });

        resolve(CreateWebCheckAction::class)->execute($user, $host, $data);

        Event::assertNotDispatched(RuleCreated::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkPreflight = $this->mock(CheckPreflight::class)->shouldIgnoreMissing();
    }
}
