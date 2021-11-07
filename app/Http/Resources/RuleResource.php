<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\Rule;

/**
 * @mixin Rule
 */
class RuleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'action'            => $this->action,
            'active'            => $this->active,
            'checkKey'          => $this->check_key,
            'checkType'         => collect($this->check_type)->map->value,
            'checksum'          => $this->checksum,
            'userId'            => $this->user_id,
            'expressionAlias'   => $this->expression_alias,
            'finish'            => $this->finish,
            'function'          => $this->function,
            'hostMatchCriteria' => $this->host_match_criteria,
            'hostMatchPart'     => $this->host_match_part,
            'keyFunction'       => $this->key_function,
            'mandatory'         => $this->mandatory,
            'operator'          => $this->operator,
            'position'          => $this->position,
            'resultsRange'      => $this->results_range,
            'teamUuid'          => $this->team_id,
            'threshold'         => $this->threshold,
            'unit'              => $this->unit,
            'dates'             => [
                'updatedAt' => DateTransformer::transform($this->updated_at),
                'createdAt' => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
