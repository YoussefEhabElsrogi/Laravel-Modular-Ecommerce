<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // Name
            'name.*'        => ['required', 'string', 'min:3', 'max:100'],

            // Description
            'description.*' => ['required', 'string', 'min:3', 'max:1000'],

            // Price
            'price'         => ['required', 'numeric', 'min:0'],

            // Images
            'images'        => ['required', 'array'],
            'images.*'      => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg'],

            // Categories
            'categories'    => ['required', 'array'],
            'categories.*'  => ['required', 'exists:categories,id'],
        ];
    }
}
