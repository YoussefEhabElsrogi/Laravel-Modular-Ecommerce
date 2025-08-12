<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use CodeZero\UniqueTranslation\UniqueTranslationRule;

class CategoryStoringRequest extends FormRequest
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
            'name.*'    => ['required', 'string', 'min:3', 'max:100', UniqueTranslationRule::for('categories')],
            'image'     => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
        ];
    }
}
