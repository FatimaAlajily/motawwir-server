<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'location' => 'required_if:type,work|string|max:255',
        'salary_range' => 'required_if:type,work|string|max:100',
        'work_place' => 'required_if:type,work|string|max:100',
        'contact' => 'required_if:type,work|string|max:255',
        'hours' => 'required_if:type,work|string|max:100',
        ];
    }
}
