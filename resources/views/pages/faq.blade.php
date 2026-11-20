<x-layout title="FAQ">
    <div class="max-w-2xl space-y-3 text-sm leading-relaxed text-stone-700 [&_a]:text-red-700 [&_a]:underline">
        <h2 class="text-xl font-semibold text-stone-900">Frequently Asked Questions</h2>

        <h3 class="pt-3 font-semibold text-stone-900">What is cobrachicken?</h3>
        <p>cobrachicken is a community news site for Canada. Readers submit links and questions about Canadian news, vote on what they find worth reading, and discuss it in the comments.</p>

        <h3 class="pt-3 font-semibold text-stone-900">Why the name?</h3>
        <p>"Cobra chicken" is the Canadian nickname for the Canada goose. It is loud, stubborn, and everywhere, which is also a fair description of a good comment thread.</p>

        <h3 class="pt-3 font-semibold text-stone-900">Which provinces and territories are covered?</h3>
        <p>All of them. A story can be tagged with one of these, or left untagged as national news:</p>
        <ul class="grid list-inside list-disc grid-cols-1 gap-x-6 sm:grid-cols-2">
            @foreach (App\Enums\Province::cases() as $province)
                <li>{{ $province->label() }} ({{ $province->value }})</li>
            @endforeach
        </ul>

        <h3 class="pt-3 font-semibold text-stone-900">Where do the stories come from?</h3>
        <p>Readers submit them. Anyone with an account can <a href="{{ route('stories.create') }}">submit</a> a link, or leave the url blank to ask a question for discussion.</p>

        <h3 class="pt-3 font-semibold text-stone-900">How does politics tagging work?</h3>
        <p>When you submit a story, you can tick the politics box. Political stories show a small "politics" tag. On the front page, untick "show politics" to hide them. The setting is saved in a cookie in your browser. Political stories still appear on the new, ask, and province pages.</p>

        <h3 class="pt-3 font-semibold text-stone-900">Can I filter by province?</h3>
        <p>Yes. The <a href="{{ route('provinces.index') }}">provinces</a> page links to the stories for each province and territory. You can also click the province tag next to any story.</p>

        <h3 class="pt-3 font-semibold text-stone-900">Is there an RSS feed?</h3>
        <p>Yes. The front page is available at <a href="{{ route('rss') }}">/rss</a>.</p>

        <h3 class="pt-3 font-semibold text-stone-900">How are stories ranked?</h3>
        <p>The front page uses the same idea as Hacker News. Each upvote pulls a story up. Age pulls it down, and the pull gets stronger the older the story is. A new story with a few votes will usually sit above a day-old story with more votes. The <a href="{{ route('newest') }}">new</a> page skips ranking and lists stories by submission time.</p>

        <h3 class="pt-3 font-semibold text-stone-900">How does moderation work?</h3>
        <p>The site owner removes spam and abuse. Please submit the original source when you can, use the article's own title, and keep comments civil.</p>

        <h3 class="pt-3 font-semibold text-stone-900">Is it open source?</h3>
        <p>Yes. The code is on GitHub at <a href="https://github.com/mboisvertdupras/cobrachicken">github.com/mboisvertdupras/cobrachicken</a> under the MIT license.</p>
    </div>
</x-layout>
