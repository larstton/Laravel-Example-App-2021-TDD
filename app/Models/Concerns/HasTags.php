<?php

namespace App\Models\Concerns;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Tags\HasTags as HasTagsAlias;

trait HasTags
{
    use HasTagsAlias;

    /**
     * @return MorphToMany|Tag
     */
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }

    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * @param  array|string  $tag
     */
    public function addTags($tag): void
    {
        $this->attachTags(Arr::wrap($tag), self::getTagType());
    }

    public static function getTagType()
    {
        return Str::lower(class_basename(self::class));
    }

    public function removeTag(string $tag)
    {
        return $this->detachTag(
            Tag::findOrCreate($tag, self::getTagType())
        );
    }

    public function scopeWhereAllTagsBeginWithString(Builder $query, $tags, $type = null): Builder
    {
        collect($tags)
            ->mapWithKeys(fn ($tag) => [$tag => Tag::findWhereBeginsWith($tag, $type)])
            ->reject->isEmpty()
            ->each(function (Collection $tags) use ($query) {
                $query->whereHas('tags', function (Builder $query) use ($tags) {
                    $query->where(fn ($query) => $query->whereIn('tags.id', $tags->pluck('id')));
                });
            });

        return $query;
    }

    public function scopeWhereAnyTagBeginsWithString(Builder $query, $tags, $type = null): Builder
    {
        $tagIds = collect($tags)
            ->mapWithKeys(fn ($tag) => [$tag => Tag::findWhereBeginsWith($tag, $type)])
            ->reject->isEmpty()
            ->flatten()
            ->pluck('id');

        if (filled($tagIds)) {
            $query->whereHas('tags', function (Builder $query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            });
        }

        return $query;
    }
}
