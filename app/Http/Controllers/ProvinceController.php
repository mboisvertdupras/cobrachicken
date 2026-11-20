<?php

namespace App\Http\Controllers;

use App\Enums\Province;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProvinceController extends Controller
{
    public function index(): View
    {
        return view('provinces.index', ['provinces' => Province::cases()]);
    }

    public function show(Request $request, Province $province): View
    {
        return view('stories.index', [
            'title' => $province->label(),
            'stories' => Story::where('province', $province)
                ->ranked()
                ->forListing($request->user())
                ->simplePaginate(StoryController::PER_PAGE, pageName: 'p'),
        ]);
    }
}
