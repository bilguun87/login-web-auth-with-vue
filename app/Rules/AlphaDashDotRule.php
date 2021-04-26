<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AlphaDashDotRule implements Rule
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
        //dd(preg_match('/[^\pL\pM\pN\-\.\s]+/u', $value));
        if(preg_match('/[^\pL\pM\pN\-\.\s]+/u', $value) > 0)
            return false;
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute may only contain letters, numbers, dashes and dots';
    }
}
