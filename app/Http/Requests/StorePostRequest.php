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
            'file' => 'required_unless:type,question,work,team|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'skill' => 'required_if:type,work,project,team|string|max:64',
            'primary_link' => 'required_if:type,new,project,team|url|max:255',
            'secondary_link' => 'nullable|url|max:255',


            'location' => 'required_if:type,work|string|max:64',
            'salary_range' => 'required_if:type,work|string|max:50',
            'work_place' => 'required_if:type,work|string|max:33',
            'contact' => 'required_if:type,work|string|max:15',
            'hours' => 'required_if:type,work|string|max:20',
        ];  
    }
}
