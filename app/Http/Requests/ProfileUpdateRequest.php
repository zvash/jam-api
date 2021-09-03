<?php

namespace App\Http\Requests;

use App\Rules\PassesNationalCodeCheck;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'company' => 'filled|min:2:max:250|regex:' . $pattern,
            'first_name' => 'filled|min:2:max:50|regex:' . $pattern,
            'last_name' => 'filled|min:3|max:50|regex:' . $pattern,
            'email' => 'filled|email:rfc|unique:users,email,'. $this->user()->id . ',id',
            'national_code' => [
                'filled',
                new PassesNationalCodeCheck(),
                ],
            'avatar' => 'mimes:jpeg,jpg,png',
        ];
    }
}
