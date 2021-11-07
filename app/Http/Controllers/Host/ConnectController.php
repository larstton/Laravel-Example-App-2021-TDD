<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\HostConnectValidTypeCheckRequest;
use App\Support\Validation\FQDN;
use App\Support\Validation\IpAddress;

class ConnectController extends Controller
{
    public function __invoke(HostConnectValidTypeCheckRequest $request, $connect)
    {
        $isValidIP = IpAddress::isValid($connect);
        $isValidFQDN = FQDN::isValid($connect);
        $type = 'invalid';
        if ($isValidIP) {
            $type = IpAddress::isValidPrivate($connect) ? 'private' : 'public';
        }
        if ($isValidFQDN) {
            $type = FQDN::isValidPrivate($connect) ? 'private' : 'public';
        }

        return $this->json([
            'data' => compact('connect', 'isValidIP', 'isValidFQDN', 'type'),
        ]);
    }
}
