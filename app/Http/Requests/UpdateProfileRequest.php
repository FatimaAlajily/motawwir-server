<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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

            'user_name' => 'nullable|string|max:255|unique:users,user_name,' . Auth::id(),
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',

            'bio'        => 'nullable|string|max:1000',
            'phone'      => 'nullable|string|max:20',
            'location'   => 'nullable|string|max:100',
            'skill'      => 'nullable|array',
            'skill.*'    => 'string|max:64',
            'github'     => 'nullable|url|max:255',
            'gmail'      => 'nullable|email|max:255',
            'domain'     => 'nullable|url|max:255',
            'cv'         => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'linkedin'   => 'nullable|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'user_name.string' => 'اسم المستخدم يجب أن يكون نصاً',
            'user_name.max' => 'اسم المستخدم يجب ألا يتجاوز 255 حرفاً',
            'user_name.unique' => 'اسم المستخدم مستخدم من قبل',
            'avatar.image' => 'الصورة الشخصية يجب أن تكون ملف صورة صالحاً',
            'avatar.mimes' => 'صيغة الصورة الشخصية يجب أن تكون: jpg, jpeg, png, gif',
            'avatar.max' => 'حجم الصورة الشخصية يجب ألا يتجاوز 4 ميجابايت',
            'bio.string' => 'النبذة التعريفية يجب أن تكون نصاً',
            'bio.max' => 'النبذة التعريفية يجب ألا تتجاوز 1000 حرف',
            'phone.string' => 'رقم الهاتف يجب أن يكون نصاً',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفاً',
            'location.string' => 'الموقع يجب أن يكون نصاً',
            'location.max' => 'الموقع يجب ألا يتجاوز 100 حرف',
            'skill.array' => 'المهارات يجب أن تكون مصفوفة',
            'skill.*.string' => 'كل مهارة يجب أن تكون نصاً',
            'skill.*.max' => 'المهارة الواجب ألا تتجاوز 64 حرفاً',
            'github.url' => 'رابط جيت هب يجب أن يكون رابطاً صحيحاً',
            'github.max' => 'رابط جيت هب يجب ألا يتجاوز 255 حرفاً',
            'gmail.email' => 'البريد الإلكتروني يجب أن يكون صيغة بريد صحيحة',
            'gmail.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرفاً',
            'domain.url' => 'رابط النطاق يجب أن يكون رابطاً صحيحاً',
            'domain.max' => 'رابط النطاق يجب ألا يتجاوز 255 حرفاً',
            'cv.file' => 'السيرة الذاتية يجب أن تكون ملفاً',
            'cv.mimes' => 'صيغة السيرة الذاتية يجب أن تكون: pdf, doc, docx',
            'cv.max' => 'حجم السيرة الذاتية يجب ألا يتجاوز 10 ميجابايت',
            'linkedin.url' => 'رابط لينكد إن يجب أن يكون رابطاً صحيحاً',
            'linkedin.max' => 'رابط لينكد إن يجب ألا يتجاوز 255 حرفاً',
        ];
    }
}
