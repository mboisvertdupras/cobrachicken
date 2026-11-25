<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use InvalidArgumentException;
use SimpleXMLElement;
use Throwable;

class FeedParser
{
    private const ATOM_NAMESPACE = 'http://www.w3.org/2005/Atom';

    private const DUBLIN_CORE_NAMESPACE = 'http://purl.org/dc/elements/1.1/';

    /**
     * Read the items of an RSS 2.0 or Atom document.
     *
     * Titles come back as raw text (entities and tags not yet removed). An item with no
     * readable date gets a null published_at.
     *
     * @return list<array{title: string, url: string, published_at: ?CarbonImmutable}>
     *
     * @throws InvalidArgumentException when the document is not XML or not a feed.
     */
    public function parse(string $xml): array
    {
        $usedInternalErrors = libxml_use_internal_errors(true);

        try {
            $document = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NOCDATA | LIBXML_NONET);
            $error = libxml_get_last_error();
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($usedInternalErrors);
        }

        if ($document === false) {
            throw new InvalidArgumentException('The feed is not valid XML'.($error ? ': '.trim($error->message) : '.'));
        }

        return match ($document->getName()) {
            'rss' => $this->rssItems($document),
            'feed' => $this->atomEntries($document),
            default => throw new InvalidArgumentException("The feed has an unknown root element <{$document->getName()}>."),
        };
    }

    /**
     * @return list<array{title: string, url: string, published_at: ?CarbonImmutable}>
     */
    private function rssItems(SimpleXMLElement $document): array
    {
        $items = [];

        foreach ($document->channel->item ?? [] as $item) {
            $url = trim((string) $item->link);

            if ($url === '' && isset($item->guid) && strtolower((string) $item->guid['isPermaLink']) !== 'false') {
                $url = trim((string) $item->guid);
            }

            $items[] = [
                'title' => (string) $item->title,
                'url' => $url,
                'published_at' => $this->date((string) $item->pubDate ?: (string) $item->children(self::DUBLIN_CORE_NAMESPACE)->date),
            ];
        }

        return $items;
    }

    /**
     * @return list<array{title: string, url: string, published_at: ?CarbonImmutable}>
     */
    private function atomEntries(SimpleXMLElement $document): array
    {
        $items = [];

        foreach ($document->children(self::ATOM_NAMESPACE)->entry as $entry) {
            $items[] = [
                'title' => (string) $entry->title,
                'url' => $this->atomLink($entry),
                'published_at' => $this->date((string) $entry->published ?: (string) $entry->updated),
            ];
        }

        return $items;
    }

    private function atomLink(SimpleXMLElement $entry): string
    {
        foreach ($entry->link as $link) {
            $attributes = $link->attributes();

            if (in_array((string) $attributes->rel, ['', 'alternate'], true)) {
                return trim((string) $attributes->href);
            }
        }

        return '';
    }

    private function date(string $value): ?CarbonImmutable
    {
        if (trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse(trim($value));
        } catch (Throwable) {
            return null;
        }
    }
}
