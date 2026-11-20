{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ route('home') }}</link>
        <description>Canadian news, submitted and ranked by readers.</description>
        @foreach ($stories as $story)
            <item>
                <title>{{ $story->title }}</title>
                <link>{{ $story->link() }}</link>
                <pubDate>{{ $story->created_at->toRssString() }}</pubDate>
                <comments>{{ route('stories.show', $story) }}</comments>
                <guid isPermaLink="true">{{ route('stories.show', $story) }}</guid>
                <description><![CDATA[<a href="{{ route('stories.show', $story) }}">Comments</a>]]></description>
            </item>
        @endforeach
    </channel>
</rss>
