<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id'])]
class Vote extends Model
{
    const UPDATED_AT = null;

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }
}
