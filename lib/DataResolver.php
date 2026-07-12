<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\ECharts;

use rex_sql;

final class DataResolver
{
    /**
    * @param mixed $items
        * @return list<array{name: string, value: float, color?: string, link?: string, link_target?: string, link_rel?: string, tooltip?: string}>
     */
    public static function fromManualItems(mixed $items): array
    {
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($items)) {
            return [];
        }

        $rows = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? $item['name'] ?? ''));
            $rawValue = trim((string) ($item['value'] ?? ''));
            if ($label === '' || !is_numeric($rawValue)) {
                continue;
            }

            $row = ['name' => $label, 'value' => (float) $rawValue];

            $hasColor = in_array((string) ($item['has_color'] ?? '0'), ['1', 'true', 'on', 'yes'], true);
            if ($hasColor) {
                $rawColor = trim((string) ($item['color'] ?? ''));
                if (preg_match('/^#[0-9a-fA-F]{6}$/', $rawColor) === 1) {
                    $row['color'] = strtolower($rawColor);
                }
            }

            $hasLink = in_array((string) ($item['has_link'] ?? '0'), ['1', 'true', 'on', 'yes'], true);
            if ($hasLink) {
                $linkMeta = self::resolveManualLinkMeta($item['link'] ?? ($item['smart_link'] ?? ''));
                if ($linkMeta !== null) {
                    $row['link'] = $linkMeta['href'];
                    $row['link_target'] = $linkMeta['target'];
                    $row['link_rel'] = $linkMeta['rel'];
                }
            }

            $tooltipText = trim((string) ($item['tooltip'] ?? ''));
            if ($tooltipText !== '') {
                $row['tooltip'] = $tooltipText;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @return list<array{name: string, value: float, color?: string, link?: string, link_target?: string, link_rel?: string, tooltip?: string}>
     */
    public static function fromCsv(string $csv, string $delimiter = ',', bool $hasHeader = true): array
    {
        $trimmed = trim($csv);
        if ($trimmed === '') {
            return [];
        }

        $sep = match ($delimiter) {
            ';' => ';',
            'tab' => "\t",
            default => ',',
        };

        $lines = preg_split('/\r\n|\r|\n/', $trimmed);
        if (!is_array($lines) || $lines === []) {
            return [];
        }

        $headers = [];
        $startIndex = 0;

        if ($hasHeader) {
            $headerRow = str_getcsv((string) $lines[0], $sep);
            if (is_array($headerRow)) {
                foreach ($headerRow as $index => $headerCell) {
                    $headers[$index] = self::normalizeCsvHeader((string) $headerCell);
                }
            }
            $startIndex = 1;
        }

        $rows = [];
        for ($i = $startIndex; $i < count($lines); ++$i) {
            $line = (string) $lines[$i];
            if (trim($line) === '') {
                continue;
            }

            $cells = str_getcsv($line, $sep);
            if (!is_array($cells) || $cells === []) {
                continue;
            }

            $data = [];
            if ($hasHeader && $headers !== []) {
                foreach ($cells as $idx => $cell) {
                    $key = $headers[$idx] ?? ('col_' . $idx);
                    $data[$key] = trim((string) $cell);
                }
            } else {
                $data['label'] = trim((string) ($cells[0] ?? ''));
                $data['value'] = trim((string) ($cells[1] ?? ''));
                $data['link'] = trim((string) ($cells[2] ?? ''));
                $data['tooltip'] = trim((string) ($cells[3] ?? ''));
                $data['color'] = trim((string) ($cells[4] ?? ''));
                $data['has_link'] = trim((string) ($cells[5] ?? ''));
                $data['has_color'] = trim((string) ($cells[6] ?? ''));
            }

            $label = trim((string) ($data['label'] ?? $data['name'] ?? $data['titel'] ?? ''));
            $rawValue = trim((string) ($data['value'] ?? $data['wert'] ?? ''));
            if ($label === '' || !is_numeric($rawValue)) {
                continue;
            }

            $row = ['name' => $label, 'value' => (float) $rawValue];

            $rawTooltip = trim((string) ($data['tooltip'] ?? $data['hint'] ?? ''));
            if ($rawTooltip !== '') {
                $row['tooltip'] = $rawTooltip;
            }

            $hasColorRaw = strtolower(trim((string) ($data['has_color'] ?? '')));
            $hasColor = $hasColorRaw === '' || in_array($hasColorRaw, ['1', 'true', 'on', 'yes', 'ja'], true);
            if ($hasColor) {
                $rawColor = trim((string) ($data['color'] ?? ''));
                if (preg_match('/^#[0-9a-fA-F]{6}$/', $rawColor) === 1) {
                    $row['color'] = strtolower($rawColor);
                }
            }

            $hasLinkRaw = strtolower(trim((string) ($data['has_link'] ?? '')));
            $hasLink = $hasLinkRaw === '' || in_array($hasLinkRaw, ['1', 'true', 'on', 'yes', 'ja'], true);
            if ($hasLink) {
                $linkMeta = self::resolveManualLinkMeta($data['link'] ?? ($data['url'] ?? ''));
                if ($linkMeta !== null) {
                    $row['link'] = $linkMeta['href'];
                    $row['link_target'] = $linkMeta['target'];
                    $row['link_rel'] = $linkMeta['rel'];
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
    * @return list<array{name: string, value: float, link?: string, tooltip?: string}>
     */
    public static function fromManual(string $manual): array
    {
        $rows = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($manual));
        if (!is_array($lines)) {
            return $rows;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = explode('|', $line);
            if (count($parts) < 2) {
                continue;
            }

            $label = trim((string) $parts[0]);
            $value = trim((string) $parts[1]);
            if ($label === '' || !is_numeric($value)) {
                continue;
            }

            $row = ['name' => $label, 'value' => (float) $value];

            $rawLink = isset($parts[2]) ? trim((string) $parts[2]) : '';
            $resolvedLink = self::resolveManualLink($rawLink);
            if ($resolvedLink !== '') {
                $row['link'] = $resolvedLink;
            }

            $tooltipText = isset($parts[3]) ? trim((string) $parts[3]) : '';
            if ($tooltipText !== '') {
                $row['tooltip'] = $tooltipText;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @return list<array{name: string, value: float}>
     */
    public static function fromYform(string $table, string $labelField, string $valueField, int $limit = 12): array
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return [];
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $labelField)) {
            return [];
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $valueField)) {
            return [];
        }

        $limit = max(1, min(200, $limit));

        $sql = rex_sql::factory();
        try {
            $sql->setQuery(
                'SELECT ' . $labelField . ' AS label_col, ' . $valueField . ' AS value_col FROM ' . $table . ' ORDER BY id DESC LIMIT ' . $limit
            );
        } catch (\Throwable) {
            return [];
        }

        $rows = [];
        foreach ($sql->getArray() as $row) {
            $label = trim((string) ($row['label_col'] ?? ''));
            $value = (string) ($row['value_col'] ?? '');
            if ($label === '' || !is_numeric($value)) {
                continue;
            }
            $rows[] = ['name' => $label, 'value' => (float) $value];
        }

        return array_reverse($rows);
    }

    private static function resolveManualLink(string $rawLink): string
    {
        if ($rawLink === '') {
            return '';
        }

        // Numeric input is treated as REDAXO article id.
        if (preg_match('/^\d+$/', $rawLink) === 1) {
            $articleId = (int) $rawLink;
            if ($articleId > 0 && function_exists('rex_getUrl')) {
                return (string) rex_getUrl($articleId);
            }

            return '';
        }

        // Direct external links are allowed for explicit URLs.
        if (preg_match('/^https:\/\//i', $rawLink) === 1) {
            return $rawLink;
        }

        return '';
    }

    private static function normalizeCsvHeader(string $header): string
    {
        $key = strtolower(trim($header));
        $key = str_replace([' ', '-', '.'], '_', $key);

        return match ($key) {
            'name', 'titel' => 'label',
            'wert' => 'value',
            'url' => 'link',
            'hinweis' => 'tooltip',
            'farbe' => 'color',
            default => $key,
        };
    }

    /**
     * @param mixed $rawLink
     */
    /**
     * @param mixed $rawLink
     * @return array{href: string, target: string, rel: string}|null
     */
    private static function resolveManualLinkMeta(mixed $rawLink): ?array
    {
        if (class_exists(\FriendsOfREDAXO\Builder\SmartLinkView::class)) {
            try {
                $resolved = \FriendsOfREDAXO\Builder\SmartLinkView::resolveSingle($rawLink);
                if (is_array($resolved) && isset($resolved['href']) && is_string($resolved['href'])) {
                    $href = trim($resolved['href']);
                    if ($href !== '') {
                        $isExternal = isset($resolved['is_external']) && (bool) $resolved['is_external'];

                        return [
                            'href' => $href,
                            'target' => $isExternal ? '_blank' : '_self',
                            'rel' => $isExternal ? 'noopener noreferrer' : '',
                        ];
                    }
                }
            } catch (\Throwable) {
                // Fall through to legacy parser.
            }
        }

        if (is_string($rawLink)) {
            $href = self::resolveManualLink(trim($rawLink));
            if ($href !== '') {
                $isExternal = preg_match('/^https:\/\//i', $href) === 1;

                return [
                    'href' => $href,
                    'target' => $isExternal ? '_blank' : '_self',
                    'rel' => $isExternal ? 'noopener noreferrer' : '',
                ];
            }
        }

        return null;
    }
}
