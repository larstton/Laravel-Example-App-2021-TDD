<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;

trait HasMeta
{
    protected $schemalessAttributeName = 'meta';

    public function getHasMetaCasts(): array
    {
        return [$this->schemalessAttributeName() => 'array'];
    }

    public function schemalessAttributeName()
    {
        return 'meta';
    }

    public function getMetaAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, $this->schemalessAttributeName());
    }

    public function scopeWithMeta(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes($this->schemalessAttributeName());
    }
}
