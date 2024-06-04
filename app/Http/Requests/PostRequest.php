<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string",
            "slug" => "required|string|unique:posts,slug",
            "description" => "required|string",
            "status" => "required|in:draft,published",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg",
            "user_id" => "required|exists:users,id",
        ];
    }
}
