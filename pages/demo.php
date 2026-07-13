<?php

declare(strict_types=1);

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

$months = [
    rex_i18n::msg('echarts_demo_month_jan'),
    rex_i18n::msg('echarts_demo_month_feb'),
    rex_i18n::msg('echarts_demo_month_mar'),
    rex_i18n::msg('echarts_demo_month_apr'),
    rex_i18n::msg('echarts_demo_month_may'),
    rex_i18n::msg('echarts_demo_month_jun'),
];

$revenueRows = [
    ['name' => $months[0], 'value' => 120],
    ['name' => $months[1], 'value' => 160],
    ['name' => $months[2], 'value' => 185],
    ['name' => $months[3], 'value' => 170],
    ['name' => $months[4], 'value' => 220],
    ['name' => $months[5], 'value' => 260],
];

$channelsRows = [
    ['name' => 'SEO', 'value' => 42],
    ['name' => 'SEA', 'value' => 27],
    ['name' => rex_i18n::msg('echarts_demo_channel_newsletter'), 'value' => 18],
    ['name' => rex_i18n::msg('echarts_demo_channel_direct'), 'value' => 13],
];

$nestedPieInnerRows = [
    ['name' => rex_i18n::msg('echarts_demo_nested_group_paid'), 'value' => 58],
    ['name' => rex_i18n::msg('echarts_demo_nested_group_owned'), 'value' => 42],
];

$nestedPieOuterRows = [
    ['name' => 'SEA', 'value' => 31],
    ['name' => 'Social Ads', 'value' => 15],
    ['name' => 'Affiliate', 'value' => 12],
    ['name' => 'SEO', 'value' => 24],
    ['name' => 'Newsletter', 'value' => 11],
    ['name' => 'Direct', 'value' => 7],
];

$gaugeSimpleRows = [['name' => 'KPI', 'value' => 72]];
$gaugeRingRows = [['name' => 'KPI', 'value' => 86]];

$switchRows = [
    ['name' => $months[0], 'value' => 98],
    ['name' => $months[1], 'value' => 124],
    ['name' => $months[2], 'value' => 149],
    ['name' => $months[3], 'value' => 171],
    ['name' => $months[4], 'value' => 205],
    ['name' => $months[5], 'value' => 238],
];

$compareSeries = [
    [
        'name' => rex_i18n::msg('echarts_demo_series_product_a'),
        'values' => [120, 142, 158, 168, 190, 214],
        'smooth' => true,
    ],
    [
        'name' => rex_i18n::msg('echarts_demo_series_product_b'),
        'values' => [90, 110, 126, 149, 171, 188],
        'smooth' => true,
        'area' => true,
    ],
    [
        'name' => rex_i18n::msg('echarts_demo_series_product_c'),
        'values' => [74, 98, 120, 138, 160, 172],
        'smooth' => true,
    ],
];

$lineRaceFrames = [
    [
        'name' => 'Q1',
        'series' => [
            ['name' => rex_i18n::msg('echarts_demo_team_north'), 'values' => [40, 48, 55, 61, 66, 70]],
            ['name' => rex_i18n::msg('echarts_demo_team_south'), 'values' => [30, 36, 42, 52, 58, 63]],
            ['name' => rex_i18n::msg('echarts_demo_team_east'), 'values' => [22, 28, 36, 44, 49, 56]],
        ],
    ],
    [
        'name' => 'Q2',
        'series' => [
            ['name' => rex_i18n::msg('echarts_demo_team_north'), 'values' => [52, 60, 70, 82, 92, 101]],
            ['name' => rex_i18n::msg('echarts_demo_team_south'), 'values' => [40, 52, 60, 69, 80, 88]],
            ['name' => rex_i18n::msg('echarts_demo_team_east'), 'values' => [34, 42, 51, 59, 68, 76]],
        ],
    ],
    [
        'name' => 'Q3',
        'series' => [
            ['name' => rex_i18n::msg('echarts_demo_team_north'), 'values' => [66, 74, 86, 100, 112, 124]],
            ['name' => rex_i18n::msg('echarts_demo_team_south'), 'values' => [54, 62, 73, 84, 96, 110]],
            ['name' => rex_i18n::msg('echarts_demo_team_east'), 'values' => [45, 53, 63, 72, 84, 95]],
        ],
    ],
];

$lineRaceApacheYears = ['2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024'];
$lineRaceApacheFrames = [
    [
        'name' => 'Jan',
        'series' => [
            ['name' => rex_i18n::msg('echarts_demo_city_beijing'), 'values' => [62, 64, 67, 69, 73, 78, 82, 85, 88]],
            ['name' => rex_i18n::msg('echarts_demo_city_shanghai'), 'values' => [54, 58, 60, 63, 67, 71, 75, 80, 84]],
            ['name' => rex_i18n::msg('echarts_demo_city_guangzhou'), 'values' => [45, 47, 50, 53, 56, 60, 63, 66, 70]],
            ['name' => rex_i18n::msg('echarts_demo_city_shenzhen'), 'values' => [40, 44, 48, 52, 58, 63, 69, 74, 81]],
        ],
    ],
    [
        'name' => 'Apr',
        'series' => [
            ['name' => rex_i18n::msg('echarts_demo_city_beijing'), 'values' => [66, 68, 70, 73, 76, 81, 84, 88, 92]],
            ['name' => rex_i18n::msg('echarts_demo_city_shanghai'), 'values' => [58, 61, 64, 67, 71, 75, 79, 83, 88]],
            ['name' => rex_i18n::msg('echarts_demo_city_guangzhou'), 'values' => [47, 50, 53, 55, 59, 63, 66, 70, 74]],
            ['name' => rex_i18n::msg('echarts_demo_city_shenzhen'), 'values' => [44, 48, 53, 58, 63, 69, 74, 81, 89]],
        ],
    ],
    [
        'name' => 'Jul',
        'series' => [
            ['name' => rex_i18n::msg('echarts_demo_city_beijing'), 'values' => [69, 71, 74, 76, 79, 83, 87, 91, 95]],
            ['name' => rex_i18n::msg('echarts_demo_city_shanghai'), 'values' => [60, 64, 67, 70, 74, 78, 82, 87, 91]],
            ['name' => rex_i18n::msg('echarts_demo_city_guangzhou'), 'values' => [49, 52, 56, 59, 63, 66, 70, 73, 77]],
            ['name' => rex_i18n::msg('echarts_demo_city_shenzhen'), 'values' => [46, 51, 56, 61, 67, 72, 78, 85, 93]],
        ],
    ],
    [
        'name' => 'Oct',
        'series' => [
            ['name' => rex_i18n::msg('echarts_demo_city_beijing'), 'values' => [72, 75, 78, 81, 84, 88, 92, 96, 101]],
            ['name' => rex_i18n::msg('echarts_demo_city_shanghai'), 'values' => [63, 67, 70, 74, 78, 82, 86, 91, 96]],
            ['name' => rex_i18n::msg('echarts_demo_city_guangzhou'), 'values' => [52, 55, 59, 62, 66, 70, 73, 77, 81]],
            ['name' => rex_i18n::msg('echarts_demo_city_shenzhen'), 'values' => [49, 54, 60, 65, 71, 77, 84, 91, 99]],
        ],
    ],
];

$scatterRows = [
    ['name' => 'P1', 'value' => 5.2],
    ['name' => 'P2', 'value' => 8.5],
    ['name' => 'P3', 'value' => 3.9],
    ['name' => 'P4', 'value' => 9.8],
    ['name' => 'P5', 'value' => 7.1],
    ['name' => 'P6', 'value' => 6.4],
];

