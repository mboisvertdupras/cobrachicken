<x-layout title="FAQ">
    <div class="page">
        <h2>Frequently Asked Questions</h2>

        <p><b>What is cobrachicken?</b></p>
        <p>cobrachicken is a community news site for Canada. Readers submit links and questions about Canadian news, vote on what they find worth reading, and discuss it in the comments.</p>

        <p><b>Why the name?</b></p>
        <p>"Cobra chicken" is the Canadian nickname for the Canada goose. It is loud, stubborn, and everywhere, which is also a fair description of a good comment thread.</p>

        <p><b>Which provinces and territories are covered?</b></p>
        <p>All of them. A story can be tagged with one of these, or left untagged as national news:</p>
        <ul>
            @foreach (App\Enums\Province::cases() as $province)
                <li>{{ $province->label() }} ({{ $province->value }})</li>
            @endforeach
        </ul>

        <p><b>Where do the stories come from?</b></p>
        <p>Readers submit them. Anyone with an account can <a href="{{ route('stories.create') }}">submit</a> a link, or leave the url blank to ask a question for discussion.</p>

        <p><b>How does politics tagging work?</b></p>
        <p>When you submit a story, you can tick the politics box. Political stories show a small "politics" tag. On the front page, untick "show politics" to hide them. The setting is saved in a cookie in your browser. Political stories still appear on the new, ask, and province pages.</p>

        <p><b>Can I filter by province?</b></p>
        <p>Yes. The <a href="{{ route('provinces.index') }}">provinces</a> page links to the stories for each province and territory. You can also click the province tag next to any story.</p>

        <p><b>Is there an RSS feed?</b></p>
        <p>Yes. The front page is available at <a href="{{ route('rss') }}">/rss</a>.</p>

        <p><b>How are stories ranked?</b></p>
        <p>The front page uses the same idea as Hacker News. Each upvote pulls a story up. Age pulls it down, and the pull gets stronger the older the story is. A new story with a few votes will usually sit above a day-old story with more votes. The <a href="{{ route('newest') }}">new</a> page skips ranking and lists stories by submission time.</p>

        <p><b>How does moderation work?</b></p>
        <p>The site owner removes spam and abuse. Please submit the original source when you can, use the article's own title, and keep comments civil.</p>

        <p><b>Is it open source?</b></p>
        <p>Yes. The code is on GitHub at <a href="https://github.com/mboisvertdupras/cobrachicken">github.com/mboisvertdupras/cobrachicken</a> under the MIT license.</p>
    </div>
</x-layout>
