<?php

namespace App\Services\Wp;

use DOMElement;
use DOMXPath;

class PultopWpConditionParser
{
    /**
     * @return array{label: string, value: ?string, note: ?string, enabled: ?bool}
     */
    public static function parseItem(DOMElement $item, DOMXPath $xpath): ?array
    {
        $labelNode = $xpath->query('.//*[contains(@class,"item-detail-item-label")]', $item)->item(0);
        $label = $labelNode !== null ? PultopWpDom::normalizeText($labelNode->textContent) : '';
        if ($label === '') {
            return null;
        }

        $valueNode = $xpath->query('.//*[contains(@class,"item-detail-item-text")]', $item)->item(0);
        $value = $valueNode !== null ? PultopWpDom::normalizeText($valueNode->textContent) : null;
        if ($value === '') {
            $value = null;
        }

        $note = null;
        $smallNode = $xpath->query('.//*[contains(@class,"item-detail-item-value")]//small', $item)->item(0);
        if ($smallNode === null) {
            $smallNode = $xpath->query('.//small', $item)->item(0);
        }
        if ($smallNode !== null) {
            $noteText = PultopWpDom::normalizeText($smallNode->textContent);
            if ($noteText !== '') {
                $note = $noteText;
            }
        }

        $enabled = self::detectEnabled($item, $xpath);

        if ($value === null && $enabled !== null) {
            $value = $enabled ? '✓' : '—';
        }

        return [
            'label' => $label,
            'value' => $value,
            'note' => $note,
            'enabled' => $enabled,
        ];
    }

    public static function detectEnabled(DOMElement $item, DOMXPath $xpath): ?bool
    {
        $class = mb_strtolower($item->getAttribute('class'));
        if (str_contains($class, 'disabled') || str_contains($class, 'is-off') || str_contains($class, 'no-')) {
            return false;
        }
        if (str_contains($class, 'enabled') || str_contains($class, 'is-on') || str_contains($class, 'yes')) {
            return true;
        }

        $icons = $xpath->query(
            './/*[contains(@class,"fa-") or contains(@class,"bi-") or contains(@class,"icon") or contains(@class,"text-success") or contains(@class,"text-danger")]',
            $item,
        );
        if ($icons !== false) {
            foreach ($icons as $icon) {
                if (! $icon instanceof DOMElement) {
                    continue;
                }
                $iconClass = mb_strtolower($icon->getAttribute('class'));
                if (str_contains($iconClass, 'dash')
                    || str_contains($iconClass, 'times')
                    || str_contains($iconClass, 'close')
                    || str_contains($iconClass, 'ban')
                    || str_contains($iconClass, 'minus')
                    || str_contains($iconClass, 'text-danger')) {
                    return false;
                }
                if (str_contains($iconClass, 'check')
                    || str_contains($iconClass, 'ok')
                    || str_contains($iconClass, 'plus')
                    || str_contains($iconClass, 'text-success')) {
                    return true;
                }
            }
        }

        $html = mb_strtolower($item->ownerDocument?->saveHTML($item) ?? '');
        if (str_contains($html, 'bi-dash') || str_contains($html, 'text-danger')) {
            return false;
        }
        if (str_contains($html, 'bi-check') || str_contains($html, 'text-success')) {
            return true;
        }

        return null;
    }

    public static function looksEnabled(?string $value): bool
    {
        if ($value === null || trim($value) === '' || $value === '✓') {
            return true;
        }
        if ($value === '—' || $value === '-' || $value === '–') {
            return false;
        }

        $lower = mb_strtolower($value);

        if (str_contains($lower, 'не предусмотр')
            || str_contains($lower, 'без капитализац')
            || str_contains($lower, 'без пролонгац')
            || preg_match('/^нет\b/u', $lower) === 1) {
            return false;
        }

        return true;
    }
}
