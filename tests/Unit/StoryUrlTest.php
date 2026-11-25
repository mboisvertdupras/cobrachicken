<?php

use App\Support\StoryUrl;
use Tests\TestCase;

uses(TestCase::class);

it('drops utm parameters, configured tracking parameters, and fragments', function () {
    config(['news.tracking_params' => ['cmp']]);

    expect(StoryUrl::normalize('https://www.cbc.ca/news/a-9.1?cmp=rss'))->toBe('https://www.cbc.ca/news/a-9.1')
        ->and(StoryUrl::normalize('https://example.ca/a?utm_source=rss&id=7&UTM_Medium=x#top'))->toBe('https://example.ca/a?id=7')
        ->and(StoryUrl::normalize('https://example.ca/a#top'))->toBe('https://example.ca/a');
});

it('lowercases the scheme and host but not the path', function () {
    expect(StoryUrl::normalize(' HTTPS://Example.CA/Some/Path '))->toBe('https://example.ca/Some/Path');
});

it('drops default ports and adds a root path', function () {
    expect(StoryUrl::normalize('https://example.ca:443'))->toBe('https://example.ca/')
        ->and(StoryUrl::normalize('http://example.ca:8080/a'))->toBe('http://example.ca:8080/a');
});

it('keeps other query parameters in order', function () {
    expect(StoryUrl::normalize('https://example.ca/?b=2&a=1&utm_x=3'))->toBe('https://example.ca/?b=2&a=1');
});

it('leaves non-urls alone', function () {
    expect(StoryUrl::normalize('not a url'))->toBe('not a url');
});
