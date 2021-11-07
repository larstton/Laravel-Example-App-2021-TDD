<?php

namespace Actions\Wizard;

use App\Actions\Host\CreateHostAction;
use App\Actions\WebCheck\CreateWebCheckAction;
use App\Actions\Wizard\CreateWebCheckFromWizardAction;
use App\Data\Host\HostData;
use App\Data\WebCheck\WebCheckData;
use App\Models\Host;
use App\Models\User;
use App\Models\WebCheck;
use App\Support\Validation\FQDN;
use App\Support\Validation\IpAddress;
use Database\Factories\CreateWebCheckWizardDataFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateWebCheckFromWizardActionTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function will_create_host_and_webcheck_when_dont_exist()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'url'       => 'https://cloudradar.io:85/login?query=true',
            'preflight' => false,
        ]);

        /** @var Host $host */
        $host = Event::fakeFor(function () use ($user, $team) {
            return Host::factory()->for($team)->make([
                'name'        => 'cloudradar.io',
                'description' => 'Created by web check wizard',
                'connect'     => 'cloudradar.io',
                'cagent'      => false,
                'dashboard'   => true,
                'muted'       => false,
                'active'      => true,
            ]);
        });
        /** @var WebCheck $webCheck */
        $webCheck = Event::fakeFor(function () use ($host, $user) {
            return WebCheck::factory()->for($user)->for($host)->make();
        });

        $this->mock('alias:'.FQDN::class)
            ->shouldReceive('isValidPublicFQDN')
            ->once()
            ->andReturnTrue();
        $this->mock(CreateHostAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $hostData) use ($user) {
                $this->assertTrue($user->is($_user));
                $this->assertInstanceOf(User::class, $_user);
                $this->assertInstanceOf(HostData::class, $hostData);
                $this->assertEquals('cloudradar.io', $hostData->name);
                $this->assertEquals('Created by web check wizard', $hostData->description);
                $this->assertEquals('cloudradar.io', $hostData->connect);
                $this->assertFalse($hostData->cagent);
                $this->assertTrue($hostData->dashboard);
                $this->assertFalse($hostData->muted);
                $this->assertTrue($hostData->active);

                return true;
            })
            ->andReturn($host);
        $this->mock(CreateWebCheckAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $_host, $webCheckData) use ($host, $user) {
                $this->assertTrue($user->is($_user));
                $this->assertTrue($host->is($_host));
                $this->assertInstanceOf(User::class, $_user);
                $this->assertInstanceOf(Host::class, $_host);
                $this->assertInstanceOf(WebCheckData::class, $webCheckData);
                $this->assertEquals('https', $webCheckData->protocol);
                $this->assertEquals('GET', $webCheckData->method);
                $this->assertEquals('/login?query=true', $webCheckData->path);
                $this->assertEquals(85, $webCheckData->port);
                $this->assertEquals(5.0, $webCheckData->timeOut);
                $this->assertEquals(90, $webCheckData->checkInterval);
                $this->assertEquals(200, $webCheckData->expectedHttpStatus);
                $this->assertTrue($webCheckData->active);
                $this->assertFalse($webCheckData->dontFollowRedirects);
                $this->assertTrue($webCheckData->ignoreSSLErrors);
                $this->assertFalse($webCheckData->searchHtmlSource);
                $this->assertFalse($webCheckData->preflight);

                return true;
            })
            ->andReturns($webCheck);

        $webCheck = resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);

        $this->assertInstanceOf(WebCheck::class, $webCheck);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function wont_make_new_host_if_host_with_connect_exists()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'url'       => 'https://cloudradar.io:85/login?query=true',
            'preflight' => false,
        ]);

        /** @var Host $host */
        $host = Event::fakeFor(function () use ($user, $team) {
            return Host::factory()->for($team)->create([
                'connect' => 'cloudradar.io',
            ]);
        });

        $webCheck = WebCheck::factory()->for($user)->for($host)->make();

        $this->mock('alias:'.FQDN::class)->shouldNotHaveReceived('isValidPublicFQDN');
        $this->mock(CreateHostAction::class)->shouldNotHaveBeenCalled();

        $this->mock(CreateWebCheckAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $_host, $webCheckData) use ($host, $user) {
                $this->assertTrue($user->is($_user));
                $this->assertTrue($host->is($_host));
                $this->assertInstanceOf(User::class, $_user);
                $this->assertInstanceOf(Host::class, $_host);
                $this->assertInstanceOf(WebCheckData::class, $webCheckData);
                $this->assertEquals('https', $webCheckData->protocol);
                $this->assertEquals('GET', $webCheckData->method);
                $this->assertEquals('/login?query=true', $webCheckData->path);
                $this->assertEquals(85, $webCheckData->port);
                $this->assertEquals(5.0, $webCheckData->timeOut);
                $this->assertEquals(90, $webCheckData->checkInterval);
                $this->assertEquals(200, $webCheckData->expectedHttpStatus);
                $this->assertTrue($webCheckData->active);
                $this->assertFalse($webCheckData->dontFollowRedirects);
                $this->assertTrue($webCheckData->ignoreSSLErrors);
                $this->assertFalse($webCheckData->searchHtmlSource);
                $this->assertFalse($webCheckData->preflight);

                return true;
            })
            ->andReturns($webCheck);

        $webCheck = resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);

        $this->assertInstanceOf(WebCheck::class, $webCheck);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function will_throw_exception_if_host_exists_with_webcheck()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'url'       => 'https://cloudradar.io:85/login?query=true',
            'preflight' => false,
        ]);

        Event::fakeFor(function () use ($user, $team) {
            $host = Host::factory()->for($team)->create([
                'connect' => 'cloudradar.io',
            ]);
            WebCheck::factory()->for($user)->for($host)->create([
                'protocol' => 'https',
                'method'   => 'GET',
                'path'     => '/login?query=true',
                'port'     => 85,
            ]);
        });

        $this->mock('alias:'.FQDN::class)->shouldNotHaveReceived('isValidPublicFQDN');
        $this->mock(CreateHostAction::class)->shouldNotHaveBeenCalled();
        $this->mock(CreateWebCheckAction::class)->shouldNotHaveBeenCalled();

        $this->expectException(ValidationException::class);

        resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function can_create_with_ip_as_host_in_url()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'url'       => 'https://8.8.8.8:85/login?query=true',
            'preflight' => false,
        ]);

        /** @var Host $host */
        $host = Event::fakeFor(function () use ($user, $team) {
            return Host::factory()->for($team)->make([
                'name'        => '8.8.8.8',
                'description' => 'Created by web check wizard',
                'connect'     => 'cloudradar.io',
                'cagent'      => false,
                'dashboard'   => true,
                'muted'       => false,
                'active'      => true,
            ]);
        });
        /** @var WebCheck $webCheck */
        $webCheck = Event::fakeFor(function () use ($host, $user) {
            return WebCheck::factory()->for($user)->for($host)->make();
        });

        $this->mock('alias:'.IpAddress::class)
            ->shouldReceive('isValidPublicIP')
            ->once()
            ->andReturnTrue();
        $this->mock(CreateHostAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $hostData) use ($user) {
                $this->assertTrue($user->is($_user));
                $this->assertInstanceOf(User::class, $_user);
                $this->assertInstanceOf(HostData::class, $hostData);
                $this->assertEquals('8.8.8.8', $hostData->name);
                $this->assertEquals('Created by web check wizard', $hostData->description);
                $this->assertEquals('8.8.8.8', $hostData->connect);
                $this->assertFalse($hostData->cagent);
                $this->assertTrue($hostData->dashboard);
                $this->assertFalse($hostData->muted);
                $this->assertTrue($hostData->active);

                return true;
            })
            ->andReturn($host);
        $this->mock(CreateWebCheckAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $_host, $webCheckData) use ($host, $user) {
                $this->assertTrue($user->is($_user));
                $this->assertTrue($host->is($_host));
                $this->assertInstanceOf(User::class, $_user);
                $this->assertInstanceOf(Host::class, $_host);
                $this->assertInstanceOf(WebCheckData::class, $webCheckData);
                $this->assertEquals('https', $webCheckData->protocol);
                $this->assertEquals('GET', $webCheckData->method);
                $this->assertEquals('/login?query=true', $webCheckData->path);
                $this->assertEquals(85, $webCheckData->port);
                $this->assertEquals(5.0, $webCheckData->timeOut);
                $this->assertEquals(90, $webCheckData->checkInterval);
                $this->assertEquals(200, $webCheckData->expectedHttpStatus);
                $this->assertTrue($webCheckData->active);
                $this->assertFalse($webCheckData->dontFollowRedirects);
                $this->assertTrue($webCheckData->ignoreSSLErrors);
                $this->assertFalse($webCheckData->searchHtmlSource);
                $this->assertFalse($webCheckData->preflight);

                return true;
            })
            ->andReturns($webCheck);

        $webCheck = resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);

        $this->assertInstanceOf(WebCheck::class, $webCheck);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function will_pass_preflight_value_through()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'preflight' => true,
        ]);

        $this->mock('alias:'.IpAddress::class)
            ->shouldReceive('isValidPublicIP')
            ->once()
            ->andReturnTrue();
        $this->mock(CreateHostAction::class)
            ->shouldReceive('execute')
            ->withAnyArgs()
            ->andReturns(new Host);
        $this->mock(CreateWebCheckAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $_host, $webCheckData) {
                $this->assertTrue($webCheckData->preflight);

                return $webCheckData->preflight === true;
            })
            ->andReturns(new WebCheck);

        $webCheck = resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);

        $this->assertInstanceOf(WebCheck::class, $webCheck);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function will_default_port_if_http_and_no_port()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'url' => 'http://cloudradar.io',
        ]);

        $this->mock('alias:'.FQDN::class)
            ->shouldReceive('isValidPublicFQDN')
            ->once()
            ->andReturnTrue();
        $this->mock(CreateHostAction::class)
            ->shouldReceive('execute')
            ->withAnyArgs()
            ->andReturns(new Host);
        $this->mock(CreateWebCheckAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $_host, $webCheckData) {
                $this->assertEquals('http', $webCheckData->protocol);
                $this->assertEquals(80, $webCheckData->port);

                return true;
            })
            ->andReturns(new WebCheck);

        $webCheck = resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);

        $this->assertInstanceOf(WebCheck::class, $webCheck);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function will_default_port_if_https_and_no_port()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'url' => 'https://cloudradar.io',
        ]);

        $this->mock('alias:'.FQDN::class)
            ->shouldReceive('isValidPublicFQDN')
            ->once()
            ->andReturnTrue();
        $this->mock(CreateHostAction::class)
            ->shouldReceive('execute')
            ->withAnyArgs()
            ->andReturns(new Host);
        $this->mock(CreateWebCheckAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $_host, $webCheckData) {
                $this->assertEquals('https', $webCheckData->protocol);
                $this->assertEquals(443, $webCheckData->port);

                return true;
            })
            ->andReturns(new WebCheck);

        $webCheck = resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);

        $this->assertInstanceOf(WebCheck::class, $webCheck);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function will_default_path_if_not_set()
    {
        $team = $this->createTeam();
        $user = $this->createUser($team);

        $data = CreateWebCheckWizardDataFactory::make([
            'url' => 'http://cloudradar.io',
        ]);

        $this->mock('alias:'.FQDN::class)
            ->shouldReceive('isValidPublicFQDN')
            ->once()
            ->andReturnTrue();
        $this->mock(CreateHostAction::class)
            ->shouldReceive('execute')
            ->withAnyArgs()
            ->andReturns(new Host);
        $this->mock(CreateWebCheckAction::class)
            ->shouldReceive('execute')
            ->withArgs(function ($_user, $_host, $webCheckData) {
                $this->assertEquals('/', $webCheckData->path);

                return true;
            })
            ->andReturns(new WebCheck);

        $webCheck = resolve(CreateWebCheckFromWizardAction::class)->execute($user, $data);

        $this->assertInstanceOf(WebCheck::class, $webCheck);
    }
}
