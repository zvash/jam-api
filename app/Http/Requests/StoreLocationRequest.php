<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'county_id' => 'required|int|min:1|exists:counties,id',
            'address' => 'required|string|filled|min:10',
            'postal_code' => 'required|digits:10'
        ];
    }
}
