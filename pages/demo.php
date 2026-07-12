<?php

use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\DataResolver;
use FriendsOfREDAXO\ECharts\DemoDataManager;
use FriendsOfREDAXO\ECharts\PresetFactory;

$demoDataToken = rex_csrf_token::factory('echarts_demo_data');
$demoDataMessage = '';

if (rex_addon::get('yform')->isAvailable()) {
    $demoAction = rex_post('echarts_demo_action', 'string', '');
    if ($demoAction !== '' && $demoDataToken->isValid()) {
        try {
            if ($demoAction === 'install') {
                DemoDataManager::install();
                $demoDataMessage = rex_view::success(rex_i18n::msg('echarts_demo_data_installed'));
            } elseif ($demoAction === 'remove') {
                DemoDataManager::remove();
                $demoDataMessage = rex_view::success(rex_i18n::msg('echarts_demo_data_removed'));
            }
        } catch (Throwable $exception) {
            $demoDataMessage = rex_view::error(rex_i18n::msg('echarts_demo_data_error') . ': ' . $exception->getMessage());
        }
    }
}

$revenueRows = [
    ['name' => 'Jan', 'value' => 120],
    ['name' => 'Feb', 'value' => 160],
    ['name' => 'Mär', 'value' => 185],
    ['name' => 'Apr', 'value' => 170],
    ['name' => 'Mai', 'value' => 220],
    ['name' => 'Jun', 'value' => 260],
];

$channelsRows = [
    ['name' => 'SEO', 'value' => 42],
    ['name' => 'SEA', 'value' => 27],
    ['name' => 'Newsletter', 'value' => 18],
    ['name' => 'Direkt', 'value' => 13],
];

$scatterRows = [
    ['name' => 'P1', 'value' => 5.2],
    ['name' => 'P2', 'value' => 8.5],
    ['name' => 'P3', 'value' => 3.9],
    ['name' => 'P4', 'value' => 9.8],
    ['name' => 'P5', 'value' => 7.1],
    ['name' => 'P6', 'value' => 6.4],
];

echo '<div class="alert alert-info" style="margin-bottom:16px">' . rex_i18n::msg('echarts_demo_intro') . '</div>';

if (rex_addon::get('yform')->isAvailable()) {
    $installed = DemoDataManager::isInstalled();
    $statusLabel = $installed
        ? rex_i18n::msg('echarts_demo_data_status_installed')
        : rex_i18n::msg('echarts_demo_data_status_not_installed');

    if ($demoDataMessage !== '') {
        echo $demoDataMessage;
    }

    echo '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">'
        . rex_i18n::msg('echarts_demo_data_title')
        . '</h3></div><div class="panel-body">';
    echo '<p>' . rex_i18n::msg('echarts_demo_data_help') . '</p>';
    echo '<p><strong>' . rex_i18n::msg('echarts_demo_data_status') . ':</strong> ' . rex_escape($statusLabel) . '</p>';
    echo '<p class="help-block">'
        . rex_escape(DemoDataManager::getTableName())
        . ' · '
        . rex_i18n::msg('echarts_demo_data_builder_hint')
        . '</p>';
    echo '<form method="post" style="display:inline-block;margin-right:8px">';
    echo $demoDataToken->getHiddenField();
    echo '<input type="hidden" name="echarts_demo_action" value="install">';
    echo '<button type="submit" class="btn btn-primary">' . rex_i18n::msg('echarts_demo_data_install') . '</button>';
    echo '</form>';
    echo '<form method="post" style="display:inline-block">';
    echo $demoDataToken->getHiddenField();
    echo '<input type="hidden" name="echarts_demo_action" value="remove">';
    echo '<button type="submit" class="btn btn-default">' . rex_i18n::msg('echarts_demo_data_remove') . '</button>';
    echo '</form>';
    echo '</div></div>';

    if ($installed) {
        $yformRows = DataResolver::fromYform(DemoDataManager::getTableName(), 'name', 'value', 50);
        if ($yformRows !== []) {
            echo '<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">Live-Chart aus YForm-Daten</h3></div><div class="panel-body">';
            echo '<p class="help-block">Diese Vorschau liest direkt aus <code>' . rex_escape(DemoDataManager::getTableName()) . '</code> (Felder: <code>name</code>, <code>value</code>).</p>';
            echo ChartRenderer::render(PresetFactory::fromType('bar', $yformRows, 'YForm Live-Daten'), 360);
            echo '</div></div>';
        }
    }
}

echo '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Gängige Chart-Typen</h3></div><div class="panel-body">';

echo '<div class="row" style="margin-bottom:20px">';
echo '<div class="col-md-6">';
echo '<h4>Balken</h4>';
echo ChartRenderer::render(PresetFactory::fromType('bar', $revenueRows, 'Umsatz 1. Halbjahr'), 320);
echo '</div>';
echo '<div class="col-md-6">';
echo '<h4>Linie / Fläche</h4>';
echo ChartRenderer::render(PresetFactory::fromType('area', $revenueRows, 'Trend mit Fläche'), 320);
echo '</div>';
echo '</div>';

echo '<div class="row" style="margin-bottom:20px">';
echo '<div class="col-md-6">';
echo '<h4>Pie / Donut</h4>';
echo ChartRenderer::render(PresetFactory::fromType('donut', $channelsRows, 'Kanäle Anteil'), 320);
echo '</div>';
echo '<div class="col-md-6">';
echo '<h4>Scatter</h4>';
echo ChartRenderer::render(PresetFactory::fromType('scatter', $scatterRows, 'Messpunkte'), 320);
echo '</div>';
echo '</div>';

echo '</div></div>';

echo '<div class="panel panel-info"><div class="panel-heading"><h3 class="panel-title">Verwendung im Modul-Output</h3></div><div class="panel-body">';
echo '<pre style="font-size:12px">';
$moduleSnippet = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'Q1', 'value' => 120],
    ['name' => 'Q2', 'value' => 180],
    ['name' => 'Q3', 'value' => 150],
    ['name' => 'Q4', 'value' => 210],
];

echo ChartRenderer::render(
    PresetFactory::fromType('bar', $rows, 'Quartale'),
    360
);
PHP;
echo rex_escape($moduleSnippet);
echo '</pre>';
echo '</div></div>';

echo '<div class="panel panel-success"><div class="panel-heading"><h3 class="panel-title">YForm Value und Builder</h3></div><div class="panel-body">';
echo '<p>YForm-Value: <code>value|echarts_option|chart_json|Chart|360|0</code></p>';
echo '<p>Builder-Element: <strong>ECharts</strong> (manuelle Daten oder YForm-Tabelle, plus JSON-Override).</p>';
echo '</div></div>';
