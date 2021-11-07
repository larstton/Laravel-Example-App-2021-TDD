<?php

namespace App\Models;

use App\Models\Concerns\HasMeta;
use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\Tag as TagBase;

/**
 * @mixin IdeHelperTag
 */
class Tag extends TagBase
{
    use OwnedByTeam, HasMeta;

    public static $tagLocale = 'en';

    public static function findWhereBeginsWith(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? static::getTagLocale();

        return static::query()
            ->whereTagNameBeginsWith($name, $type, $locale)
            ->get();
    }

    public static function getTagLocale()
    {
        return static::$tagLocale ?? app()->getLocale();
    }

    public static function findFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? static::getTagLocale();

        return parent::findFromString($name, $type, $locale);
    }

    public static function findFromStringOfAnyType(string $name, string $locale = null)
    {
        $locale = $locale ?? static::getTagLocale();

        return parent::findFromStringOfAnyType($name, $locale);
    }

    protected static function findOrCreateFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? static::getTagLocale();

        return parent::findOrCreateFromString($name, $type, $locale);
    }

    /**
     * @return MorphToMany|Host
     */
    public function hosts()
    {
        return $this->morphedByMany(Host::class, 'taggable');
    }

    public function scopeWhereTagNameBeginsWith(
        Builder $query,
        string $name,
        string $type = null,
        string $locale = null
    ) {
        $locale = $locale ?? static::getTagLocale();

        return $query
            ->where("name->{$locale}", 'LIKE', "{$name}%")
            ->when($type, fn ($query) => $query->where('type', $type));
    }

    public function scopeWhereTagIs(Builder $query, string $name, $locale = null): Builder
    {
        $locale = $locale ?? static::getTagLocale();
        $locale = $this->getQuery()->getGrammar()->wrap('name->'.$locale);

        return $query
            ->whereRaw('lower('.$locale.') like ?', ['%'.mb_strtolower($name).'%']);
    }

    public function scopeContaining(Builder $query, string $name, $locale = null): Builder
    {
        $locale = $locale ?? static::getTagLocale();

        return parent::scopeContaining($query, $name, $locale);
    }

    public function setAttribute($key, $value)
    {
        if ($key === 'name' && ! is_array($value)) {
            return $this->setTranslation($key, static::getTagLocale(), $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function addRecipientFilterToMeta(Recipient $recipient)
    {
        $meta = $this->meta->recipient_filtering ?? [];
        $meta[] = $recipient->id;
        $meta = array_unique($meta);
        $this->meta->recipient_filtering = $meta;

        return $this;
    }

    public function removeRecipientFilterFromMeta(Recipient $recipient)
    {
        $meta = $this->meta->recipient_filtering ?? [];
        if (($key = array_search($recipient->id, $meta)) !== false) {
            unset($meta[$key]);
        }

        $this->meta->recipient_filtering = $meta;

        return $this;
    }

    public function addTeamMemberFilterToMeta(TeamMember $teamMember)
    {
        $meta = $this->meta->team_member_filtering ?? [];
        $meta[] = $teamMember->id;
        $meta = array_unique($meta);
        $this->meta->team_member_filtering = $meta;

        return $this;
    }

    public function removeTeamMemberFilterFromMeta(TeamMember $teamMember)
    {
        $meta = $this->meta->team_member_filtering ?? [];
        if (($key = array_search($teamMember->id, $meta)) !== false) {
            unset($meta[$key]);
        }

        $this->meta->team_member_filtering = $meta;

        return $this;
    }
}
