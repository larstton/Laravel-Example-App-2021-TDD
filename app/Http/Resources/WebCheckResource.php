<?php

namespace App\Http\Resources;

use App\Http\Transformers\DateTransformer;
use App\Models\WebCheck;

/**
 * @mixin WebCheck
 */
class WebCheckResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                      => $this->id,
            'hostId'                  => $this->host_id,
            'userId'                  => $this->user_id,
            'dontFollowRedirects'     => $this->dont_follow_redirects,
            'ignoreSslErrors'         => $this->ignore_ssl_errors,
            'searchHtmlSource'        => $this->search_html_source,
            'active'                  => $this->active,
            'port'                    => $this->port,
            'method'                  => $this->method,
            'protocol'                => $this->protocol,
            'path'                    => $this->path,
            'expectedPattern'         => $this->expected_pattern,
            'expectedPatternPresence' => $this->expected_pattern_presence,
            'expectedHttpStatus'      => $this->expected_http_status,
            'timeOut'                 => $this->time_out,
            'checkInterval'           => $this->check_interval,
            'lastSuccess'             => $this->last_success,
            'inProgress'              => $this->in_progress,
            'headers'                 => $this->headers,
            'postData'                => $this->post_data,
            'dates'                   => [
                'lastCheckedAt' => DateTransformer::transform($this->last_checked_at),
                'updatedAt'     => DateTransformer::transform($this->updated_at),
                'createdAt'     => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
