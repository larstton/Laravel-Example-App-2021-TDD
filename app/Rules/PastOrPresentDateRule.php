<?php

namespace App\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class PastOrPresentDateRule implements Rule
{
    private $dateFormat;

    public function __construct($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function passes($attribute, $value)
    {
        try {
            return ! Carbon::createFromFormat($this->dateFormat, $value)->isFuture();
        } catch (Exception $exception) {
            return false;
        }
    }

    public function message()
    {
        return 'Date cannot be in the future or is an invalid format.';
    }
}
