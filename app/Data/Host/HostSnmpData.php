<?php

namespace App\Data\Host;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class HostSnmpData extends FlexibleDataTransferObject
{
    public ?string $protocol;
    public ?int $port;
    public ?int $timeout;
    public ?string $community;
    public ?string $privacyProtocol;
    public ?string $securityLevel;
    public ?string $authenticationProtocol;
    public ?string $username;
    public ?string $authenticationPassword;
    public ?string $privacyPassword;

    public function hasData()
    {
        return ! is_null($this->protocol);
    }

    public function isV2()
    {
        return $this->protocol === 'v2';
    }
}
