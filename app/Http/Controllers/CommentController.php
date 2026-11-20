<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        return view('comments.index', [
            'title' => 'Comments',
            'comments' => Comment::with('user', 'story')
                ->withVoteBy($request->user())
                ->latest()
                ->orderByDesc('id')
                ->simplePaginate(StoryController::PER_PAGE, pageName: 'p'),
        ]);
    }

    public function store(StoreCommentRequest $request, Story $story): RedirectResponse|Response
    {
        $comment = $story->comments()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        if ($request->ajax()) {
            return response()->view('comments._comment', [
                'comment' => $comment->load('user'),
                'commentsByParent' => collect(),
            ], 201);
        }

        return to_route('stories.show', $story);
    }
}
