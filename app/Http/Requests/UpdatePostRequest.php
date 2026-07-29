<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'title' => 'sometimes|string|min:5|max:255',
            'content' => 'nullable|string|min:10',
            'type' => 'sometimes|in:question,work,new,project,team',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,pdf,doc,docx|max:10240',
            'skill' => 'nullable|array|min:1',
            'skill.*' => 'string|max:64',
            'primary_link' => 'nullable|url|max:255',
            'secondary_link' => 'nullable|url|max:255',
            // Work fields it shows if type == work
            'location' => 'nullable|string|max:64',
            'salary_range' => 'nullable|string|max:50',
            'work_place' => 'nullable|string|max:33',
            'contact' => 'nullable|string|max:15',
            'hours' => 'nullable|string|max:20',
        ];
    }
}
