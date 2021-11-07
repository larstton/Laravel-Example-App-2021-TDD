<?php

namespace App\Http\Loophole\Resources;

use App\Support\StatusPage\StatusPageBuilder;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StatusPageBuilder
 */
class StatusPageResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        if ($this->hasHistory()) {
            return [
                'history' => $this->getHistory() ?? [],
            ];
        }

        if ($this->hasBadge()) {
            return [
                'title'  => $this->getStatusPageTitle(),
                'issues' => [
                    'total' => [
                        'alerts'   => $this->getTotalAlertCount(),
                        'warnings' => $this->when(
                            $this->includeWarnings(),
                            fn () => $this->getTotalWarningCount(),
                            0
                        ),
                    ],
                ],
            ];
        }

        if ($this->hasShield()) {
            // https://shields.io/endpoint
            static::$wrap = null;

            $forceState = $request->input('forceState', '');
            if (in_array($forceState, ['success', 'warning', 'error'])) {
                $state = $forceState;
            } else {
                $state = $this->getStateForShield();
            }

            return [
                'schemaVersion' => 1,
                'label'         => $request->input('label', 'System Status'),
                'style'         => $request->input('style', 'for-the-badge'),
                'labelColor'    => $request->input('labelColor', '#555555'),
                'isError'       => $state === 'error',
                'message'       => $this->getShieldMessageByState($request, $state),
                'color'         => $this->getShieldColorByState($request, $state),
            ];
        }

        return [
            $this->merge($this->statusPageData()),
            'issues' => [
                'total'   => [
                    'alerts'   => $this->getTotalAlertCount(),
                    'warnings' => $this->when(
                        $this->includeWarnings(),
                        fn () => $this->getTotalWarningCount(),
                        0
                    ),
                ],
                'byHosts' => $this->filter($this->getEventCountGroupedByHost()),
                'byTags'  => $this->when(
                    $this->includeGroupedByTag(),
                    fn () => $this->filter($this->getEventCountGroupedByTag()),
                    []
                ),
            ],
        ];
    }
}
