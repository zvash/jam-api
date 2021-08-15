<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images' => 'required|array|filled',
            'images.*' => 'mimes:jpg,jpeg,png',
            'description' => 'required|string|min:1',
            'location_id' => [
                'required',//ask if this field is required
                'exists:locations,id',
                function ($attribute, $value, $fail) {
                    $user = request()->user();
                    if (! $user->locations()->where('id', $value)->first()) {
                        $fail('Wrong location id');
                        return false;
                    }
                    return true;
                }
            ],
            'requires_driver' => 'required|boolean',
            'final_price_needed' => 'required|boolean',
            'approximate_weight' => 'numeric|min:0.1',
            'pickup_date' => 'date|after:yesterday|required_if:requires_driver,true',
            'items' => 'array|filled',
            'items.*.id' => 'exists:items',
            'items.*.weight' => 'numeric|min:0.1',
        ];
    }
}
