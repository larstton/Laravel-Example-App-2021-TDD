<?php

namespace App\Data\Wizard;

use App\Data\BaseData;
use App\Http\Requests\Wizard\CreateWebCheckWizardRequest;

class CreateWebCheckWizardData extends BaseData
{
    public string $url;
    public bool $preflight;

    public static function fromRequest(CreateWebCheckWizardRequest $request): self
    {
        return new self([
            'url'       => $request->input('url'),
            'preflight' => (bool) $request->input('preflight'),
        ]);
    }
}
