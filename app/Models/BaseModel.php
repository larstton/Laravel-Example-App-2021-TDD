<?php

namespace App\Models;

use App\Models\Concerns\HasTraitsWithCasts;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasUuid, HasTraitsWithCasts, HasFactory;

    protected $guarded = [];
}
