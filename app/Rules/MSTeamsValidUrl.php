<?php

namespace App\Rules;

use App\Support\MSTeamsService;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MSTeamsValidUrl implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /**
         * @var $MSTeamsService MSTeamsService
         */
        $MSTeamsService = app()->get(MSTeamsService::class);

        try {
            Log::info('Checking msteams url', ['value' => $value]);

            return $MSTeamsService->test($value);

        } catch (\Exception $exception) {
            $this->errorMessage = trans('Error checking MSTeams webhook url: :exception',['exception' => $exception->getMessage()]);

            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Incorrect URL for MSTeams recipient';
    }
}
