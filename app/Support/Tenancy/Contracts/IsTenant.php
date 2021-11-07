<?php

namespace App\Support\Tenancy\Contracts;

interface IsTenant
{
    public static function hasCurrentTenant(): bool;

    public static function currentTenant();

    public function makeCurrentTenant();

    public function isCurrentTenant(): bool;
}
