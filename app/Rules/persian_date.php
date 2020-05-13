<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class persian_date implements Rule{
    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct(){
        //
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value){
        return preg_match(
            '^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|([1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9])|([1-9])|(0[1-9]))\/(30|([1-2][0-9])|(0[1-9])|([1-9]))))$^', $value);
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message(){
        return 'The validation error message.';
    }
}
