<?php

namespace App\Support;

use Illuminate\Support\Str;

class StoryUrl
{
    /**
     * Reduce a link to the form used to spot duplicate stories.
     *
     * Lowercases the scheme and host, drops a default port and the fragment, and removes
     * utm_* parameters plus the extra tracking parameters listed in news.tracking_params.
     * Other query parameters keep their order and encoding. A link that is not an
     * absolute http(s) url is returned trimmed and otherwise untouched.
     */
    public static function normalize(string $url): string
    {
        $url = trim($url);
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return $url;
        }

        $scheme = strtolower($parts['scheme']);
        $userInfo = isset($parts['user']) ? $parts['user'].(isset($parts['pass']) ? ':'.$parts['pass'] : '').'@' : '';
        $defaultPort = ['http' => 80, 'https' => 443][$scheme] ?? null;
        $port = isset($parts['port']) && $parts['port'] !== $defaultPort ? ':'.$parts['port'] : '';
        $path = ($parts['path'] ?? '') === '' ? '/' : $parts['path'];
        $query = self::withoutTrackingParameters($parts['query'] ?? '');

        return $scheme.'://'.$userInfo.strtolower($parts['host']).$port.$path.($query === '' ? '' : '?'.$query);
    }

    /**
     * The scheme, host, and path of a normalized url, used to find candidate duplicates in the database.
     */
    public static function withoutQuery(string $normalizedUrl): string
    {
        return Str::before($normalizedUrl, '?');
    }

    private static function withoutTrackingParameters(string $query): string
    {
        $trackingParameters = array_map('strtolower', config('news.tracking_params', []));

        $kept = array_filter(explode('&', $query), function (string $pair) use ($trackingParameters): bool {
            $name = strtolower(urldecode(Str::before($pair, '=')));

            return $pair !== '' && ! str_starts_with($name, 'utm_') && ! in_array($name, $trackingParameters, true);
        });

        return implode('&', $kept);
    }
}
