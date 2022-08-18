<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CoordinateRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (substr($value, 2, 1) === '.' && is_numeric($value) && strlen($value) <= 9) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Invalid coordinate provided.');
    }
}
