<?php

use App\Enums\Province;

return [

    /*
    |--------------------------------------------------------------------------
    | News Fetcher
    |--------------------------------------------------------------------------
    |
    | The news:fetch command reads the feeds below and submits new stories as
    | the poster account. The account must already exist; the command never
    | creates it. Usernames match case-insensitively, as they do at login.
    |
    */

    'poster_username' => env('NEWS_POSTER_USERNAME', 'mbd'),

    'max_stories_per_run' => (int) env('NEWS_MAX_STORIES_PER_RUN', 5),

    'max_stories_per_feed' => (int) env('NEWS_MAX_STORIES_PER_FEED', 2),

    'max_age_hours' => (int) env('NEWS_MAX_AGE_HOURS', 48),

    'timeout_seconds' => (int) env('NEWS_TIMEOUT_SECONDS', 10),

    /*
    | CBC's CDN resets connections for some User-Agents, including Guzzle's
    | default and agents that contain a "+https://..." contact URL.
    */

    'user_agent' => env('NEWS_USER_AGENT', 'cobrachicken/1.0'),

    /*
    | Query parameters removed from links before stories are compared for
    | duplicates, in addition to every utm_* parameter. CBC adds cmp=rss.
    */

    'tracking_params' => ['cmp'],

    /*
    |--------------------------------------------------------------------------
    | Feeds
    |--------------------------------------------------------------------------
    |
    | Each feed is an RSS 2.0 or Atom url with a display name, an optional
    | Province case that tags its stories, and a politics flag. When the same
    | link shows up in several feeds, the story gets the politics tag if any
    | of them has it, and a province only if those feeds agree on one. (CBC
    | Ottawa, for one, also carries Quebec stories.)
    |
    */

    'feeds' => [
        ['name' => 'CBC Top Stories', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-topstories', 'province' => null, 'politics' => false],
        ['name' => 'CBC Canada', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada', 'province' => null, 'politics' => false],
        ['name' => 'CBC Politics', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-politics', 'province' => null, 'politics' => true],
        ['name' => 'CBC British Columbia', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-britishcolumbia', 'province' => Province::BritishColumbia, 'politics' => false],
        ['name' => 'CBC Calgary', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-calgary', 'province' => Province::Alberta, 'politics' => false],
        ['name' => 'CBC Edmonton', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-edmonton', 'province' => Province::Alberta, 'politics' => false],
        ['name' => 'CBC Saskatchewan', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-saskatchewan', 'province' => Province::Saskatchewan, 'politics' => false],
        ['name' => 'CBC Manitoba', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-manitoba', 'province' => Province::Manitoba, 'politics' => false],
        ['name' => 'CBC Toronto', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-toronto', 'province' => Province::Ontario, 'politics' => false],
        ['name' => 'CBC Ottawa', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-ottawa', 'province' => Province::Ontario, 'politics' => false],
        ['name' => 'CBC Montreal', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-montreal', 'province' => Province::Quebec, 'politics' => false],
        ['name' => 'CBC New Brunswick', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-newbrunswick', 'province' => Province::NewBrunswick, 'politics' => false],
        ['name' => 'CBC Nova Scotia', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-novascotia', 'province' => Province::NovaScotia, 'politics' => false],
        ['name' => 'CBC P.E.I.', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-pei', 'province' => Province::PrinceEdwardIsland, 'politics' => false],
        ['name' => 'CBC Newfoundland and Labrador', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-newfoundland', 'province' => Province::NewfoundlandAndLabrador, 'politics' => false],
        ['name' => 'CBC North', 'url' => 'https://www.cbc.ca/webfeed/rss/rss-canada-north', 'province' => null, 'politics' => false],
        ['name' => 'CTV News Canada', 'url' => 'https://www.ctvnews.ca/arc/outboundfeeds/rss/category/canada/?outputType=xml', 'province' => null, 'politics' => false],
        ['name' => 'CTV News Politics', 'url' => 'https://www.ctvnews.ca/arc/outboundfeeds/rss/category/politics/?outputType=xml', 'province' => null, 'politics' => true],
        ['name' => 'Global News Canada', 'url' => 'https://globalnews.ca/canada/feed/', 'province' => null, 'politics' => false],
        ['name' => 'Global News Politics', 'url' => 'https://globalnews.ca/politics/feed/', 'province' => null, 'politics' => true],
        ['name' => 'Nunatsiaq News', 'url' => 'https://nunatsiaq.com/feed/', 'province' => Province::Nunavut, 'politics' => false],
        ['name' => 'Cabin Radio', 'url' => 'https://cabinradio.ca/feed/', 'province' => Province::NorthwestTerritories, 'politics' => false],
        ['name' => 'Yukon News', 'url' => 'https://www.yukon-news.com/feed/', 'province' => Province::Yukon, 'politics' => false],
        ['name' => 'APTN News', 'url' => 'https://www.aptnnews.ca/feed/', 'province' => null, 'politics' => false],
        ['name' => 'The Narwhal', 'url' => 'https://thenarwhal.ca/feed/', 'province' => null, 'politics' => false],
        ['name' => 'The Tyee', 'url' => 'https://thetyee.ca/rss2.xml', 'province' => null, 'politics' => false],
        ['name' => 'Canada\'s National Observer', 'url' => 'https://www.nationalobserver.com/front/rss', 'province' => null, 'politics' => false],
    ],

];
