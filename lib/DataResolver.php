<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\ECharts;

use rex_sql;

final class DataResolver
{
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
}
