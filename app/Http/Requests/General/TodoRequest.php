<?php

namespace App\Http\Requests\General;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['bail', 'required', Rule::in(['NOTE', 'TASK', 'MISC'])],
            'name' => 'required|string|max:150',
            'description' => 'required|string',
            'date' => 'required'
        ];
    }
}
