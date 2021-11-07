<?php

namespace Actions\WebCheck\Guard;

use App\Actions\WebCheck\Guard\WebCheckUniquenessGuard;
use App\Exceptions\WebCheckException;
use App\Models\Host;
use App\Models\WebCheck;
use Database\Factories\WebCheckDataFactory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebCheckUniquenessGuardTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_guard_against_duplicate_web_checks_for_team()
    {
        $host = Host::factory()->create();
        WebCheck::factory()->for($host)->create([
            'path'            => 'path',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5 = md5(Str::random()),
        ]);

        $webCheckData = WebCheckDataFactory::make([
            'path'       => 'path',
            'method'     => 'GET',
            'port'       => 80,
            'headersMD5' => $md5,
        ]);

        $this->expectException(WebCheckException::class);
        $this->expectErrorMessage("There is already an identical check for this host.");

        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host);
    }

    /** @test */
    public function will_permit_different_path()
    {
        $host = Host::factory()->create();
        WebCheck::factory()->for($host)->create([
            'path'            => 'path',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5 = md5(Str::random()),
        ]);

        $webCheckData = WebCheckDataFactory::make([
            'path'       => 'path-different',
            'method'     => 'GET',
            'port'       => 80,
            'headersMD5' => $md5,
        ]);

        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host);
    }

    /** @test */
    public function will_permit_different_method()
    {
        $host = Host::factory()->create();
        WebCheck::factory()->for($host)->create([
            'path'            => 'path',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5 = md5(Str::random()),
        ]);

        $webCheckData = WebCheckDataFactory::make([
            'path'       => 'path',
            'method'     => 'POST',
            'port'       => 80,
            'headersMD5' => $md5,
        ]);

        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host);
    }

    /** @test */
    public function will_permit_different_port()
    {
        $host = Host::factory()->create();
        WebCheck::factory()->for($host)->create([
            'path'            => 'path',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5 = md5(Str::random()),
        ]);

        $webCheckData = WebCheckDataFactory::make([
            'path'       => 'path',
            'method'     => 'GET',
            'port'       => 90,
            'headersMD5' => $md5,
        ]);

        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host);
    }

    /** @test */
    public function will_permit_different_headers_md5_hash()
    {
        $host = Host::factory()->create();
        WebCheck::factory()->for($host)->create([
            'path'            => 'path',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5 = md5(Str::random()),
        ]);

        $webCheckData = WebCheckDataFactory::make([
            'path'       => 'path',
            'method'     => 'GET',
            'port'       => 80,
            'headersMD5' => md5(Str::random()),
        ]);

        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host);
    }

    /** @test */
    public function will_permit_same_webcheck_for_different_host()
    {
        WebCheck::factory()->for(Host::factory())->create([
            'path'            => 'path',
            'method'          => 'GET',
            'port'            => 80,
            'headers_md5_sum' => $md5 = md5(Str::random()),
        ]);

        $webCheckData = WebCheckDataFactory::make([
            'path'       => 'path',
            'method'     => 'GET',
            'port'       => 80,
            'headersMD5' => $md5,
        ]);

        $host = Host::factory()->create();

        resolve(WebCheckUniquenessGuard::class)($webCheckData, $host);
    }
}
