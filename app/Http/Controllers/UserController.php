<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function show(User $user): View
    {
        return view('users.show', ['user' => $user]);
    }

    public function submissions(Request $request, User $user): View
    {
        return view('stories.index', [
            'title' => $user->username."'s submissions",
            'stories' => $user->stories()->newest()->forListing($request->user())->simplePaginate(StoryController::PER_PAGE, pageName: 'p'),
        ]);
    }

    public function comments(Request $request, User $user): View
    {
        return view('comments.index', [
            'title' => $user->username."'s comments",
            'comments' => $user->comments()
                ->with('user', 'story')
                ->withVoteBy($request->user())
                ->latest()
                ->orderByDesc('id')
                ->simplePaginate(StoryController::PER_PAGE, pageName: 'p'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate(['about' => ['nullable', 'string', 'max:5000']]);

        $request->user()->update($validated);

        return to_route('users.show', $request->user());
    }
}
