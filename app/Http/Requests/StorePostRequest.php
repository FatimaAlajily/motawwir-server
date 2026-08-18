<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title' => 'required|string|min:5|max:255',
            'content' => 'required_unless:type,new|min:10',
            'type' => 'required|in:question,work,new,project,team',
            'file' => 'required_unless:type,question,work,team|file|mimes:jpg,jpeg,png,gif,mp4,pdf,doc,docx|max:10240',
            'skill' => 'required_if:type,work,project,team|array|min:1',
            'skill.*' => 'string|max:64',
            'primary_link' => 'required_if:type,new,project,team|url|max:255',
            'secondary_link' => 'nullable|url|max:255',


            'location' => 'required_if:type,work|string|max:64',
            'salary_range' => 'required_if:type,work|string|max:50',
            'work_place' => 'required_if:type,work|string|max:33',
            'contact' => 'required_if:type,work|string|max:15',
            'hours' => 'required_if:type,work|string|max:20',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // العنوان
            'title.required' => 'حقل العنوان مطلوب.',
            'title.string' => 'يجب أن يكون العنوان نصاً.',
            'title.min' => 'يجب أن يكون العنوان 5 أحرف على الأقل.',
            'title.max' => 'يجب ألا يتجاوز العنوان 255 حرفاً.',

            // المحتوى
            'content.required_unless' => 'حقل المحتوى مطلوب.',
            'content.min' => 'يجب أن يكون المحتوى 10 أحرف على الأقل.',

            // النوع
            'type.required' => 'نوع المنشور مطلوب.',
            'type.in' => 'نوع المنشور المحدد غير صالح.',

            // الملف
            'file.required_unless' => 'الملف المرفق مطلوب.',
            'file.file' => 'يجب أن يكون المرفق ملفاً صالحاً.',
            'file.mimes' => 'نوع الملف غير مسموح به.',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',

            // المهارات
            'skill.required_if' => 'يجب اختيار مهارة واحدة على الأقل.',
            'skill.array' => 'المهارات يجب أن تكون مصفوفة.',
            'skill.min' => 'يجب اختيار مهارة واحدة على الأقل.',

            // الروابط
            'primary_link.required_if' => 'حقل الرابط الأساسي مطلوب.',
            'primary_link.url' => 'يجب أن يكون الرابط الأساسي رابطاً صحيحاً.',
            'secondary_link.url' => 'يجب أن يكون الرابط الثانوي رابطاً صحيحاً.',

            // تفاصيل العمل
            'location.required_if' => 'حقل الموقع مطلوب.',
            'salary_range.required_if' => 'حقل نطاق الراتب مطلوب.',
            'work_place.required_if' => 'حقل مكان العمل مطلوب.',
            'contact.required_if' => 'حقل وسائل التواصل مطلوب.',
            'hours.required_if' => 'حقل ساعات العمل مطلوب.',
        ];
    }
}
