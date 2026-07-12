<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\ECharts;

final class PresetFactory
{
    /**
     * @param list<array{name: string, value: float|int|string}> $rows
     * @param list<string> $palette
     * @return array<string, mixed>
     */
    public static function fromType(
        string $type,
        array $rows,
        string $title = '',
        bool $showLegend = true,
        array $palette = [],
        bool $showTooltip = true,
        bool $showLabels = false,
        bool $showGrid = true
    ): array {
        $effectivePalette = self::resolvePalette($palette, count($rows));

        return match ($type) {
            'line' => self::line($rows, $title, false, $showLegend, $effectivePalette, $showTooltip, $showLabels, $showGrid),
            'area' => self::line($rows, $title, true, $showLegend, $effectivePalette, $showTooltip, $showLabels, $showGrid),
            'pie' => self::pie($rows, $title, false, $showLegend, $effectivePalette, $showTooltip, $showLabels),
            'donut' => self::pie($rows, $title, true, $showLegend, $effectivePalette, $showTooltip, $showLabels),
            'scatter' => self::scatter($rows, $title, $effectivePalette, $showTooltip, $showLabels, $showGrid),
            default => self::bar($rows, $title, $showLegend, $effectivePalette, $showTooltip, $showLabels, $showGrid),
        };
    }

