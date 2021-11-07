<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperUserSetting
 */
class UserSetting extends Model
{
    protected $primaryKey = 'user_id';

    protected $casts = [
        'value' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'value',
    ];
}
