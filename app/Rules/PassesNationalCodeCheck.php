<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PassesNationalCodeCheck implements Rule
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
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkNationalCode($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('messages.validation.invalid_national_code');
    }

    /**
     * @param string $code
     * @return bool
     */
    private function checkNationalCode(string $code)
    {
        $code = (string)preg_replace('/[^0-9]/', '', $code);

        if (strlen($code) != 10) {
            return false;
        }

        $list_code = str_split($code);
        $last = (int)$list_code[9];
        unset($list_code[9]);
        $i = 10;
        $sum = 0;

        foreach ($list_code as $key => $_) {
            $sum += intval($_) * $i--;
        }

        $mod = (int)$sum % 11;

        if ($mod >= 2) {
            $mod = 11 - $mod;
        }

        return $mod == $last;
    }
}
