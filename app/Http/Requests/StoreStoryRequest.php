<?php

namespace App\Http\Requests;

use App\Enums\Province;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStoryRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:80'],
            'url' => ['nullable', 'required_without:text', 'url:http,https', 'max:2048'],
            'text' => ['nullable', 'required_without:url', 'string', 'max:10000'],
            'province' => ['nullable', Rule::enum(Province::class)],
            'is_political' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'url.required_without' => 'Please submit a url, some text, or both.',
            'text.required_without' => 'Please submit a url, some text, or both.',
        ];
    }
}
