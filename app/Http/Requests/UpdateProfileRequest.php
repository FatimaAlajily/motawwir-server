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
}
