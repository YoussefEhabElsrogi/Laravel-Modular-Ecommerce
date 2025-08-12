<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdatingRequest extends FormRequest
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
            'images'        => ['nullable', 'array'],
            'images.*'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],

            // Categories
            'categories'    => ['nullable', 'array'],
            'categories.*'  => ['nullable', 'exists:categories,id'],
        ];
    }
}