$companyShareRows = [
    ['name' => 'Nordic Capital', 'value' => 34],
    ['name' => 'GreenVentures', 'value' => 26],
    ['name' => 'Founder Pool', 'value' => 22],
    ['name' => 'Mitarbeitende', 'value' => 18],
];

$stockYears = ['2000', '2002', '2004', '2006', '2008', '2010', '2012', '2014', '2016', '2018', '2020', '2022', '2024', '2026'];
$stockSeries = [
    ['name' => 'SOLAR-X', 'values' => [24, 28, 33, 48, 38, 44, 52, 61, 74, 88, 95, 112, 129, 146]],
    ['name' => 'CLOUD-ONE', 'values' => [18, 22, 29, 36, 31, 41, 49, 59, 67, 79, 90, 104, 120, 134]],
];

$waterYears = ['2000', '2005', '2010', '2015', '2020', '2026'];
$waterSeries = [
    ['name' => rex_i18n::msg('echarts_demo_household_single'), 'values' => [129, 125, 121, 117, 113, 109]],
    ['name' => rex_i18n::msg('echarts_demo_household_family'), 'values' => [164, 159, 153, 149, 145, 141]],
    ['name' => rex_i18n::msg('echarts_demo_household_shared'), 'values' => [142, 139, 135, 131, 129, 126]],
];

$germanyStateValues = [
    ['name' => 'Baden-Württemberg', 'value' => 82],
    ['name' => 'Bayern', 'value' => 88],
    ['name' => 'Berlin', 'value' => 73],
    ['name' => 'Brandenburg', 'value' => 64],
    ['name' => 'Bremen', 'value' => 61],
    ['name' => 'Hamburg', 'value' => 76],
    ['name' => 'Hessen', 'value' => 79],
    ['name' => 'Mecklenburg-Vorpommern', 'value' => 58],
    ['name' => 'Niedersachsen', 'value' => 71],
    ['name' => 'Nordrhein-Westfalen', 'value' => 84],
    ['name' => 'Rheinland-Pfalz', 'value' => 69],
    ['name' => 'Saarland', 'value' => 63],
    ['name' => 'Sachsen-Anhalt', 'value' => 57],
    ['name' => 'Sachsen', 'value' => 66],
    ['name' => 'Schleswig-Holstein', 'value' => 62],
    ['name' => 'Thüringen', 'value' => 60],
];

$nrwBayernHessenRows = [
    ['name' => rex_i18n::msg('echarts_demo_state_nrw'), 'value' => 84],
    ['name' => rex_i18n::msg('echarts_demo_state_bayern'), 'value' => 88],
    ['name' => rex_i18n::msg('echarts_demo_state_hessen'), 'value' => 79],
];

$hessenDistrictRows = [
    ['name' => 'Frankfurt am Main Städte', 'value' => 93],
    ['name' => 'Wiesbaden Städte', 'value' => 81],
    ['name' => 'Kassel Städte', 'value' => 74],
    ['name' => 'Main-Kinzig-Kreis', 'value' => 69],
    ['name' => 'Darmstadt-Dieburg', 'value' => 77],
    ['name' => 'Lahn-Dill-Kreis', 'value' => 66],
    ['name' => 'Fulda', 'value' => 71],
    ['name' => 'Odenwaldkreis', 'value' => 62],
];

$nrwDistrictRows = [
    ['name' => 'Düsseldorf Städte', 'value' => 95],
    ['name' => 'Cologne Städte', 'value' => 90],
    ['name' => 'Bonn Städte', 'value' => 84],
    ['name' => 'Münster Städte', 'value' => 79],
    ['name' => 'Aachen', 'value' => 73],
    ['name' => 'Rhein-Erft-Kreis', 'value' => 68],
    ['name' => 'Rhein-Sieg', 'value' => 72],
    ['name' => 'Steinfurt', 'value' => 63],
];

$nrwCompanyCoverageRows = [
    ['name' => 'Düsseldorf Städte', 'value' => 2],
    ['name' => 'Cologne Städte', 'value' => 2],
    ['name' => 'Bonn Städte', 'value' => 1],
    ['name' => 'Münster Städte', 'value' => 1],
    ['name' => 'Aachen', 'value' => 1],
    ['name' => 'Rhein-Erft-Kreis', 'value' => 1],
    ['name' => 'Rhein-Sieg', 'value' => 1],
    ['name' => 'Steinfurt', 'value' => 0],
    ['name' => 'Essen Städte', 'value' => 2],
    ['name' => 'Dortmund Städte', 'value' => 2],
    ['name' => 'Bielefeld Städte', 'value' => 0],
    ['name' => 'Bochum Städte', 'value' => 1],
    ['name' => 'Wuppertal Städte', 'value' => 1],
    ['name' => 'Paderborn', 'value' => 0],
    ['name' => 'Unna', 'value' => 1],
];

$fvnCircleRows = [
    ['name' => 'Kreis Düsseldorf', 'value' => 74],
    ['name' => 'Kreis Solingen', 'value' => 61],
    ['name' => 'Kreis Wuppertal/Niederberg', 'value' => 66],
    ['name' => 'Kreis Mönchengladbach/Viersen', 'value' => 59],
    ['name' => 'Kreis Grevenbroich/Neuss', 'value' => 63],
    ['name' => 'Kreis Kempen/Krefeld', 'value' => 57],
    ['name' => 'Kreis Moers', 'value' => 55],
    ['name' => 'Kreis Kleve/Geldern', 'value' => 52],
    ['name' => 'Kreis Duisburg/Mülheim/Dinslaken', 'value' => 70],
    ['name' => 'Kreis Oberhausen/Bottrop', 'value' => 58],
    ['name' => 'Kreis Rees/Bocholt', 'value' => 50],
    ['name' => 'Kreis Essen', 'value' => 62],
    ['name' => 'Kreis Remscheid', 'value' => 53],
];

$fvnCircleGroups = [
    ['name' => 'Kreis Düsseldorf', 'keywords' => ['duesseldorf']],
    ['name' => 'Kreis Solingen', 'keywords' => ['solingen']],
    ['name' => 'Kreis Wuppertal/Niederberg', 'keywords' => ['wuppertal', 'mettmann']],
    ['name' => 'Kreis Mönchengladbach/Viersen', 'keywords' => ['moenchengladbach', 'viersen']],
    ['name' => 'Kreis Grevenbroich/Neuss', 'keywords' => ['rhein-kreis neuss']],
    ['name' => 'Kreis Kempen/Krefeld', 'keywords' => ['krefeld', 'viersen']],
    ['name' => 'Kreis Moers', 'keywords' => ['wesel', 'moers']],
    ['name' => 'Kreis Kleve/Geldern', 'keywords' => ['cleves', 'geldern']],
    ['name' => 'Kreis Duisburg/Mülheim/Dinslaken', 'keywords' => ['duisburg', 'muelheim', 'wesel', 'dinslaken']],
    ['name' => 'Kreis Oberhausen/Bottrop', 'keywords' => ['oberhausen', 'bottrop']],
    ['name' => 'Kreis Rees/Bocholt', 'keywords' => ['borken', 'bocholt', 'rees']],
    ['name' => 'Kreis Essen', 'keywords' => ['essen']],
    ['name' => 'Kreis Remscheid', 'keywords' => ['remscheid']],
];

