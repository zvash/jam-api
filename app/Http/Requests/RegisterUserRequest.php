<?php

namespace App\Http\Requests;

use App\Rules\IranMobilePhoneNumber;
use App\Rules\NotVerifiedUserIranMobilePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $pattern = '/^[\pL\pM\s-]+$/u';
        return [
            'first_name' => 'required|filled|min:2:max:50|regex:' . $pattern,
            'last_name' => 'required|filled|min:3|max:50|regex:' . $pattern,
            'phone' => [
                'required',
                new NotVerifiedUserIranMobilePhoneNumber()
            ],
            'password' => 'required|min:8',
        ];
    }
}
