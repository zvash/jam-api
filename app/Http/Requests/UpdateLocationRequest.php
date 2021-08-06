<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'county_id' => 'int|min:1|exists:counties,id',
            'address' => 'string|filled|min:10',
            'postal_code' => 'digits:10'
        ];
    }
}
