<?php

namespace App\Http\Requests;

use App\Rules\IranMobilePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordByCodeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => [
                'required',
                new IranMobilePhoneNumber()
            ],
            'code' => 'required|string|filled|min:4',
            'password' => 'required|confirmed|min:8',
        ];
    }
}
