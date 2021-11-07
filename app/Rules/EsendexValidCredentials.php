<?php

namespace App\Rules;

use App\Support\EsendexService;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class EsendexValidCredentials implements Rule
{
    private $errorMessage = '';

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
         * @var $esendexService EsendexService
         */
        $esendexService = app()->get(EsendexService::class);

        try {
            Log::info('Checking esendex credentials', ['value' => $value]);
            $creditsCount = $esendexService->credits($value['username'], $value['password'], $value['account']);
            if ($creditsCount < 1) {
                $this->errorMessage = trans('No remaining messages, please charge your account');
            }

            return $creditsCount > 0;
        } catch (Exception $exception) {
            $this->errorMessage = trans('Error retrieving remaining messages: :exception',
                ['exception' => $exception->getMessage()]);

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
        return $this->errorMessage;
    }
}