    /**
     * @return list<string>
     */
    public static function parsePalette(string $manualColors): array
    {
        $colors = [];
        $lines = preg_split('/\r\n|\r|\n/', $manualColors);
        if (!is_array($lines)) {
            return [];
        }

        foreach ($lines as $line) {
            $color = trim($line);
            if ($color === '') {
                continue;
            }

            // Allow common CSS color notations.
            if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) === 1
                || preg_match('/^(rgb|rgba|hsl|hsla)\(.+\)$/', $color) === 1
                || preg_match('/^var\(--[a-zA-Z0-9_-]+\)$/', $color) === 1
                || preg_match('/^[a-zA-Z]+$/', $color) === 1) {
                $colors[] = $color;
            }
        }

        return $colors;
    }

    /**
     * @param mixed $items
     * @return list<string>
     */
    public static function parsePaletteItems(mixed $items): array
    {
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($items)) {
            return [];
        }

        $lines = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $color = trim((string) ($item['color'] ?? $item['value'] ?? ''));
            if ($color !== '') {
                $lines[] = $color;
            }
        }

        return self::parsePalette(implode("\n", $lines));
    }

    /**
     * @param list<array{name: string, value: float|int|string}> $rows
     * @param list<string> $palette
     * @return array<string, mixed>
     */
    public static function bar(
        array $rows,
        string $title = '',
        bool $showLegend = true,
        array $palette = [],
        bool $showTooltip = true,
        bool $showLabels = false,
        bool $showGrid = true
    ): array {
        $categoryLabels = array_map(
            static fn (array $row): string => (string) ($row['name'] ?? ''),
            $rows
        );

        $series = [];
        $paletteSize = count($palette);
        foreach ($rows as $index => $row) {
            $data = array_fill(0, count($rows), null);

            $value = (float) ($row['value'] ?? 0);
            $point = ['value' => $value];
            if (isset($row['link']) && is_string($row['link']) && $row['link'] !== '') {
                $point['link'] = $row['link'];
                if (isset($row['link_target']) && is_string($row['link_target']) && $row['link_target'] !== '') {
                    $point['link_target'] = $row['link_target'];
                }
                if (isset($row['link_rel']) && is_string($row['link_rel']) && $row['link_rel'] !== '') {
                    $point['link_rel'] = $row['link_rel'];
                }
            }
            if (isset($row['tooltip']) && is_string($row['tooltip']) && $row['tooltip'] !== '') {
                $point['tooltip'] = $row['tooltip'];
            }
            $data[$index] = $point;

            $seriesItem = [
                'name' => (string) ($row['name'] ?? ''),
                'type' => 'bar',
                'stack' => 'echarts-bar-single',
                'data' => $data,
                'label' => ['show' => $showLabels, 'position' => 'top'],
            ];

            if ($paletteSize > 0) {
                $seriesItem['itemStyle'] = [
                    'color' => $palette[$index % $paletteSize],
                ];
            }

            if (isset($row['color']) && is_string($row['color']) && preg_match('/^#[0-9a-fA-F]{6}$/', $row['color']) === 1) {
                $seriesItem['itemStyle'] = [
                    'color' => strtolower($row['color']),
                ];
            }

            $series[] = $seriesItem;
        }

        $options = [
            'title' => ['text' => $title],
            'tooltip' => ['show' => $showTooltip, 'trigger' => 'axis'],
            'legend' => ['show' => $showLegend, 'data' => $categoryLabels],
            'xAxis' => ['type' => 'category', 'data' => $categoryLabels],
            'yAxis' => ['type' => 'value', 'splitLine' => ['show' => $showGrid]],
            'series' => $series,
        ];

        if ($palette !== []) {
            $options['color'] = $palette;
        }

        return $options;
    }

    /**
     * @param list<array{name: string, value: float|int|string}> $rows
     * @param list<string> $palette
     * @return array<string, mixed>
     */
    public static function line(
        array $rows,
        string $title = '',
        bool $area = false,
        bool $showLegend = true,
        array $palette = [],
        bool $showTooltip = true,
        bool $showLabels = false,
        bool $showGrid = true
    ): array {
        $series = [
            'name' => self::seriesNameFromTitle($title, 'Wert'),
            'type' => 'line',
            'smooth' => true,
            'data' => self::numericValues($rows),
            'label' => ['show' => $showLabels],
        ];

        if ($area) {
            $series['areaStyle'] = [];
        }

        $lineData = [];
        foreach ($rows as $row) {
            $item = ['value' => (float) ($row['value'] ?? 0)];
            if (isset($row['color']) && is_string($row['color']) && preg_match('/^#[0-9a-fA-F]{6}$/', $row['color']) === 1) {
                $item['itemStyle'] = ['color' => strtolower($row['color'])];
            }
            if (isset($row['link']) && is_string($row['link']) && $row['link'] !== '') {
                $item['link'] = $row['link'];
                if (isset($row['link_target']) && is_string($row['link_target']) && $row['link_target'] !== '') {
                    $item['link_target'] = $row['link_target'];
                }
                if (isset($row['link_rel']) && is_string($row['link_rel']) && $row['link_rel'] !== '') {
                    $item['link_rel'] = $row['link_rel'];
                }
            }
            if (isset($row['tooltip']) && is_string($row['tooltip']) && $row['tooltip'] !== '') {
                $item['tooltip'] = $row['tooltip'];
            }
            $lineData[] = $item;
        }

        $series['data'] = $lineData;

        $options = [
            'title' => ['text' => $title],
            'tooltip' => ['show' => $showTooltip, 'trigger' => 'axis'],
            'legend' => ['show' => $showLegend],
            'xAxis' => ['type' => 'category', 'data' => array_column($rows, 'name')],
            'yAxis' => ['type' => 'value', 'splitLine' => ['show' => $showGrid]],
            'series' => [$series],
        ];

        if ($palette !== []) {
            $options['color'] = $palette;
        }

        return $options;
    }

    /**
     * @param list<array{name: string, value: float|int|string}> $rows
     * @param list<string> $palette
     * @return array<string, mixed>
     */
    public static function pie(
        array $rows,
        string $title = '',
        bool $donut = false,
        bool $showLegend = true,
        array $palette = [],
        bool $showTooltip = true,
        bool $showLabels = false
    ): array {
        $radius = $donut ? ['45%', '70%'] : '70%';

        $options = [
            'title' => ['text' => $title, 'left' => 'center'],
            'tooltip' => ['show' => $showTooltip, 'trigger' => 'item'],
            'legend' => ['show' => $showLegend, 'bottom' => 0],
            'series' => [[
                'name' => 'Anteile',
                'type' => 'pie',
                'radius' => $radius,
                'label' => ['show' => $showLabels],
                'data' => array_map(static function (array $row): array {
                    $item = [
                        'name' => (string) $row['name'],
                        'value' => (float) $row['value'],
                    ];
                    if (isset($row['color']) && is_string($row['color']) && preg_match('/^#[0-9a-fA-F]{6}$/', $row['color']) === 1) {
                        $item['itemStyle'] = ['color' => strtolower($row['color'])];
                    }
                    if (isset($row['link']) && is_string($row['link']) && $row['link'] !== '') {
                        $item['link'] = $row['link'];
                        if (isset($row['link_target']) && is_string($row['link_target']) && $row['link_target'] !== '') {
                            $item['link_target'] = $row['link_target'];
                        }
                        if (isset($row['link_rel']) && is_string($row['link_rel']) && $row['link_rel'] !== '') {
                            $item['link_rel'] = $row['link_rel'];
                        }
                    }
                    if (isset($row['tooltip']) && is_string($row['tooltip']) && $row['tooltip'] !== '') {
                        $item['tooltip'] = $row['tooltip'];
                    }

                    return $item;
                }, $rows),
            ]],
        ];

        if ($palette !== []) {
            $options['color'] = $palette;
        }

        return $options;
    }

    /**
     * @param list<array{name: string, value: float|int|string}> $rows
     * @param list<string> $palette
     * @return array<string, mixed>
     */
    public static function scatter(
        array $rows,
        string $title = '',
        array $palette = [],
        bool $showTooltip = true,
        bool $showLabels = false,
        bool $showGrid = true
    ): array {
        $points = [];
        foreach ($rows as $index => $row) {
            $item = ['value' => [$index + 1, (float) $row['value']]];
            if (isset($row['color']) && is_string($row['color']) && preg_match('/^#[0-9a-fA-F]{6}$/', $row['color']) === 1) {
                $item['itemStyle'] = ['color' => strtolower($row['color'])];
            }
            if (isset($row['link']) && is_string($row['link']) && $row['link'] !== '') {
                $item['link'] = $row['link'];
                if (isset($row['link_target']) && is_string($row['link_target']) && $row['link_target'] !== '') {
                    $item['link_target'] = $row['link_target'];
                }
                if (isset($row['link_rel']) && is_string($row['link_rel']) && $row['link_rel'] !== '') {
                    $item['link_rel'] = $row['link_rel'];
                }
            }
            if (isset($row['tooltip']) && is_string($row['tooltip']) && $row['tooltip'] !== '') {
                $item['tooltip'] = $row['tooltip'];
            }
            $points[] = $item;
        }

        $options = [
            'title' => ['text' => $title],
            'tooltip' => ['show' => $showTooltip, 'trigger' => 'item'],
            'xAxis' => ['type' => 'value', 'name' => 'Index', 'splitLine' => ['show' => $showGrid]],
            'yAxis' => ['type' => 'value', 'name' => 'Wert', 'splitLine' => ['show' => $showGrid]],
            'series' => [[
                'name' => self::seriesNameFromTitle($title, 'Wert'),
                'type' => 'scatter',
                'data' => $points,
                'symbolSize' => 12,
                'label' => ['show' => $showLabels, 'formatter' => '{@[1]}'],
            ]],
        ];

        if ($palette !== []) {
            $options['color'] = $palette;
        }

        return $options;
    }

    /**
     * @param list<array{name: string, value: float|int|string}> $rows
     * @return list<float>
     */
    private static function numericValues(array $rows): array
    {
        return array_map(static fn (array $row): float => (float) $row['value'], $rows);
    }

    private static function seriesNameFromTitle(string $title, string $fallback): string
    {
        $resolved = trim($title);

        return $resolved !== '' ? $resolved : $fallback;
    }

    /**
     * @return list<string>
     */
    private static function defaultPalette(): array
    {
        return ['#5470c6', '#91cc75', '#fac858', '#ee6666', '#73c0de', '#3ba272', '#fc8452', '#9a60b4', '#ea7ccc'];
    }

    /**
     * @param list<string> $palette
     * @return list<string>
     */
    private static function resolvePalette(array $palette, int $rowCount): array
    {
        if ($palette === []) {
            return self::defaultPalette();
        }

        if (count($palette) === 1) {
            $targetSize = max(3, $rowCount);
            $mono = self::monochromePaletteFromHex($palette[0], $targetSize);
            if ($mono !== []) {
                return $mono;
            }
        }

        return $palette;
    }

    /**
     * @return list<string>
     */
    private static function monochromePaletteFromHex(string $color, int $count): array
    {
        $hex = self::normalizeHexColor($color);
        if ($hex === null) {
            return [];
        }

        $r = hexdec(substr($hex, 1, 2));
        $g = hexdec(substr($hex, 3, 2));
        $b = hexdec(substr($hex, 5, 2));

        $result = [];
        if ($count <= 1) {
            return [$hex];
        }

        // Relative luminance (0..1). We widen the gradient for very dark/light base colors.
        $luminance = ((0.2126 * $r) + (0.7152 * $g) + (0.0722 * $b)) / 255;

        $minFactor = -0.28;
        $maxFactor = 0.56;

        if ($luminance <= 0.12) {
            // Very dark colors (e.g. black): only lighten strongly so shades are clearly visible.
            $minFactor = 0.12;
            $maxFactor = 0.88;
        } elseif ($luminance >= 0.88) {
            // Very light colors (e.g. white): only darken strongly so shades are clearly visible.
            $minFactor = -0.88;
            $maxFactor = -0.12;
        }

        for ($i = 0; $i < $count; ++$i) {
            $ratio = $i / ($count - 1);
            $factor = $minFactor + (($maxFactor - $minFactor) * $ratio);
            $result[] = self::adjustRgbLightness($r, $g, $b, $factor);
        }

        return $result;
    }

    private static function normalizeHexColor(string $color): ?string
    {
        $trimmed = trim($color);
        if (preg_match('/^#[0-9a-fA-F]{3}$/', $trimmed) === 1) {
            $r = $trimmed[1];
            $g = $trimmed[2];
            $b = $trimmed[3];

            return '#' . $r . $r . $g . $g . $b . $b;
        }

        if (preg_match('/^#[0-9a-fA-F]{6}$/', $trimmed) === 1) {
            return strtolower($trimmed);
        }

        return null;
    }

    private static function adjustRgbLightness(int $r, int $g, int $b, float $factor): string
    {
        if ($factor >= 0) {
            $nr = (int) round($r + ((255 - $r) * $factor));
            $ng = (int) round($g + ((255 - $g) * $factor));
            $nb = (int) round($b + ((255 - $b) * $factor));
        } else {
            $nr = (int) round($r * (1 + $factor));
            $ng = (int) round($g * (1 + $factor));
            $nb = (int) round($b * (1 + $factor));
        }

        $nr = max(0, min(255, $nr));
        $ng = max(0, min(255, $ng));
        $nb = max(0, min(255, $nb));

        return sprintf('#%02x%02x%02x', $nr, $ng, $nb);
    }
}
