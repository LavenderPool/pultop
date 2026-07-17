<?php

namespace App\Services\Wp;

use DOMDocument;
use DOMElement;
use DOMXPath;

class PultopWpDom
{
    /** @var list<string> */
    private const RESERVED_BANK_PATHS = [
        'deposits', 'credits', 'cards', 'banks', 'articles', 'category', 'gold-stat',
        'kurs-obmena-valyut', 'credit-type', 'wp-admin', 'wp-content', 'wp-json',
    ];

    public static function xpath(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new DOMXPath($dom);
    }

    public static function normalizeText(?string $text): string
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    public static function normalizeHtml(string $html): string
    {
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/\s+/u', ' ', $html) ?? $html;
        $html = preg_replace('/\s*<p>\s*<\/p>\s*/iu', '', $html) ?? $html;

        return trim($html);
    }

    public static function firstElement(DOMXPath $xpath, string $expression, ?DOMElement $context = null): ?DOMElement
    {
        $nodes = $context !== null
            ? $xpath->query($expression, $context)
            : $xpath->query($expression);

        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        $node = $nodes->item(0);

        return $node instanceof DOMElement ? $node : null;
    }

    public static function absoluteUrl(string $baseUrl, string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $baseUrl = rtrim($baseUrl, '/');

        if (str_starts_with($url, '/')) {
            return $baseUrl.$url;
        }

        return $baseUrl.'/'.$url;
    }

    public static function slugFromSectionHref(string $href, string $section): ?string
    {
        if ($href === '') {
            return null;
        }

        $path = parse_url($href, PHP_URL_PATH);
        if (! is_string($path)) {
            return null;
        }

        $section = trim($section, '/');
        if (! preg_match('#/'.preg_quote($section, '#').'/([^/]+)/?#', $path, $m)) {
            return null;
        }

        $slug = $m[1];
        if ($slug === '' || $slug === 'page') {
            return null;
        }

        return $slug;
    }

    public static function slugFromBankHref(string $href): ?string
    {
        if ($href === '') {
            return null;
        }

        $path = parse_url($href, PHP_URL_PATH) ?? $href;
        if (! is_string($path)) {
            return null;
        }

        if (preg_match('#/banks/([^/]+)/?#', $path, $m)) {
            return $m[1] !== '' && $m[1] !== 'page' ? $m[1] : null;
        }

        return self::slugFromLooseBankHref($href);
    }

    public static function slugFromLooseBankHref(string $href): ?string
    {
        if ($href === '') {
            return null;
        }

        $path = parse_url($href, PHP_URL_PATH) ?? $href;
        if (! is_string($path)) {
            return null;
        }

        $path = trim($path, '/');
        if ($path === '' || str_contains($path, '/')) {
            return null;
        }

        if (in_array($path, self::RESERVED_BANK_PATHS, true)) {
            return null;
        }

        return $path;
    }
}
