<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Story;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:story,comment'],
            'id' => ['required', 'integer'],
        ];
    }

    public function votable(): Story|Comment
    {
        return Relation::getMorphedModel($this->validated('type'))::findOrFail($this->validated('id'));
    }
}