$rainOptions = [
    'title' => ['text' => rex_i18n::msg('echarts_demo_rain_chart_title')],
    'tooltip' => ['trigger' => 'axis'],
    'legend' => ['data' => [rex_i18n::msg('echarts_demo_city_berlin'), rex_i18n::msg('echarts_demo_city_hamburg'), rex_i18n::msg('echarts_demo_city_munich')]],
    'xAxis' => ['type' => 'category', 'data' => $months],
    'yAxis' => ['type' => 'value', 'name' => 'mm'],
    'series' => [
        ['name' => rex_i18n::msg('echarts_demo_city_berlin'), 'type' => 'bar', 'data' => [32, 28, 36, 41, 55, 68]],
        ['name' => rex_i18n::msg('echarts_demo_city_hamburg'), 'type' => 'bar', 'data' => [54, 48, 57, 62, 73, 79]],
        ['name' => rex_i18n::msg('echarts_demo_city_munich'), 'type' => 'bar', 'data' => [44, 39, 52, 59, 70, 84]],
    ],
];

$globalRadarOptions = [
    'title' => ['text' => rex_i18n::msg('echarts_demo_global_chart_title')],
    'tooltip' => ['trigger' => 'item'],
    'legend' => ['data' => [rex_i18n::msg('echarts_demo_global_series_2026'), rex_i18n::msg('echarts_demo_global_series_2030')]],
    'radar' => [
        'indicator' => [
            ['name' => rex_i18n::msg('echarts_demo_region_europe'), 'max' => 100],
            ['name' => rex_i18n::msg('echarts_demo_region_north_america'), 'max' => 100],
            ['name' => rex_i18n::msg('echarts_demo_region_south_america'), 'max' => 100],
            ['name' => rex_i18n::msg('echarts_demo_region_africa'), 'max' => 100],
            ['name' => rex_i18n::msg('echarts_demo_region_asia'), 'max' => 100],
            ['name' => rex_i18n::msg('echarts_demo_region_oceania'), 'max' => 100],
        ],
    ],
    'series' => [[
        'name' => rex_i18n::msg('echarts_demo_global_chart_title'),
        'type' => 'radar',
        'data' => [
            ['name' => rex_i18n::msg('echarts_demo_global_series_2026'), 'value' => [68, 72, 41, 36, 88, 27]],
            ['name' => rex_i18n::msg('echarts_demo_global_series_2030'), 'value' => [74, 77, 46, 43, 93, 31]],
        ],
    ]],
];

$renderExample = static function (string $title, string $description, array $options, string $code, int $height = 340): void {
    echo '<div class="panel panel-default" style="margin-bottom:20px">';
    echo '<div class="panel-heading"><h3 class="panel-title">' . rex_escape($title) . '</h3></div>';
    echo '<div class="panel-body">';
    echo '<p class="text-muted" style="margin-bottom:14px">' . rex_escape($description) . '</p>';
    echo '<div class="row">';
    echo '<div class="col-md-7">';
    echo ChartRenderer::render($options, $height);
    echo '</div>';
    echo '<div class="col-md-5">';
    echo '<div class="help-block" style="margin-top:0">' . rex_i18n::msg('echarts_demo_copy_hint') . '</div>';
    echo '<pre style="font-size:12px;max-height:360px;overflow:auto">' . rex_escape($code) . '</pre>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
};

echo '<div class="alert alert-info" style="margin-bottom:16px">' . rex_i18n::msg('echarts_demo_intro') . '</div>';
echo '<div class="alert alert-success" style="margin-bottom:16px">' . rex_i18n::msg('echarts_demo_showcase_intro') . '</div>';

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
            $yformOptions = PresetFactory::fromType('bar', $yformRows, rex_i18n::msg('echarts_demo_live_chart_title'));
            $yformCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\DataResolver;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = DataResolver::fromYform('rex_echarts_demo_data', 'name', 'value', 50);
$options = PresetFactory::fromType('bar', $rows, 'YForm Live-Daten');

echo ChartRenderer::render($options, 360);
PHP;
            $renderExample(
                rex_i18n::msg('echarts_demo_live_panel_title'),
                rex_i18n::msg('echarts_demo_live_panel_help', DemoDataManager::getTableName()),
                $yformOptions,
                $yformCode,
                360
            );
        }
    }
}

$basicBarOptions = PresetFactory::fromType('bar', $revenueRows, rex_i18n::msg('echarts_demo_chart_revenue_halfyear'));
$basicBarCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'Jan', 'value' => 120],
    ['name' => 'Feb', 'value' => 160],
    ['name' => 'Mar', 'value' => 185],
    ['name' => 'Apr', 'value' => 170],
    ['name' => 'Mai', 'value' => 220],
    ['name' => 'Jun', 'value' => 260],
];

echo ChartRenderer::render(
    PresetFactory::fromType('bar', $rows, 'Umsatz 1. Halbjahr'),
    340
);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_basic_bar_title'),
    rex_i18n::msg('echarts_demo_example_basic_bar_desc'),
    $basicBarOptions,
    $basicBarCode
);

$multiLineOptions = PresetFactory::multiLine(
    $months,
    $compareSeries,
    rex_i18n::msg('echarts_demo_compare_chart_title'),
    true,
    ['#1f77b4', '#2ca02c', '#ff7f0e'],
    true,
    false,
    true
);
$multiLineCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$series = [
    ['name' => 'Produkt A', 'values' => [120, 142, 158, 168, 190, 214]],
    ['name' => 'Produkt B', 'values' => [90, 110, 126, 149, 171, 188], 'area' => true],
    ['name' => 'Produkt C', 'values' => [74, 98, 120, 138, 160, 172]],
];
$options = PresetFactory::multiLine($months, $series, 'Produktvergleich');
echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_compare_lines_title'),
    rex_i18n::msg('echarts_demo_example_compare_lines_desc'),
    $multiLineOptions,
    $multiLineCode
);

$lineRaceOptions = PresetFactory::lineRace(
    $months,
    $lineRaceFrames,
    rex_i18n::msg('echarts_demo_line_race_chart_title'),
    true,
    ['#005f73', '#0a9396', '#94d2bd'],
    true,
    true,
    1200,
    true,
    false,
    false
);
$lineRaceCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$frames = [
    [
        'name' => 'Q1',
        'series' => [
            ['name' => 'Nord', 'values' => [40, 48, 55, 61, 66, 70]],
            ['name' => 'South', 'values' => [30, 36, 42, 52, 58, 63]],
            ['name' => 'Ost', 'values' => [22, 28, 36, 44, 49, 56]],
        ],
    ],
    [
        'name' => 'Q2',
        'series' => [
            ['name' => 'Nord', 'values' => [52, 60, 70, 82, 92, 101]],
            ['name' => 'South', 'values' => [40, 52, 60, 69, 80, 88]],
            ['name' => 'Ost', 'values' => [34, 42, 51, 59, 68, 76]],
        ],
    ],
];

$options = PresetFactory::lineRace(
    $months,
    $frames,
    'Line Race',
    true,
    [],
    true,
    true,
    1200,
    true,
    false,
    false
);
echo ChartRenderer::render($options, 360);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_line_race_title'),
    rex_i18n::msg('echarts_demo_example_line_race_desc'),
    $lineRaceOptions,
    $lineRaceCode,
    360
);

