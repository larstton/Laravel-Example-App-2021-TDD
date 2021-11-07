<?php

namespace App\Support\AgentData;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AgentData
{
    private Agent $agent;
    private Request $request;
    private array $data;

    public function __construct(Agent $agent, Request $request)
    {
        $this->agent = $agent;
        $this->request = $request;
        $this->build();
    }

    public function build()
    {
        $this->data = [
            'ip'        => $this->request->getClientIp(),
            'device'    => [
                'isBot'       => $this->agent->isRobot(),
                'model'       => $this->agent->device(),
                'platform'    => $this->agent->platform(),
                'isMobile'    => $this->agent->isMobile(),
                'isTablet'    => $this->agent->isTablet(),
                'isDesktop'   => $this->agent->isDesktop(),
                'isPhone'     => $this->agent->isPhone(),
                'mobileGrade' => $this->agent->mobileGrade(),
            ],
            'header'    => [
                'attributes' => $this->agent->getHttpHeaders(),
            ],
            'server'    => [
                'attributes' => $this->request->server(),
            ],
            'browser'   => [
                'name'    => $browser = $this->agent->browser(),
                'version' => $this->agent->version($browser),
            ],
            'userAgent' => $this->agent->getUserAgent(),
            'bot'       => [
                'isBot'   => $this->agent->isRobot(),
                'botName' => $this->agent->robot(),
            ],
            'languages' => $this->agent->languages(),
        ];
    }

    public function all(): array
    {
        return $this->data;
    }
}
