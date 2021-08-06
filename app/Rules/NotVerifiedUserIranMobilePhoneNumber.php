<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class NotVerifiedUserIranMobilePhoneNumber implements Rule
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
        $pattern = '/^09[0-4]{1}[0-9]{8}$/';
        if (! preg_match($pattern, $value)) {
            return false;
        }
        $user = User::query()
            ->where($attribute, $value)
            ->whereNotNull($attribute . '_verified_at')
            ->first();
        if ($user) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('messages.validation.invalid_phone');
    }
}