$lineRaceApacheOptions = PresetFactory::lineRace(
    $lineRaceApacheYears,
    $lineRaceApacheFrames,
    rex_i18n::msg('echarts_demo_line_race_apache_chart_title'),
    true,
    ['#3d5a80', '#457b9d', '#2a9d8f', '#e76f51'],
    true,
    true,
    1000,
    true,
    true,
    false
);
$lineRaceApacheCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$years = ['2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024'];
$frames = [
    [
        'name' => 'Jan',
        'series' => [
            ['name' => 'Beijing', 'values' => [62, 64, 67, 69, 73, 78, 82, 85, 88]],
            ['name' => 'Shanghai', 'values' => [54, 58, 60, 63, 67, 71, 75, 80, 84]],
            ['name' => 'Guangzhou', 'values' => [45, 47, 50, 53, 56, 60, 63, 66, 70]],
            ['name' => 'Shenzhen', 'values' => [40, 44, 48, 52, 58, 63, 69, 74, 81]],
        ],
    ],
    [
        'name' => 'Jul',
        'series' => [
            ['name' => 'Beijing', 'values' => [69, 71, 74, 76, 79, 83, 87, 91, 95]],
            ['name' => 'Shanghai', 'values' => [60, 64, 67, 70, 74, 78, 82, 87, 91]],
            ['name' => 'Guangzhou', 'values' => [49, 52, 56, 59, 63, 66, 70, 73, 77]],
            ['name' => 'Shenzhen', 'values' => [46, 51, 56, 61, 67, 72, 78, 85, 93]],
        ],
    ],
];

// Inspiration: https://echarts.apache.org/examples/en/editor.html?c=line-race
// Neu: Start erst, wenn der Chart im Viewport sichtbar ist.
$options = PresetFactory::lineRace($years, $frames, 'Line Race (Apache-style)', true, [], true, true, 1000, true, true, false);
echo ChartRenderer::render($options, 380);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_line_race_apache_title'),
    rex_i18n::msg('echarts_demo_example_line_race_apache_desc'),
    $lineRaceApacheOptions,
    $lineRaceApacheCode,
    380
);

$geoMapOptions = [
    'title' => ['text' => rex_i18n::msg('echarts_demo_geo_chart_title')],
    'tooltip' => ['trigger' => 'item'],
    'visualMap' => [
        'min' => 55,
        'max' => 90,
        'left' => 10,
        'bottom' => 10,
        'text' => [rex_i18n::msg('echarts_demo_geo_high'), rex_i18n::msg('echarts_demo_geo_low')],
        'calculable' => true,
    ],
    'geo' => [
        'map' => 'de_bundeslaender',
        'roam' => true,
        'label' => ['show' => true, 'fontSize' => 9],
        'itemStyle' => ['borderColor' => '#d6dee6', 'borderWidth' => 1],
        'emphasis' => ['label' => ['show' => true], 'itemStyle' => ['areaColor' => '#ffe8a3']],
    ],
    'series' => [
        [
            'name' => rex_i18n::msg('echarts_demo_geo_series_region_values'),
            'type' => 'map',
            'map' => 'de_bundeslaender',
            'geoIndex' => 0,
            'data' => $germanyStateValues,
        ],
        [
            'name' => rex_i18n::msg('echarts_demo_geo_series_cities'),
            'type' => 'scatter',
            'coordinateSystem' => 'geo',
            'symbolSize' => 14,
            'itemStyle' => ['color' => '#e76f51'],
            'data' => [
                ['name' => 'Düsseldorf', 'value' => [6.7735, 51.2277, 84]],
                ['name' => 'München', 'value' => [11.5820, 48.1351, 88]],
                ['name' => 'Wiesbaden', 'value' => [8.2415, 50.0782, 79]],
                ['name' => 'Hamburg', 'value' => [9.9937, 53.5511, 76]],
                ['name' => 'Berlin', 'value' => [13.4050, 52.5200, 73]],
            ],
        ],
    ],
];
$geoMapOptionsJson = json_encode($geoMapOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$geoMapOptionsBase64 = is_string($geoMapOptionsJson) && $geoMapOptionsJson !== '' ? base64_encode($geoMapOptionsJson) : '';
$geoMapAssetUrl = rex_addon::get('echarts')->getAssetsUrl('maps/de-bundeslaender.geo.json');
$geoMapCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;

// Wichtig: Die Demo nutzt assets/echarts-demo-geo.js als Loader.
// Der Loader holt data-map-geojson-url, registriert die Karte per
// echarts.registerMap(...) und rendert dann data-map-options.

// 1) GeoJSON lokal speichern, z.B. assets/addons/echarts/maps/de-bundeslaender.geo.json
// 2) Optionen als JSON bereitstellen (hier vereinfacht)
// 3) Container mit data-map-* Attributen ausgeben

$options = [
    'geo' => ['map' => 'de_bundeslaender', 'roam' => true],
    'series' => [
        ['type' => 'map', 'map' => 'de_bundeslaender', 'data' => [
            ['name' => 'Nordrhein-Westfalen', 'value' => 84],
            ['name' => 'Bayern', 'value' => 88],
            ['name' => 'Hessen', 'value' => 79],
        ]],
        ['type' => 'scatter', 'coordinateSystem' => 'geo', 'data' => [
            ['name' => 'Düsseldorf', 'value' => [6.7735, 51.2277, 84]],
            ['name' => 'München', 'value' => [11.5820, 48.1351, 88]],
            ['name' => 'Wiesbaden', 'value' => [8.2415, 50.0782, 79]],
        ]],
    ],
];

$optionsBase64 = base64_encode((string) json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo '<div class="js-echarts-geo-map"'
    . ' data-map-name="de_bundeslaender"'
    . ' data-map-name-prop="NAME_1"'
    . ' data-map-geojson-url="' . rex_addon::get('echarts')->getAssetsUrl('maps/de-bundeslaender.geo.json') . '"'
    . ' data-map-options="' . $optionsBase64 . '"'
    . ' style="height:420px"'
    . '></div>';
PHP;
echo '<div class="panel panel-default" style="margin-bottom:20px">';
echo '<div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_demo_example_geo_de_title') . '</h3></div>';
echo '<div class="panel-body">';
echo '<p class="text-muted" style="margin-bottom:14px">' . rex_i18n::msg('echarts_demo_example_geo_de_desc') . '</p>';
echo '<div class="row">';
echo '<div class="col-md-7">';
echo '<div class="js-echarts-geo-map" style="height:420px" data-map-name="de_bundeslaender" data-map-name-prop="NAME_1" data-map-geojson-url="' . rex_escape($geoMapAssetUrl) . '" data-map-options="' . rex_escape($geoMapOptionsBase64) . '"></div>';
echo '</div>';
echo '<div class="col-md-5">';
echo '<div class="help-block" style="margin-top:0">' . rex_i18n::msg('echarts_demo_copy_hint') . '</div>';
echo '<pre style="font-size:12px;max-height:360px;overflow:auto">' . rex_escape($geoMapCode) . '</pre>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

$stateCompareOptions = PresetFactory::fromType(
    'bar',
    $nrwBayernHessenRows,
    rex_i18n::msg('echarts_demo_state_compare_chart_title'),
    true,
    ['#005f73', '#0a9396', '#94d2bd'],
    true,
    true,
    true,
    false
);
$stateCompareCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'Nordrhein-Westfalen', 'value' => 84],
    ['name' => 'Bayern', 'value' => 88],
    ['name' => 'Hessen', 'value' => 79],
];

$options = PresetFactory::fromType('bar', $rows, 'Vergleich: NRW, Bayern, Hessen');
echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_state_compare_title'),
    rex_i18n::msg('echarts_demo_example_state_compare_desc'),
    $stateCompareOptions,
    $stateCompareCode,
    340
);

