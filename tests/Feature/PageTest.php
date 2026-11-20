<?php

use App\Models\Story;

it('renders the faq with every province', function () {
    $this->get('/faq')->assertOk()->assertSee('Canada goose')->assertSee('Nunavut')->assertSee('Prince Edward Island');
});

it('renders the about page', function () {
    $this->get('/about')->assertOk()->assertSee('About cobrachicken');
});

it('serves the top stories as rss', function () {
    $story = Story::factory()->create(['title' => 'Leafs & Habs tonight']);

    $response = $this->get('/rss')->assertOk()->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');

    expect(simplexml_load_string($response->getContent())->channel->item->title->__toString())->toBe($story->title);
});

it('links the rss feed from the layout', function () {
    $this->get('/')->assertSee('type="application/rss+xml"', false);
});
