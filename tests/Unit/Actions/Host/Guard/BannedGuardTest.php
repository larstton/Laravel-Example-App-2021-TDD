<?php

namespace Tests\Unit\Actions\Host\Guard;

use App\Actions\Host\Guard\BannedGuard;
use App\Exceptions\HostException;
use Database\Factories\HostDataFactory;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class BannedGuardTest extends TestCase
{
    use WithoutTenancyChecks;

    /** @test */
    public function will_not_guard_if_no_connect_supplied()
    {
        $hostData = HostDataFactory::make([
            'connect' => null,
        ]);

        $this->expectNotToPerformAssertions();

        resolve(BannedGuard::class)($hostData);
    }

    /** @test */
    public function will_guard_against_banned_connect()
    {
        config([
            'banned.domains' => [
                '*.im-a-banned-domain.com*',
            ],
        ]);

        $hostData = HostDataFactory::make([
            'connect' => 'sub.im-a-banned-domain.com',
        ]);

        $this->expectException(HostException::class);
        $this->expectErrorMessage("'sub.im-a-banned-domain.com' domain is not allowed.");

        resolve(BannedGuard::class)($hostData);
    }

    /** @test */
    public function will_allow_non_banned_connect()
    {
        config([
            'banned.domains' => [
                '*.im-a-banned-domain.com*',
            ],
        ]);

        $hostData = HostDataFactory::make([
            'connect' => 'sub.im-NOT-banned-domain.com',
        ]);

        $this->expectNotToPerformAssertions();

        resolve(BannedGuard::class)($hostData);
    }
}
