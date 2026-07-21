<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'text' => 'required|string|max:1000',
            'type' => 'required|in:post,profile',
            'post_id' => 'required_if:type,post|prohibited_if:type,profile|exists:posts,id',
            'profile_user_id' => 'required_if:type,profile|prohibited_if:type,post|exists:users,id'
        ];
    }

    public function messages()
    {
        return [
            'post_id.required_if' => 'يجب تحديد المنشور عند التعليق على منشور',
            'post_id.prohibited_if' => 'لا يجوز إرسال رقم المنشور عند التعليق على صفحة شخصية',
            'profile_user_id.required_if' => 'يجب تحديد صاحب الصفحة عند التعليق على صفحة شخصية',
            'profile_user_id.prohibited_if' => 'لا يجوز إرسال رقم صاحب الصفحة عند التعليق على منشور',
        ];
    }
}
