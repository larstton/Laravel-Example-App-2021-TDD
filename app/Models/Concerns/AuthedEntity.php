<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface AuthedEntity
{
    public function team(): BelongsTo;

    public function getAuthIdentifier();
}