$districtMapAssetUrl = rex_addon::get('echarts')->getAssetsUrl('maps/de-kreise.geo.json');
$hessenDistrictOptions = [
    'title' => ['text' => rex_i18n::msg('echarts_demo_district_hessen_chart_title')],
    'tooltip' => ['trigger' => 'item'],
    'visualMap' => [
        'min' => 55,
        'max' => 100,
        'left' => 10,
        'bottom' => 10,
        'text' => [rex_i18n::msg('echarts_demo_geo_high'), rex_i18n::msg('echarts_demo_geo_low')],
        'calculable' => true,
    ],
    'series' => [[
        'name' => rex_i18n::msg('echarts_demo_district_series_title'),
        'type' => 'map',
        'map' => 'de_kreise_hessen',
        'label' => [
            'show' => false,
            'fontSize' => 8,
            'color' => '#1f2933',
            'formatter' => '{b}',
        ],
        'emphasis' => ['label' => ['show' => true, 'fontSize' => 10]],
        'data' => $hessenDistrictRows,
    ]],
];
$nrwDistrictOptions = [
    'title' => ['text' => rex_i18n::msg('echarts_demo_district_nrw_chart_title')],
    'tooltip' => ['trigger' => 'item'],
    'visualMap' => [
        'min' => 55,
        'max' => 100,
        'left' => 10,
        'bottom' => 10,
        'text' => [rex_i18n::msg('echarts_demo_geo_high'), rex_i18n::msg('echarts_demo_geo_low')],
        'calculable' => true,
    ],
    'series' => [[
        'name' => rex_i18n::msg('echarts_demo_district_series_title'),
        'type' => 'map',
        'map' => 'de_kreise_nrw',
        'label' => [
            'show' => false,
            'fontSize' => 8,
            'color' => '#1f2933',
            'formatter' => '{b}',
        ],
        'emphasis' => ['label' => ['show' => true, 'fontSize' => 10]],
        'data' => $nrwDistrictRows,
    ]],
];
$hessenDistrictOptionsJson = json_encode($hessenDistrictOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$hessenDistrictOptionsBase64 = is_string($hessenDistrictOptionsJson) && $hessenDistrictOptionsJson !== '' ? base64_encode($hessenDistrictOptionsJson) : '';
$nrwDistrictOptionsJson = json_encode($nrwDistrictOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$nrwDistrictOptionsBase64 = is_string($nrwDistrictOptionsJson) && $nrwDistrictOptionsJson !== '' ? base64_encode($nrwDistrictOptionsJson) : '';
$districtMapCode = <<<'PHP'
<?php
// Kreis-Geometrie: https://github.com/isellsoap/deutschlandGeoJSON (Ordner 4_kreise)
// Filter je Bundesland ueber Feature-Property NAME_1 (z.B. Hessen, Nordrhein-Westfalen)

echo '<div class="js-echarts-geo-map"'
  . ' data-map-name="de_kreise_hessen"'
  . ' data-map-geojson-url=".../de-kreise.geo.json"'
  . ' data-map-filter-prop="NAME_1"'
  . ' data-map-filter-value="Hessen"'
  . ' data-map-options="...base64json..."'
  . '></div>';
PHP;

echo '<div class="panel panel-default" style="margin-bottom:20px">';
echo '<div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_demo_example_district_states_title') . '</h3></div>';
echo '<div class="panel-body">';
echo '<p class="text-muted" style="margin-bottom:14px">' . rex_i18n::msg('echarts_demo_example_district_states_desc') . '</p>';
echo '<div class="row">';
echo '<div class="col-md-6">';
echo '<div class="js-echarts-geo-map" style="height:460px" data-map-name="de_kreise_hessen" data-map-name-prop="NAME_3" data-map-geojson-url="' . rex_escape($districtMapAssetUrl) . '" data-map-filter-prop="NAME_1" data-map-filter-value="Hessen" data-map-options="' . rex_escape($hessenDistrictOptionsBase64) . '"></div>';
echo '</div>';
echo '<div class="col-md-6">';
echo '<div class="js-echarts-geo-map" style="height:460px" data-map-name="de_kreise_nrw" data-map-name-prop="NAME_3" data-map-geojson-url="' . rex_escape($districtMapAssetUrl) . '" data-map-filter-prop="NAME_1" data-map-filter-value="Nordrhein-Westfalen" data-map-options="' . rex_escape($nrwDistrictOptionsBase64) . '"></div>';
echo '</div>';
echo '</div>';
echo '<div class="help-block" style="margin-top:12px">' . rex_i18n::msg('echarts_demo_copy_hint') . '</div>';
echo '<pre style="font-size:12px;max-height:320px;overflow:auto">' . rex_escape($districtMapCode) . '</pre>';
echo '</div>';
echo '</div>';

$coverageOptions = [
    'title' => ['text' => rex_i18n::msg('echarts_demo_district_coverage_chart_title')],
    'tooltip' => [
        'trigger' => 'item',
        'formatter' => '{b}<br/>{a}: {@[2]}',
    ],
    'visualMap' => [
        'type' => 'piecewise',
        'left' => 10,
        'bottom' => 10,
        'pieces' => [
            ['value' => 2, 'label' => rex_i18n::msg('echarts_demo_coverage_active'), 'color' => '#2a9d8f'],
            ['value' => 1, 'label' => rex_i18n::msg('echarts_demo_coverage_partial'), 'color' => '#f4a261'],
            ['value' => 0, 'label' => rex_i18n::msg('echarts_demo_coverage_inactive'), 'color' => '#b0b7c3'],
        ],
    ],
    'series' => [[
        'name' => rex_i18n::msg('echarts_demo_district_coverage_series'),
        'type' => 'map',
        'map' => 'de_kreise_nrw_coverage',
        'label' => [
            'show' => false,
            'fontSize' => 8,
            'color' => '#1f2933',
            'formatter' => '{b}',
        ],
        'emphasis' => ['label' => ['show' => true, 'fontSize' => 10]],
        'data' => $nrwCompanyCoverageRows,
    ]],
];

$coverageOptionsJson = json_encode($coverageOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$coverageOptionsBase64 = is_string($coverageOptionsJson) && $coverageOptionsJson !== '' ? base64_encode($coverageOptionsJson) : '';
$coverageCode = <<<'PHP'
<?php
// Wichtig: Die Karte wird ueber assets/echarts-demo-geo.js geladen.
// value=2 => aktiv, value=1 => teilweise aktiv, value=0 => inaktiv

$rows = [
  ['name' => 'Düsseldorf Städte', 'value' => 2],
  ['name' => 'Cologne Städte', 'value' => 2],
  ['name' => 'Bonn Städte', 'value' => 1],
  ['name' => 'Steinfurt', 'value' => 0],
];

$options = [
    'series' => [[
        'type' => 'map',
        'map' => 'de_kreise_nrw_coverage',
        'data' => $rows,
    ]],
    'visualMap' => [
        'type' => 'piecewise',
        'pieces' => [
            ['value' => 2, 'label' => 'Aktiv', 'color' => '#2a9d8f'],
            ['value' => 1, 'label' => 'Teilaktiv', 'color' => '#f4a261'],
            ['value' => 0, 'label' => 'Inaktiv', 'color' => '#b0b7c3'],
        ],
    ],
];

$optionsBase64 = base64_encode((string) json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo '<div class="js-echarts-geo-map"'
    . ' data-map-name="de_kreise_nrw_coverage"'
    . ' data-map-name-prop="NAME_3"'
    . ' data-map-geojson-url="' . rex_addon::get('echarts')->getAssetsUrl('maps/de-kreise.geo.json') . '"'
    . ' data-map-filter-prop="NAME_1"'
    . ' data-map-filter-value="Nordrhein-Westfalen"'
    . ' data-map-options="' . $optionsBase64 . '"'
    . ' style="height:480px"'
    . '></div>';
PHP;

echo '<div class="panel panel-default" style="margin-bottom:20px">';
echo '<div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_demo_example_district_coverage_title') . '</h3></div>';
echo '<div class="panel-body">';
echo '<p class="text-muted" style="margin-bottom:14px">' . rex_i18n::msg('echarts_demo_example_district_coverage_desc') . '</p>';
echo '<div class="row">';
echo '<div class="col-md-7">';
echo '<div class="js-echarts-geo-map" style="height:480px" data-map-name="de_kreise_nrw_coverage" data-map-name-prop="NAME_3" data-map-geojson-url="' . rex_escape($districtMapAssetUrl) . '" data-map-filter-prop="NAME_1" data-map-filter-value="Nordrhein-Westfalen" data-map-options="' . rex_escape($coverageOptionsBase64) . '"></div>';
echo '</div>';
echo '<div class="col-md-5">';
echo '<div class="help-block" style="margin-top:0">' . rex_i18n::msg('echarts_demo_copy_hint') . '</div>';
echo '<pre style="font-size:12px;max-height:320px;overflow:auto">' . rex_escape($coverageCode) . '</pre>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

$fvnOptions = [
        'title' => ['text' => rex_i18n::msg('echarts_demo_fvn_chart_title')],
        'tooltip' => ['trigger' => 'item'],
        'visualMap' => [
                'min' => 45,
                'max' => 80,
                'left' => 10,
                'bottom' => 10,
                'text' => [rex_i18n::msg('echarts_demo_geo_high'), rex_i18n::msg('echarts_demo_geo_low')],
                'calculable' => true,
        ],
        'series' => [[
                'name' => rex_i18n::msg('echarts_demo_fvn_series_title'),
                'type' => 'map',
                'map' => 'de_fvn_kreise',
                'label' => ['show' => false],
                'emphasis' => ['label' => ['show' => true, 'fontSize' => 10]],
                'data' => $fvnCircleRows,
        ]],
];
$fvnOptionsJson = json_encode($fvnOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$fvnOptionsBase64 = is_string($fvnOptionsJson) && $fvnOptionsJson !== '' ? base64_encode($fvnOptionsJson) : '';
$fvnGroupsJson = json_encode($fvnCircleGroups, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$fvnGroupsBase64 = is_string($fvnGroupsJson) && $fvnGroupsJson !== '' ? base64_encode($fvnGroupsJson) : '';
$fvnCode = <<<'PHP'
<?php
// FVN-Kreise ueber Gruppenlogik aus NRW-Kreisgeometrien ableiten.
// Jede Gruppe bekommt eine Keyword-Liste aus Orten/Kreisnamen.

$groups = [
    ['name' => 'Kreis Düsseldorf', 'keywords' => ['duesseldorf']],
    ['name' => 'Kreis Solingen', 'keywords' => ['solingen']],
    ['name' => 'Kreis Wuppertal/Niederberg', 'keywords' => ['wuppertal', 'mettmann']],
    // ... weitere FVN-Kreise
];

$options = [
    'series' => [[
        'type' => 'map',
        'map' => 'de_fvn_kreise',
        'data' => [
            ['name' => 'Kreis Düsseldorf', 'value' => 74],
            ['name' => 'Kreis Solingen', 'value' => 61],
        ],
    ]],
];

$optionsBase64 = base64_encode((string) json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$groupsBase64 = base64_encode((string) json_encode($groups, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo '<div class="js-echarts-geo-map"'
    . ' data-map-name="de_fvn_kreise"'
    . ' data-map-geojson-url="' . rex_addon::get('echarts')->getAssetsUrl('maps/de-kreise.geo.json') . '"'
    . ' data-map-filter-prop="NAME_1"'
    . ' data-map-filter-value="Nordrhein-Westfalen"'
    . ' data-map-groups="' . $groupsBase64 . '"'
    . ' data-map-group-source-prop="NAME_3"'
    . ' data-map-groups-drop-unmatched="1"'
    . ' data-map-options="' . $optionsBase64 . '"'
    . ' style="height:520px"'
    . '></div>';
PHP;

echo '<div class="panel panel-default" style="margin-bottom:20px">';
echo '<div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_demo_example_fvn_title') . '</h3></div>';
echo '<div class="panel-body">';
echo '<p class="text-muted" style="margin-bottom:14px">' . rex_i18n::msg('echarts_demo_example_fvn_desc') . '</p>';
echo '<div class="row">';
echo '<div class="col-md-7">';
echo '<div class="js-echarts-geo-map" style="height:520px" data-map-name="de_fvn_kreise" data-map-geojson-url="' . rex_escape($districtMapAssetUrl) . '" data-map-filter-prop="NAME_1" data-map-filter-value="Nordrhein-Westfalen" data-map-groups="' . rex_escape($fvnGroupsBase64) . '" data-map-group-source-prop="NAME_3" data-map-groups-drop-unmatched="1" data-map-options="' . rex_escape($fvnOptionsBase64) . '"></div>';
echo '</div>';
echo '<div class="col-md-5">';
echo '<div class="help-block" style="margin-top:0">' . rex_i18n::msg('echarts_demo_copy_hint') . '</div>';
echo '<pre style="font-size:12px;max-height:360px;overflow:auto">' . rex_escape($fvnCode) . '</pre>';
echo '</div>';
echo '</div>';
echo '<div class="alert alert-warning" style="margin-top:14px;margin-bottom:0">' . rex_i18n::msg('echarts_demo_example_fvn_note') . '</div>';
echo '</div>';
echo '</div>';

echo '<div class="alert alert-info" style="margin-bottom:16px">';
echo '<strong>' . rex_i18n::msg('echarts_demo_map_guide_title') . '</strong><br>';
echo rex_i18n::msg('echarts_demo_map_guide_text_1') . '<br>';
echo rex_i18n::msg('echarts_demo_map_guide_text_2') . '<br>';
echo '<strong>' . rex_i18n::msg('echarts_demo_map_guide_sources_title') . '</strong>';
echo '<ul style="margin-top:6px;margin-bottom:0">';
echo '<li><a href="https://github.com/isellsoap/deutschlandGeoJSON" target="_blank" rel="noopener noreferrer">deutschlandGeoJSON (Bundesländer und Kreise)</a></li>';
echo '<li><a href="https://www.govdata.de" target="_blank" rel="noopener noreferrer">GovData (Deutschland Open Data)</a></li>';
echo '<li><a href="https://www.opengeodata.nrw.de" target="_blank" rel="noopener noreferrer">OpenGeoData NRW</a></li>';
echo '</ul>';
echo '</div>';

$donutOptions = PresetFactory::fromType(
    'donut',
    $channelsRows,
    rex_i18n::msg('echarts_demo_chart_channels_share'),
    true,
    ['#1d3557', '#457b9d', '#a8dadc', '#e63946'],
    true,
    false,
    true,
    true
);
$donutCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'SEO', 'value' => 42],
    ['name' => 'SEA', 'value' => 27],
    ['name' => 'Newsletter', 'value' => 18],
    ['name' => 'Direkt', 'value' => 13],
];

$options = PresetFactory::fromType(
    'donut',
    $rows,
    'Channel share',
    true,
    ['#1d3557', '#457b9d', '#a8dadc', '#e63946'],
    true,
    false,
    true,
    true
);

echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_donut_title'),
    rex_i18n::msg('echarts_demo_example_donut_desc'),
    $donutOptions,
    $donutCode
);

$nestedPieOptions = PresetFactory::nestedPie(
    $nestedPieInnerRows,
    $nestedPieOuterRows,
    rex_i18n::msg('echarts_demo_nested_pie_chart_title'),
    true,
    ['#264653', '#2a9d8f', '#e76f51', '#f4a261', '#e9c46a', '#8ab17d', '#577590'],
    true
);
$nestedPieCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$inner = [
    ['name' => 'Paid', 'value' => 58],
    ['name' => 'Owned', 'value' => 42],
];

$outer = [
    ['name' => 'SEA', 'value' => 31],
    ['name' => 'Social Ads', 'value' => 15],
    ['name' => 'Affiliate', 'value' => 12],
    ['name' => 'SEO', 'value' => 24],
    ['name' => 'Newsletter', 'value' => 11],
    ['name' => 'Direct', 'value' => 7],
];

$options = PresetFactory::nestedPie($inner, $outer, 'Traffic Split: nested pie');
echo ChartRenderer::render($options, 360);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_nested_pie_title'),
    rex_i18n::msg('echarts_demo_example_nested_pie_desc'),
    $nestedPieOptions,
    $nestedPieCode,
    360
);

$gaugeSimpleOptions = PresetFactory::fromType(
    'gauge_simple',
    $gaugeSimpleRows,
    rex_i18n::msg('echarts_demo_gauge_simple_chart_title'),
    true,
    [],
    true,
    false,
    true,
    false
);
$gaugeSimpleCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [['name' => 'KPI', 'value' => 72]];
$options = PresetFactory::fromType('gauge_simple', $rows, 'KPI Fulfillment');

echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_gauge_simple_title'),
    rex_i18n::msg('echarts_demo_example_gauge_simple_desc'),
    $gaugeSimpleOptions,
    $gaugeSimpleCode,
    340
);

$gaugeRingOptions = PresetFactory::fromType(
    'gauge_ring',
    $gaugeRingRows,
    rex_i18n::msg('echarts_demo_gauge_ring_chart_title'),
    true,
    [],
    true,
    false,
    true,
    false
);
$gaugeRingCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [['name' => 'KPI', 'value' => 86]];
$options = PresetFactory::fromType('gauge_ring', $rows, 'Service Health');

echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_gauge_ring_title'),
    rex_i18n::msg('echarts_demo_example_gauge_ring_desc'),
    $gaugeRingOptions,
    $gaugeRingCode,
    340
);

