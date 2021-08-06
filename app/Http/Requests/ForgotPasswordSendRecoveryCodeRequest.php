<?php

namespace App\Http\Requests;

use App\Rules\IranMobilePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordSendRecoveryCodeRequest extends FormRequest
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
            ]
        ];
    }
}
