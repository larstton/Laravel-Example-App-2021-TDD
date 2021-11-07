<?php /** @noinspection PhpUnusedPrivateMethodInspection */

namespace App\Http\Controllers\Onboard;

use App\Actions\Onboard\SaveOnboardingPayloadStep1Action;
use App\Actions\Onboard\SaveOnboardingPayloadStep2Action;
use App\Data\Onboard\OnboardStep1Data;
use App\Data\Onboard\OnboardStep2Data;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function __invoke(Request $request, $step)
    {
        return app()->call([$this, "handleStep{$step}"]);
    }

    public function handleStep1(Request $request, SaveOnboardingPayloadStep1Action $action)
    {
        $this->validate($request, [
            'dateFormat'      => [
                'required',
                'string',
                'regex:/[LBM]./',
            ],
            'defaultFrontman' => [
                'required',
                'uuid',
                Rule::exists('frontmen', 'id'),
            ],
            'timezone'        => [
                'required',
                'string',
                'min:3',
                'max:200',
                'timezone',
            ],
        ]);

        $action->execute(OnboardStep1Data::make($request), current_team());

        return $this->accepted();
    }

    public function handleStep2(Request $request, SaveOnboardingPayloadStep2Action $action)
    {
        $this->validate($request, [
            'name'                     => [
                Rule::requiredIf($request->onboardingTraining),
                'nullable',
                'string',
            ],
            'companyName'              => [
                Rule::requiredIf($request->onboardingTraining),
                'nullable',
                'string',
            ],
            'phoneNumber'              => [
                Rule::requiredIf($request->onboardingTraining),
                'nullable',
                'string',
                'regex:/^\\+[0-9]+$/',
                'min:5',
                'max:200',
            ],
            'expectedHostRequirements' => [
                'nullable',
                'string',
            ],
            'reasonForSignup'          => [
                'nullable',
                'string',
            ],
            'onboardingTraining'       => [
                'nullable',
                'boolean',
            ],
        ]);

        $action->execute(OnboardStep2Data::make($request), current_user(), current_team());

        return $this->accepted();
    }
}
