<?php

namespace App\Http\Controllers;

use App\Enums\Province;
use App\Http\Requests\StoreStoryRequest;
use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StoryController extends Controller
{
    public const PER_PAGE = 30;

    public function index(Request $request): View
    {
        $showPolitics = $request->cookie('show_politics') !== '0';

        return view('stories.index', [
            'stories' => Story::ranked()
                ->showingPolitics($showPolitics)
                ->forListing($request->user())
                ->simplePaginate(self::PER_PAGE, pageName: 'p'),
            'showPolitics' => $showPolitics,
        ]);
    }

    public function newest(Request $request): View
    {
        return view('stories.index', [
            'title' => 'New',
            'stories' => Story::newest()->forListing($request->user())->simplePaginate(self::PER_PAGE, pageName: 'p'),
        ]);
    }

    public function ask(Request $request): View
    {
        return view('stories.index', [
            'title' => 'Ask',
            'stories' => Story::ask()->ranked()->forListing($request->user())->simplePaginate(self::PER_PAGE, pageName: 'p'),
        ]);
    }

    public function show(Request $request, Story $story): View
    {
        $story->load('user')->loadExists(['votes as voted' => fn ($votes) => $votes->where('user_id', $request->user()?->id)]);

        $comments = $story->comments()
            ->with('user')
            ->withVoteBy($request->user())
            ->orderByDesc('points')
            ->oldest()
            ->get();

        return view('stories.show', [
            'story' => $story,
            'commentsByParent' => $comments->groupBy(fn ($comment) => $comment->parent_id ?? 0),
        ]);
    }

    public function create(): View
    {
        return view('stories.create', ['provinces' => Province::cases()]);
    }

    public function store(StoreStoryRequest $request): RedirectResponse
    {
        $request->user()->stories()->create([
            ...$request->validated(),
            'is_political' => $request->boolean('is_political'),
        ]);

        return to_route('newest');
    }

    public function rss(): Response
    {
        return response()
            ->view('stories.rss', ['stories' => Story::ranked()->with('user')->limit(self::PER_PAGE)->get()])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