$switchBarOptions = PresetFactory::fromType(
    'bar',
    $switchRows,
    rex_i18n::msg('echarts_demo_switch_chart_sales'),
    true,
    ['#1d3557', '#457b9d', '#a8dadc'],
    true,
    true,
    true,
    false
);
$switchBarOptions = PresetFactory::withExportToolbox($switchBarOptions, true, true, true, 'switch-view-bar');
$switchLineOptions = PresetFactory::fromType(
    'line',
    $switchRows,
    rex_i18n::msg('echarts_demo_switch_chart_trend'),
    true,
    ['#0f4c5c'],
    true,
    true,
    true,
    false
);
$switchLineOptions = PresetFactory::withExportToolbox($switchLineOptions, true, true, true, 'switch-view-line');
$switchAreaOptions = PresetFactory::fromType(
    'area',
    $switchRows,
    rex_i18n::msg('echarts_demo_switch_chart_area'),
    true,
    ['#2a9d8f'],
    true,
    true,
    true,
    false
);
$switchAreaOptions = PresetFactory::withExportToolbox($switchAreaOptions, true, true, true, 'switch-view-area');
$switchGaugeOptions = PresetFactory::fromType(
    'gauge_simple',
    [['name' => 'KPI', 'value' => 79]],
    rex_i18n::msg('echarts_demo_switch_chart_gauge'),
    true,
    ['#e76f51'],
    true,
    false,
    true,
    false
);
$switchGaugeOptions = PresetFactory::withExportToolbox($switchGaugeOptions, true, true, true, 'switch-view-gauge');

