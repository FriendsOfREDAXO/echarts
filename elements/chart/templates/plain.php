<?php

use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\DataResolver;
use FriendsOfREDAXO\ECharts\PresetFactory;

$headline = trim((string) ($elementData['headline'] ?? ''));
$chartTitle = trim((string) ($elementData['chart_title'] ?? ''));
$chartType = trim((string) ($elementData['chart_type'] ?? 'bar'));
$height = trim((string) ($elementData['height'] ?? '380'));
$sourceType = trim((string) ($elementData['source_type'] ?? 'manual'));
$manualData = (string) ($elementData['manual_data'] ?? '');
$manualColors = (string) ($elementData['manual_colors'] ?? '');
$yformTable = trim((string) ($elementData['yform_table'] ?? ''));
$yformLabelField = trim((string) ($elementData['yform_label_field'] ?? 'name'));
$yformValueField = trim((string) ($elementData['yform_value_field'] ?? 'value'));
$yformLimit = (int) ($elementData['yform_limit'] ?? 12);
$showLegend = in_array((string) ($elementData['show_legend'] ?? '1'), ['1', 'true', 'on', 'yes'], true);
$showTooltip = in_array((string) ($elementData['show_tooltip'] ?? '1'), ['1', 'true', 'on', 'yes'], true);
$showLabels = in_array((string) ($elementData['show_labels'] ?? ''), ['1', 'true', 'on', 'yes'], true);
$showGrid = in_array((string) ($elementData['show_grid'] ?? '1'), ['1', 'true', 'on', 'yes'], true);
$optionsJson = trim((string) ($elementData['chart_options_json'] ?? ''));

if ($sourceType === 'yform') {
    $rows = DataResolver::fromYform($yformTable, $yformLabelField, $yformValueField, $yformLimit);
} else {
    $rows = DataResolver::fromManual($manualData);
}

$options = [];
if ($optionsJson !== '') {
    try {
        $decoded = json_decode($optionsJson, true, 512, JSON_THROW_ON_ERROR);
        if (is_array($decoded)) {
            $options = $decoded;
        }
    } catch (Throwable) {
        $options = [];
    }
}

if ($options === []) {
    if ($rows === []) {
        return;
    }

    $palette = PresetFactory::parsePalette($manualColors);
    $options = PresetFactory::fromType($chartType, $rows, $chartTitle, $showLegend, $palette, $showTooltip, $showLabels, $showGrid);
}
?>
<div class="echarts-element echarts-element-plain">
    <?php if ($headline !== ''): ?>
        <h2><?= rex_escape($headline) ?></h2>
    <?php endif; ?>
    <?= ChartRenderer::render($options, $height) ?>
</div>
