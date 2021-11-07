<?php

namespace App\Rules;

use App\Models\Recipient;
use Illuminate\Contracts\Validation\Rule;

class UniqueRecipientRule implements Rule
{
    public function passes($attribute, $value)
    {
        $mediatype = request('mediatype');

        return Recipient::where('media_type', $mediatype)
            ->where('sendto', $value)
            ->doesntExist();
    }

    public function message()
    {
        return 'Combination of mediatype and sendto already exists';
    }
}