$switchOptionsMap = [
    'bar' => $switchBarOptions,
    'line' => $switchLineOptions,
    'area' => $switchAreaOptions,
    'gauge_simple' => $switchGaugeOptions,
];
$switchOptionsJson = json_encode($switchOptionsMap, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$switchOptionsBase64 = is_string($switchOptionsJson) && $switchOptionsJson !== '' ? base64_encode($switchOptionsJson) : '';
$switchCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'Jan', 'value' => 98],
    ['name' => 'Feb', 'value' => 124],
    ['name' => 'Mar', 'value' => 149],
    ['name' => 'Apr', 'value' => 171],
    ['name' => 'May', 'value' => 205],
    ['name' => 'Jun', 'value' => 238],
];

$bar = PresetFactory::fromType('bar', $rows, 'Switch View Demo', true, [], true, true, true, false);
$line = PresetFactory::fromType('line', $rows, 'Switch View Demo', true, [], true, true, true, false);
$area = PresetFactory::fromType('area', $rows, 'Switch View Demo', true, [], true, true, true, false);

echo ChartRenderer::render($bar, 360, ['class' => 'js-echarts-switch-chart']);
// Buttons + JS wechseln zwischen $bar/$line/$area und zeigen SVG-Export im Toolbox-Menü.
PHP;

echo '<div class="panel panel-default" style="margin-bottom:20px">';
echo '<div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_demo_switch_title') . '</h3></div>';
echo '<div class="panel-body">';
echo '<p class="text-muted" style="margin-bottom:12px">' . rex_i18n::msg('echarts_demo_switch_desc') . '</p>';
echo '<div class="help-block" style="margin-top:0;margin-bottom:12px">' . rex_i18n::msg('echarts_demo_switch_export_hint') . '</div>';
echo '<div class="echarts-demo-switcher" data-echarts-switcher data-switch-default="bar" data-switch-options="' . rex_escape($switchOptionsBase64) . '">';
echo '<div class="echarts-demo-switcher-controls" role="group" style="margin-bottom:12px">';
echo '<button type="button" class="echarts-switch-button is-active" data-switch-type="bar" aria-pressed="true">' . rex_i18n::msg('echarts_demo_switch_btn_bar') . '</button>';
echo '<button type="button" class="echarts-switch-button" data-switch-type="line" aria-pressed="false">' . rex_i18n::msg('echarts_demo_switch_btn_line') . '</button>';
echo '<button type="button" class="echarts-switch-button" data-switch-type="area" aria-pressed="false">' . rex_i18n::msg('echarts_demo_switch_btn_area') . '</button>';
echo '<button type="button" class="echarts-switch-button" data-switch-type="gauge_simple" aria-pressed="false">' . rex_i18n::msg('echarts_demo_switch_btn_gauge') . '</button>';
echo '</div>';
echo ChartRenderer::render($switchBarOptions, 360, ['class' => 'js-echarts-switch-chart', 'style' => 'margin-top:8px']);
echo '</div>';
echo '<div class="help-block" style="margin-top:12px">' . rex_i18n::msg('echarts_demo_copy_hint') . '</div>';
echo '<pre style="font-size:12px;max-height:320px;overflow:auto">' . rex_escape($switchCode) . '</pre>';
echo '</div>';
echo '</div>';

