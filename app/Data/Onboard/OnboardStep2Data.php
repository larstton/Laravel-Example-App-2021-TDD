<?php

namespace App\Data\Onboard;

use App\Data\BaseData;
use Illuminate\Http\Request;

class OnboardStep2Data extends BaseData
{
    public ?string $name;
    public ?string $companyName;
    public ?string $phoneNumber;
    public ?string $expectedHostRequirements;
    public ?string $reasonForSignup;
    public bool $onboardingTraining;

    public static function make(Request $request): self
    {
        return new self([
            'name'                     => $request->name,
            'companyName'              => $request->companyName,
            'phoneNumber'              => $request->phoneNumber,
            'expectedHostRequirements' => $request->expectedHostRequirements,
            'reasonForSignup'          => $request->reasonForSignup,
            'onboardingTraining'       => $request->onboardingTraining,
        ]);
    }
}
