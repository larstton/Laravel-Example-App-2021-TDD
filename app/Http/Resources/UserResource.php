<?php

namespace App\Http\Resources;

use App\Http\Resources\Team\TeamResource;
use App\Http\Transformers\DateTransformer;
use App\Models\User;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'email'           => $this->email,
            'nickname'        => $this->nickname,
            'name'            => $this->name,
            'lang'            => $this->lang,
            'hostTag'         => $this->host_tag,
            'privacyAccepted' => $this->privacy_accepted,
            'termsAccepted'   => $this->terms_accepted,
            'productNews'     => $this->product_news,
            'roles'           => $this->role->value,
            'teamStatus'      => $this->team_status->value,
            'team'            => TeamResource::make($this->team),
            'subUnitId'       => $this->sub_unit_id,
            'settings'        => user_settings($this->resource)->get(),
            'dates'           => [
                'verifiedAt' => DateTransformer::transform($this->email_verified_at),
                'updatedAt'  => DateTransformer::transform($this->updated_at),
                'createdAt'  => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
