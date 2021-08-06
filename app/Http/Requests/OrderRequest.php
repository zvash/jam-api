<?php

namespace App\Http\Requests;

use App\Rules\ValidOrderByToken;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'driver_token' => [
                'required',
                new ValidOrderByToken()
            ]
        ];
    }
}
