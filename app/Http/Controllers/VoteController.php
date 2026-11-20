<?php

namespace App\Http\Controllers;

use App\Actions\ToggleVote;
use App\Http\Requests\VoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class VoteController extends Controller
{
    public function store(VoteRequest $request, ToggleVote $toggleVote): JsonResponse|RedirectResponse
    {
        return $this->vote($request, $toggleVote, true);
    }

    public function destroy(VoteRequest $request, ToggleVote $toggleVote): JsonResponse|RedirectResponse
    {
        return $this->vote($request, $toggleVote, false);
    }

    private function vote(VoteRequest $request, ToggleVote $toggleVote, bool $up): JsonResponse|RedirectResponse
    {
        $votable = $request->votable();
        $toggleVote->handle($request->user(), $votable, $up);

        if ($request->expectsJson()) {
            return response()->json(['points' => $votable->points, 'voted' => $up]);
        }

        return back();
    }
}
