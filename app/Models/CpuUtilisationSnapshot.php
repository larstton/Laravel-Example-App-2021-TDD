<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCpuUtilisationSnapshot
 */
class CpuUtilisationSnapshot extends Model
{
    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'top'      => 'array',
    ];
}
