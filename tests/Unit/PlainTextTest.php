<?php

use App\Support\PlainText;

it('escapes html', function () {
    expect((string) PlainText::format('<script>alert(1)</script>'))
        ->toBe('<p>&lt;script&gt;alert(1)&lt;/script&gt;</p>');
});

it('splits paragraphs on blank lines', function () {
    expect((string) PlainText::format("One\n\nTwo"))->toBe("<p>One</p>\n<p>Two</p>");
});

it('autolinks http urls without trailing punctuation', function () {
    expect((string) PlainText::format('See https://example.ca/a?b=1&c=2.'))
        ->toBe('<p>See <a href="https://example.ca/a?b=1&amp;c=2" rel="nofollow noreferrer">https://example.ca/a?b=1&amp;c=2</a>.</p>');
});

it('does not let a url break out of its attribute', function () {
    expect((string) PlainText::format('https://example.ca/"onmouseover="alert(1)'))
        ->toBe('<p><a href="https://example.ca/" rel="nofollow noreferrer">https://example.ca/</a>&quot;onmouseover=&quot;alert(1)</p>');
});

it('leaves javascript urls as text', function () {
    expect((string) PlainText::format('javascript:alert(1)'))->toBe('<p>javascript:alert(1)</p>');
});
