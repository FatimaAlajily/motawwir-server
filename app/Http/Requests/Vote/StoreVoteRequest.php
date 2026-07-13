<?php

namespace App\Http\Requests\Vote;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
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
            'type'       => 'required|in:post,comment',
            'post_id'    => 'required_if:type,post|prohibited_if:type,comment|exists:posts,id',
            'comment_id' => 'required_if:type,comment|prohibited_if:type,post|exists:comments,id',
            'custom'       => 'required|in:upvote,downvote,ai',
        ];
    }
}
