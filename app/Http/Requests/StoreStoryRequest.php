<?php

namespace App\Http\Requests;

use App\Actions\SubmitStory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStoryRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return SubmitStory::rules();
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
