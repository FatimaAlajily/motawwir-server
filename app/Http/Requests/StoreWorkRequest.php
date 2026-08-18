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

    public function messages(): array
    {
        return [
            'location.required_if' => 'حقل الموقع مطلوب',
            'location.string' => 'الموقع يجب أن يكون نصاً',
            'location.max' => 'الموقع يجب ألا يتجاوز 255 حرفاً',
            'salary_range.required_if' => 'حقل نطاق الراتب مطلوب',
            'salary_range.string' => 'نطاق الراتب يجب أن يكون نصاً',
            'salary_range.max' => 'نطاق الراتب يجب ألا يتجاوز 100 حرف',
            'work_place.required_if' => 'حقل مكان العمل مطلوب',
            'work_place.string' => 'مكان العمل يجب أن يكون نصاً',
            'work_place.max' => 'مكان العمل يجب ألا يتجاوز 100 حرف',
            'contact.required_if' => 'حقل معلومات التواصل مطلوب',
            'contact.string' => 'معلومات التواصل يجب أن تكون نصاً',
            'contact.max' => 'معلومات التواصل يجب ألا تتجاوز 255 حرفاً',
            'hours.required_if' => 'حقل ساعات العمل مطلوب',
            'hours.string' => 'ساعات العمل يجب أن تكون نصاً',
            'hours.max' => 'ساعات العمل يجب ألا تتجاوز 100 حرف',
        ];
    }
}
