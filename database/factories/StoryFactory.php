<?php

namespace Database\Factories;

use App\Enums\Province;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Story>
 */
class StoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => Str::limit(rtrim(fake()->sentence(6), '.'), 80, '', preserveWords: true),
            'url' => 'https://example.ca/'.fake()->slug(4),
            'text' => null,
            'province' => null,
            'is_political' => false,
        ];
    }

    public function link(): static
    {
        return $this->state(fn (): array => [
            'url' => fake()->randomElement(['https://example.ca/', 'https://news.example.com/']).fake()->slug(4),
            'text' => null,
        ]);
    }

    public function ask(): static
    {
        return $this->state(fn (): array => [
            'url' => null,
            'text' => fake()->paragraphs(2, true),
        ]);
    }

    public function political(): static
    {
        return $this->state(['is_political' => true]);
    }

    public function province(?Province $province = null): static
    {
        return $this->state(fn (): array => [
            'province' => $province ?? fake()->randomElement(Province::cases()),
        ]);
    }
}
