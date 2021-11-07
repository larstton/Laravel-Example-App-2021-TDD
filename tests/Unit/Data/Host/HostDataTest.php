<?php

namespace Tests\Unit\Data\Host;

use App\Data\Host\HostData;
use App\Data\Host\HostSnmpData;
use App\Http\Requests\Host\CreateHostRequest;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\SubUnit;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;
use TypeError;

class HostDataTest extends TestCase
{
    use WithoutEvents, WithoutTenancyChecks;

    private array $hostData;

    /** @test */
    public function can_create_dto_with_correct_values()
    {
        $this->assertInstanceOf(HostData::class, new HostData($this->hostData));
    }

    /** @test */
    public function will_build_with_frontman()
    {
        $data = new HostData(array_merge($this->hostData, [
            'frontman' => Frontman::factory()->create(),
        ]));

        $this->assertInstanceOf(HostData::class, $data);
    }

    /** @test */
    public function will_build_with_sub_unit()
    {
        $data = new HostData(array_merge($this->hostData, [
            'subUnit' => SubUnit::factory()->create(),
        ]));

        $this->assertInstanceOf(HostData::class, $data);
    }

    /** @test */
    public function will_fail_with_invalid_name()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'name' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_connect()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'connect' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_description()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'description' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_cagent()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'cagent' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_active()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'active' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_dashboard()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'dashboard' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_muted()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'muted' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_frontman()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'frontman' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_subunit()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'subUnit' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_tags()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'tags' => 123,
        ]));
    }

    /** @test */
    public function will_fail_with_invalid_snmp_data()
    {
        $this->expectException(TypeError::class);
        new HostData(array_merge($this->hostData, [
            'snmpData' => 123,
        ]));
    }

    /** @test */
    public function will_build_snmp_data_dto_when_snmp_data_supplied_as_array()
    {
        $data = new HostData(array_merge($this->hostData, [
            'snmpData' => [
                'protocol'  => 'v2',
                'community' => 'cloud',
                'port'      => 222,
                'timeout'   => 333,
            ],
        ]));

        $this->assertInstanceOf(HostSnmpData::class, $data->snmpData);
        $this->assertEquals('v2', $data->snmpData->protocol);
        $this->assertEquals('cloud', $data->snmpData->community);
        $this->assertEquals(222, $data->snmpData->port);
        $this->assertEquals(333, $data->snmpData->timeout);
    }

    /** @test */
    public function can_build_up_from_request()
    {
        $request = new CreateHostRequest([
            'name'        => $this->hostData['name'],
            'description' => $this->hostData['description'],
            'connect'     => $this->hostData['connect'],
            'cagent'      => $this->hostData['cagent'],
            'dashboard'   => $this->hostData['dashboard'],
            'muted'       => $this->hostData['muted'],
            'active'      => $this->hostData['active'],
            'frontmanId'  => $this->hostData['frontman'],
            'subUnitId'   => $this->hostData['subUnit'],
            'tags'        => $this->hostData['tags'],
            'snmpData'    => $this->hostData['snmpData'],
        ]);

        $actual = HostData::fromRequest($request);

        $this->assertInstanceOf(HostData::class, $actual);
    }

    /** @test */
    public function can_build_up_from_v1_public_api_request()
    {
        $host = Host::factory()->create();
        $request = new \App\Http\Api\V1\Requests\CreateHostRequest([
            'name' => $this->hostData['name'],
        ]);

        $actual = HostData::fromApiV1Request($request, $host);

        $this->assertInstanceOf(HostData::class, $actual);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hostData = [
            'name'        => 'host-name',
            'connect'     => '8.8.8.8',
            'description' => 'host-description',
            'cagent'      => false,
            'active'      => true,
            'dashboard'   => true,
            'muted'       => false,
            'frontman'    => null,
            'subUnit'     => null,
            'tags'        => null,
            'snmpData'    => [],
        ];
    }
}
