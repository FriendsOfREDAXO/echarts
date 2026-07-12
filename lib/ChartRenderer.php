<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\ECharts;

use rex_escape;

final class ChartRenderer
{
    /**
     * @param array<string, mixed> $options
     * @param array<string, string> $attributes
     */
    public static function render(array $options, string|int $height = 360, array $attributes = []): string
    {
        $json = json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($json) || $json === '') {
            return '';
        }

        $base64 = base64_encode($json);
        $style = 'height:' . self::normalizeHeight($height) . ';';
        $class = 'rex-echarts-chart';

        if (isset($attributes['class']) && $attributes['class'] !== '') {
            $class .= ' ' . trim($attributes['class']);
            unset($attributes['class']);
        }

        if (isset($attributes['style']) && $attributes['style'] !== '') {
            $style .= $attributes['style'];
            unset($attributes['style']);
        }

        $htmlAttrs = [];
        foreach ($attributes as $key => $value) {
            if ($value === '') {
                continue;
            }
            $htmlAttrs[] = rex_escape($key) . '="' . rex_escape($value) . '"';
        }

        return '<div class="' . rex_escape($class) . '" style="' . rex_escape($style) . '" data-echarts-options="' . rex_escape($base64) . '"'
            . ($htmlAttrs !== [] ? ' ' . implode(' ', $htmlAttrs) : '') . '></div>';
    }

    private static function normalizeHeight(string|int $height): string
    {
        if (is_int($height)) {
            return max(120, $height) . 'px';
        }

        $trimmed = trim($height);
        if ($trimmed === '') {
            return '360px';
        }

        if (preg_match('/^\d+$/', $trimmed) === 1) {
            return $trimmed . 'px';
        }

        return $trimmed;
    }
}