$customOptions = [
    'title' => ['text' => rex_i18n::msg('echarts_demo_custom_mixed_title')],
    'tooltip' => ['trigger' => 'axis'],
    'legend' => ['data' => [rex_i18n::msg('echarts_demo_custom_sales'), rex_i18n::msg('echarts_demo_custom_budget')]],
    'xAxis' => ['type' => 'category', 'data' => $months],
    'yAxis' => [
        ['type' => 'value', 'name' => rex_i18n::msg('echarts_demo_custom_amount')],
        ['type' => 'value', 'name' => rex_i18n::msg('echarts_demo_custom_quote')],
    ],
    'series' => [
        [
            'name' => rex_i18n::msg('echarts_demo_custom_sales'),
            'type' => 'bar',
            'data' => [120, 132, 161, 184, 210, 232],
            'itemStyle' => ['color' => '#577590'],
        ],
        [
            'name' => rex_i18n::msg('echarts_demo_custom_budget'),
            'type' => 'line',
            'yAxisIndex' => 1,
            'smooth' => true,
            'data' => [0.62, 0.66, 0.71, 0.73, 0.78, 0.81],
            'itemStyle' => ['color' => '#f8961e'],
        ],
    ],
];
$customCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;

$options = [
    'title' => ['text' => 'Mix: Balken + Linie'],
    'tooltip' => ['trigger' => 'axis'],
    'legend' => ['data' => ['Sales', 'Budgetquote']],
    'xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']],
    'yAxis' => [
        ['type' => 'value', 'name' => 'EUR'],
        ['type' => 'value', 'name' => '%'],
    ],
    'series' => [
        ['name' => 'Sales', 'type' => 'bar', 'data' => [120, 132, 161, 184, 210, 232]],
        ['name' => 'Budgetquote', 'type' => 'line', 'yAxisIndex' => 1, 'data' => [0.62, 0.66, 0.71, 0.73, 0.78, 0.81]],
    ],
];

echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_custom_title'),
    rex_i18n::msg('echarts_demo_example_custom_desc'),
    $customOptions,
    $customCode
);

$scatterOptions = PresetFactory::fromType('scatter', $scatterRows, rex_i18n::msg('echarts_demo_chart_points'));
$scatterCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'P1', 'value' => 5.2],
    ['name' => 'P2', 'value' => 8.5],
    ['name' => 'P3', 'value' => 3.9],
    ['name' => 'P4', 'value' => 9.8],
    ['name' => 'P5', 'value' => 7.1],
    ['name' => 'P6', 'value' => 6.4],
];

$options = PresetFactory::fromType('scatter', $rows, 'Messpunkte');
echo ChartRenderer::render($options, 320);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_scatter_title'),
    rex_i18n::msg('echarts_demo_example_scatter_desc'),
    $scatterOptions,
    $scatterCode,
    320
);

$companyShareOptions = PresetFactory::fromType('donut', $companyShareRows, rex_i18n::msg('echarts_demo_company_share_chart_title'), true, ['#264653', '#2a9d8f', '#e9c46a', '#f4a261'], true, false, true, true);
$companyShareCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'Nordic Capital', 'value' => 34],
    ['name' => 'GreenVentures', 'value' => 26],
    ['name' => 'Founder Pool', 'value' => 22],
    ['name' => 'Mitarbeitende', 'value' => 18],
];

$options = PresetFactory::fromType('donut', $rows, 'Firmenanteile', true, [], true, false, true, true);
echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_company_share_title'),
    rex_i18n::msg('echarts_demo_example_company_share_desc'),
    $companyShareOptions,
    $companyShareCode
);

$stockOptions = PresetFactory::multiLine($stockYears, $stockSeries, rex_i18n::msg('echarts_demo_stock_chart_title'), true, ['#3a86ff', '#ff006e'], true, false, true);
$stockCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$years = ['2000', '2002', '2004', '2006', '2008', '2010', '2012', '2014', '2016', '2018', '2020', '2022', '2024', '2026'];
$series = [
    ['name' => 'SOLAR-X', 'values' => [24, 28, 33, 48, 38, 44, 52, 61, 74, 88, 95, 112, 129, 146]],
    ['name' => 'CLOUD-ONE', 'values' => [18, 22, 29, 36, 31, 41, 49, 59, 67, 79, 90, 104, 120, 134]],
];

$options = PresetFactory::multiLine($years, $series, 'Aktienkurse 2000-2026');
echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_stock_title'),
    rex_i18n::msg('echarts_demo_example_stock_desc'),
    $stockOptions,
    $stockCode
);

$waterOptions = PresetFactory::multiLine($waterYears, $waterSeries, rex_i18n::msg('echarts_demo_water_chart_title'), true, ['#0077b6', '#00b4d8', '#90e0ef'], true, false, true);
$waterCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$years = ['2000', '2005', '2010', '2015', '2020', '2026'];
$series = [
    ['name' => 'Single-Haushalt', 'values' => [129, 125, 121, 117, 113, 109]],
    ['name' => 'Familie (4 Pers.)', 'values' => [164, 159, 153, 149, 145, 141]],
    ['name' => 'WG (3 Pers.)', 'values' => [142, 139, 135, 131, 129, 126]],
];

$options = PresetFactory::multiLine($years, $series, 'Wasserverbrauch 2000-2026');
echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_water_title'),
    rex_i18n::msg('echarts_demo_example_water_desc'),
    $waterOptions,
    $waterCode
);

$rainCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;

$options = [
    'title' => ['text' => 'Regenmengen pro Monat'],
    'tooltip' => ['trigger' => 'axis'],
    'legend' => ['data' => ['Berlin', 'Hamburg', 'Muenchen']],
    'xAxis' => ['type' => 'category', 'data' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']],
    'yAxis' => ['type' => 'value', 'name' => 'mm'],
    'series' => [
        ['name' => 'Berlin', 'type' => 'bar', 'data' => [32, 28, 36, 41, 55, 68]],
        ['name' => 'Hamburg', 'type' => 'bar', 'data' => [54, 48, 57, 62, 73, 79]],
        ['name' => 'Muenchen', 'type' => 'bar', 'data' => [44, 39, 52, 59, 70, 84]],
    ],
];

echo ChartRenderer::render($options, 340);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_rain_title'),
    rex_i18n::msg('echarts_demo_example_rain_desc'),
    $rainOptions,
    $rainCode
);

$globalCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;

$options = [
    'title' => ['text' => 'Globaler Überblick'],
    'tooltip' => ['trigger' => 'item'],
    'legend' => ['data' => ['2026', '2030']],
    'radar' => [
        'indicator' => [
            ['name' => 'Europa', 'max' => 100],
            ['name' => 'Nordamerika', 'max' => 100],
            ['name' => 'Suedamerika', 'max' => 100],
            ['name' => 'Afrika', 'max' => 100],
            ['name' => 'Asien', 'max' => 100],
            ['name' => 'Ozeanien', 'max' => 100],
        ],
    ],
    'series' => [[
        'type' => 'radar',
        'data' => [
            ['name' => '2026', 'value' => [68, 72, 41, 36, 88, 27]],
            ['name' => '2030', 'value' => [74, 77, 46, 43, 93, 31]],
        ],
    ]],
];

echo ChartRenderer::render($options, 360);
PHP;
$renderExample(
    rex_i18n::msg('echarts_demo_example_global_title'),
    rex_i18n::msg('echarts_demo_example_global_desc'),
    $globalRadarOptions,
    $globalCode,
    360
);

echo '<div class="panel panel-success"><div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_demo_yform_builder_title') . '</h3></div><div class="panel-body">';
echo '<p>' . rex_i18n::msg('echarts_demo_yform_value_line') . ' <code>value|echarts_option|chart_json|Chart|360|0</code></p>';
echo '<p>' . rex_i18n::msg('echarts_demo_builder_line') . '</p>';
echo '</div></div>';
