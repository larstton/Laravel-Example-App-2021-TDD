<?php

namespace App\Actions\WebCheck\Guard;

use App\Data\WebCheck\WebCheckData;
use App\Exceptions\WebCheckException;
use App\Models\Host;
use App\Models\WebCheck;

class WebCheckUniquenessGuard
{
    public function __invoke(WebCheckData $webCheckData, Host $host, ?WebCheck $webCheck = null): void
    {
        throw_if(
            WebCheck::query()
                ->when($webCheck, fn ($query) => $query->where('id', '!=', $webCheck->id))
                ->where('host_id', $host->id)
                ->where('path', $webCheckData->path)
                ->where('method', $webCheckData->method)
                ->where('port', $webCheckData->port)
                ->where('headers_md5_sum', $webCheckData->headersMD5)
                ->exists(),
            WebCheckException::checkExistsForTeam()
        );
    }
}
