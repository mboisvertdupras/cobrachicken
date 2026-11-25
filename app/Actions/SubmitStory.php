<?php

namespace App\Actions;

use App\Enums\Province;
use App\Models\Story;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class SubmitStory
{
    /**
     * The rules every story submission must pass, from the submit form or the news fetcher.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function rules(): array
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
     * Create the story. The Votable trait records the author's own vote.
     *
     * @param  array{title: string, url?: ?string, text?: ?string, province?: ?string, is_political: bool}  $attributes
     */
    public function handle(User $user, array $attributes): Story
    {
        return $user->stories()->create($attributes);
    }
}
