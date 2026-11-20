<?php

namespace Database\Seeders;

use App\Actions\ToggleVote;
use App\Enums\Province;
use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * @var list<array{0: string, 1: ?Province, 2: bool}>
     */
    private const LINK_STORIES = [
        ['Calgary council approves new transit line to the airport', Province::Alberta, true],
        ['Oilers sign rookie defenceman to three-year extension', Province::Alberta, false],
        ['Chinook winds push Lethbridge to 18 degrees in February', Province::Alberta, false],
        ['Vancouver housing starts hit a ten-year high', Province::BritishColumbia, false],
        ['B.C. legislature debates new short-term rental rules', Province::BritishColumbia, true],
        ['Atmospheric river brings record rain to the Fraser Valley', Province::BritishColumbia, false],
        ['Blue Bombers clinch home playoff game at Princess Auto Stadium', Province::Manitoba, false],
        ['Winnipeg opens the Red River skating trail early this year', Province::Manitoba, false],
        ['Fredericton tech startup raises seed round for forestry sensors', Province::NewBrunswick, false],
        ['Moncton adds bike lanes along Main Street', Province::NewBrunswick, false],
        ['St. John\'s digs out after another nor\'easter', Province::NewfoundlandAndLabrador, false],
        ['Newfoundland premier announces offshore wind consultation', Province::NewfoundlandAndLabrador, true],
        ['Halifax ferry service adds evening crossings', Province::NovaScotia, false],
        ['Nova Scotia lobster season opens with strong prices', Province::NovaScotia, false],
        ['Yellowknife ice road opens two weeks late', Province::NorthwestTerritories, false],
        ['Iqaluit gets a second grocery store', Province::Nunavut, false],
        ['Nunavut legislature passes new housing bill', Province::Nunavut, true],
        ['Toronto city council votes on Gardiner Expressway repairs', Province::Ontario, true],
        ['Leafs win in overtime, extend streak to six', Province::Ontario, false],
        ['Ottawa\'s Rideau Canal Skateway opens for the season', Province::Ontario, false],
        ['Ontario announces new rules for highway speed limits', Province::Ontario, true],
        ['Charlottetown harbour redevelopment gets green light', Province::PrinceEdwardIsland, false],
        ['P.E.I. potato harvest larger than expected', Province::PrinceEdwardIsland, false],
        ['Montreal metro blue line extension reaches new milestone', Province::Quebec, false],
        ['Quebec City winter carnival breaks attendance record', Province::Quebec, false],
        ['National Assembly debates changes to French-language law', Province::Quebec, true],
        ['Roughriders fans pack Mosaic Stadium for the Labour Day Classic', Province::Saskatchewan, false],
        ['Saskatoon tests a four-day work week for city staff', Province::Saskatchewan, false],
        ['Whitehorse records coldest night in a decade', Province::Yukon, false],
        ['Dawson City prepares for the Yukon Quest', Province::Yukon, false],
        ['Bank of Canada holds its key interest rate', null, false],
        ['House of Commons passes the budget implementation act', null, true],
        ['Federal government announces new national park in the North', null, true],
        ['Grey Cup preview: what to watch this weekend', null, false],
        ['Environment Canada issues winter outlook for the Prairies', null, false],
        ['Statistics Canada: population grows past 41 million', null, false],
        ['Canada Post strike talks resume', null, true],
        ['VIA Rail orders new trains for the Quebec City-Windsor corridor', null, false],
        ['Canadian dollar climbs on oil prices', null, false],
        ['Senate committee reviews online news act changes', null, true],
    ];

    /**
     * @var list<array{0: string, 1: ?Province}>
     */
    private const ASK_STORIES = [
        ['Ask: Best winter tires for driving in the Prairies?', Province::Saskatchewan],
        ['Ask: Moving from Toronto to Halifax, what should I know?', Province::NovaScotia],
        ['Ask: Which provinces have the best public library systems?', null],
        ['Ask: How do you deal with geese on your running route?', null],
        ['Ask: Good places to watch the northern lights near Yellowknife?', Province::NorthwestTerritories],
        ['Ask: Is it worth getting a heat pump in Montreal?', Province::Quebec],
        ['Ask: What is your favourite small town in B.C.?', Province::BritishColumbia],
        ['Ask: CFL or NHL, which do you follow more and why?', null],
        ['Ask: Tips for a first winter in Winnipeg?', Province::Manitoba],
        ['Ask: How are your provincial health wait times lately?', null],
        ['Ask: Best poutine outside of Quebec?', null],
        ['Ask: Anyone commute by ferry in Vancouver or Victoria?', Province::BritishColumbia],
        ['Ask: What local news sources do you trust in Alberta?', Province::Alberta],
        ['Ask: How do you follow federal politics without burning out?', null],
        ['Ask: Recommendations for camping in Newfoundland?', Province::NewfoundlandAndLabrador],
        ['Ask: What changed most in your city in the last ten years?', null],
        ['Ask: Best way to learn French as an adult in Ottawa?', Province::Ontario],
        ['Ask: Which Canadian podcasts do you listen to?', null],
        ['Ask: How bad was the snow where you are this week?', null],
        ['Ask: Favourite road trip route in the Maritimes?', Province::NewBrunswick],
    ];

    private const COMMENT_LINES = [
        'This has been a long time coming. Curious to see how it plays out over the next year.',
        'I live nearby and can confirm the traffic has been rough.',
        'Does anyone have the original report? The article does not link to it.',
        'Same thing happened in my town a few years ago. It worked out better than expected.',
        'The comments on the local paper were wild for this one.',
        'Good. More of this, please.',
        'I am not convinced the numbers add up, but I hope I am wrong.',
        'Watch out for the geese near the river path, they are nesting again.',
        'There is a longer write-up here: https://example.ca/longer-write-up',
        'My family has been there for three generations and this is the first time I have seen it.',
        'Agreed. The details matter more than the headline though.',
        'Anyone know if this applies to the territories too?',
    ];

    public function run(): void
    {
        $toggleVote = new ToggleVote;

        $users = User::factory(10)->create()->push(
            User::factory()->create(['username' => 'demo', 'email' => 'demo@example.com']),
        );

        $stories = collect(self::LINK_STORIES)
            ->map(fn (array $story): Story => Story::factory()->link()->for($users->random())->create([
                'title' => $story[0],
                'url' => fake()->randomElement(['https://example.ca/', 'https://news.example.com/']).Str::slug($story[0]),
                'province' => $story[1],
                'is_political' => $story[2],
                'created_at' => now()->subMinutes(fake()->numberBetween(5, 5 * 24 * 60)),
            ]))
            ->merge(collect(self::ASK_STORIES)->map(fn (array $story): Story => Story::factory()->ask()->for($users->random())->create([
                'title' => $story[0],
                'province' => $story[1],
                'created_at' => now()->subMinutes(fake()->numberBetween(5, 5 * 24 * 60)),
            ])));

        foreach ($stories as $story) {
            $this->voteFromOthers($toggleVote, $story, $users, fake()->numberBetween(0, 9));
            $this->seedThread($toggleVote, $story, $users, null, 0);
        }
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function voteFromOthers(ToggleVote $toggleVote, Story|Comment $votable, Collection $users, int $count): void
    {
        $users->reject(fn (User $user): bool => $user->id === $votable->user_id)
            ->shuffle()
            ->take($count)
            ->each(fn (User $voter) => $toggleVote->handle($voter, $votable, true));
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function seedThread(ToggleVote $toggleVote, Story $story, Collection $users, ?Comment $parent, int $depth): void
    {
        $count = match ($depth) {
            0 => fake()->numberBetween(0, 4),
            1 => fake()->numberBetween(0, 2),
            2 => fake()->numberBetween(0, 1),
            default => 0,
        };

        $after = $parent?->created_at ?? $story->created_at;

        for ($i = 0; $i < $count; $i++) {
            $comment = Comment::factory()->for($story)->for($users->random())->create([
                'parent_id' => $parent?->id,
                'body' => fake()->randomElement(self::COMMENT_LINES),
                'created_at' => fake()->dateTimeBetween($after, 'now'),
            ]);

            $this->voteFromOthers($toggleVote, $comment, $users, fake()->numberBetween(0, 3));
            $this->seedThread($toggleVote, $story, $users, $comment, $depth + 1);
        }
    }
}
