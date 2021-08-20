<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetDriverCountiesRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'state_ids' => 'array',
            'state_ids.*' => 'int|exists:states,id',
            'county_ids' => 'array',
            'county_ids.*' => 'int|exists:counties,id',
        ];
    }
}
