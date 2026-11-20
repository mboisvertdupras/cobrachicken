<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class PlainText
{
    private const URL_PATTERN = '~(https?://[^\s<>"\']*[^\s<>"\'.,;:!?)\]])~i';

    /**
     * Escape user text, split paragraphs on blank lines, and autolink http(s) URLs.
     */
    public static function format(string $text): HtmlString
    {
        $paragraphs = preg_split('/\R\s*\R/', trim($text));

        $html = collect($paragraphs)
            ->map(fn (string $paragraph): string => '<p>'.self::linkify($paragraph).'</p>')
            ->implode("\n");

        return new HtmlString($html);
    }

    private static function linkify(string $paragraph): string
    {
        $parts = preg_split(self::URL_PATTERN, $paragraph, -1, PREG_SPLIT_DELIM_CAPTURE);

        return collect($parts)
            ->map(fn (string $part, int $index): string => $index % 2 === 1
                ? '<a href="'.e($part).'" rel="nofollow noreferrer">'.e($part).'</a>'
                : e($part))
            ->implode('');
    }
}
