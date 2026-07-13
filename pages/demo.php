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

$lineRaceApacheCountries = ['Finland', 'France', 'Germany', 'Iceland', 'Norway', 'Poland', 'Russia', 'United Kingdom'];
$lineRaceApacheYears = [1950, 1960, 1970, 1980, 1990, 2000, 2010, 2020];
$lineRaceApacheIncomeByCountry = [
    'Finland' => [5600, 9200, 13800, 18800, 24700, 30100, 36700, 41800],
    'France' => [6200, 9800, 14500, 19700, 25800, 31400, 38100, 43600],
    'Germany' => [6000, 10100, 15400, 20900, 27100, 32900, 40200, 46800],
    'Iceland' => [5400, 9100, 14100, 19600, 26100, 33200, 41000, 49600],
    'Norway' => [6400, 10200, 15700, 22300, 30500, 38700, 48500, 59200],
    'Poland' => [3000, 4200, 6100, 7600, 9800, 13200, 18700, 24400],
    'Russia' => [4100, 5600, 7600, 9300, 11200, 14300, 19700, 26300],
    'United Kingdom' => [6700, 9900, 14700, 19400, 24300, 30100, 36400, 42200],
];

$lineRaceApacheRawData = [['Income', 'LifeExpectancy', 'Population', 'Country', 'Year']];
foreach ($lineRaceApacheCountries as $countryName) {
    $incomeValues = $lineRaceApacheIncomeByCountry[$countryName] ?? [];
    foreach ($lineRaceApacheYears as $yearIndex => $yearValue) {
        $incomeValue = (float) ($incomeValues[$yearIndex] ?? 0);
        $lineRaceApacheRawData[] = [
            $incomeValue,
            62 + ($yearIndex * 2),
            2_000_000 + ($yearIndex * 120_000),
            $countryName,
            $yearValue,
        ];
    }
}

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

$fvnVisualConfig = [
    'useGradients' => true,
    'gradientLighten' => 0.16,
    'gradientDarken' => 0.08,
    'hoverLighten' => 0.22,
    'hoverDarken' => 0.05,
    'hoverZoom' => true,
    'seriesShadowColor' => 'rgba(11, 33, 24, 0.38)',
    'seriesShadowBlur' => 18,
    'seriesShadowOffsetX' => 1,
    'seriesShadowOffsetY' => 8,
];

$fvnCircleBase = [
    ['name' => 'Düsseldorf', 'value' => 74, 'baseColor' => '#145243'],
    ['name' => 'Duisburg / Mülheim / Dinslaken', 'value' => 70, 'baseColor' => '#2a6f57'],
    ['name' => 'Oberhausen / Bottrop', 'value' => 58, 'baseColor' => '#2f8757'],
    ['name' => 'Grevenbroich / Neuss', 'value' => 63, 'baseColor' => '#4c9554'],
    ['name' => 'Kempen / Krefeld', 'value' => 57, 'baseColor' => '#4f9f70'],
    ['name' => 'Moers', 'value' => 55, 'baseColor' => '#4d8d3e'],
    ['name' => 'Kleve / Geldern', 'value' => 52, 'baseColor' => '#8bc95d'],
    ['name' => 'Rees / Bocholt', 'value' => 50, 'baseColor' => '#6cc486'],
    ['name' => 'Essen', 'value' => 62, 'baseColor' => '#8ecf77'],
    ['name' => 'Wuppertal / Niederberg', 'value' => 66, 'baseColor' => '#82c9a4'],
    ['name' => 'Solingen', 'value' => 61, 'baseColor' => '#a1d992'],
    ['name' => 'Remscheid', 'value' => 53, 'baseColor' => '#9ee5bf'],
];

$mixHex = static function (string $hex, string $targetHex, float $ratio): string {
    $norm = static function (string $color): array {
        $clean = ltrim(trim($color), '#');
        if (strlen($clean) === 3) {
            $clean = $clean[0] . $clean[0] . $clean[1] . $clean[1] . $clean[2] . $clean[2];
        }
        if (strlen($clean) !== 6) {
            return [0, 0, 0];
        }

        return [
            (int) hexdec(substr($clean, 0, 2)),
            (int) hexdec(substr($clean, 2, 2)),
            (int) hexdec(substr($clean, 4, 2)),
        ];
    };

    $from = $norm($hex);
    $target = $norm($targetHex);
    $mix = max(0.0, min(1.0, $ratio));

    $r = (int) round(($from[0] * (1.0 - $mix)) + ($target[0] * $mix));
    $g = (int) round(($from[1] * (1.0 - $mix)) + ($target[1] * $mix));
    $b = (int) round(($from[2] * (1.0 - $mix)) + ($target[2] * $mix));

    return sprintf('#%02x%02x%02x', $r, $g, $b);
};

$gradient = static function (string $fromColor, string $toColor): array {
    return [
        'type' => 'linear',
        'x' => 0,
        'y' => 0,
        'x2' => 1,
        'y2' => 1,
        'colorStops' => [
            ['offset' => 0, 'color' => $fromColor],
            ['offset' => 1, 'color' => $toColor],
        ],
        'global' => false,
    ];
};

$fvnCircleRows = [];
foreach ($fvnCircleBase as $entry) {
    $baseColor = (string) ($entry['baseColor'] ?? '#2f8f50');
    $gradStart = $mixHex($baseColor, '#ffffff', (float) $fvnVisualConfig['gradientLighten']);
    $gradEnd = $mixHex($baseColor, '#00150d', (float) $fvnVisualConfig['gradientDarken']);

    $hoverStart = $mixHex($baseColor, '#ffffff', (float) $fvnVisualConfig['hoverLighten']);
    $hoverEnd = $mixHex($baseColor, '#03110b', (float) $fvnVisualConfig['hoverDarken']);

    $areaColor = $fvnVisualConfig['useGradients']
        ? $gradient($gradStart, $gradEnd)
        : $baseColor;
    $hoverAreaColor = $fvnVisualConfig['useGradients']
        ? $gradient($hoverStart, $hoverEnd)
        : $hoverStart;

    $fvnCircleRows[] = [
        'name' => $entry['name'],
        'value' => $entry['value'],
        'itemStyle' => ['areaColor' => $areaColor],
        'emphasis' => [
            'itemStyle' => [
                'areaColor' => $hoverAreaColor,
            ],
        ],
    ];
}

$fvnCirclePointGroups = [
    ['name' => 'Düsseldorf', 'points' => [['coords' => [6.7763137, 51.2254018]], ['coords' => [6.9056079, 51.2209866]], ['coords' => [6.9777778, 51.2527778]], ['coords' => [6.8493503, 51.2973261]]]],
    ['name' => 'Duisburg / Mülheim / Dinslaken', 'points' => [['coords' => [6.759562, 51.434999]], ['coords' => [6.8829192, 51.4272925]], ['coords' => [6.7345106, 51.5623618]], ['coords' => [6.6811994, 51.5975224]], ['coords' => [6.7660319, 51.6414581]]]],
    ['name' => 'Oberhausen / Bottrop', 'points' => [['coords' => [6.8514435, 51.4696137]], ['coords' => [6.929204, 51.521581]]]],
    ['name' => 'Grevenbroich / Neuss', 'points' => [['coords' => [6.6916476, 51.1981778]], ['coords' => [6.5848937, 51.0862467]], ['coords' => [6.8416158, 51.0934389]], ['coords' => [6.6760958, 51.2652237]], ['coords' => [6.6193924, 51.226675]], ['coords' => [6.5143539, 51.1902651]], ['coords' => [6.5036893, 51.1017138]], ['coords' => [6.6978814, 51.0633574]]]],
    ['name' => 'Kempen / Krefeld', 'points' => [['coords' => [6.5623343, 51.3331205]], ['coords' => [6.4195011, 51.3642126]], ['coords' => [6.5446958, 51.2641433]], ['coords' => [6.45, 51.3167]], ['coords' => [6.2714171, 51.3155092]], ['coords' => [6.343738, 51.336077]], ['coords' => [6.3905476, 51.2562118]]]],
    ['name' => 'Moers', 'points' => [['coords' => [6.62843, 51.451283]], ['coords' => [6.547923, 51.5017981]], ['coords' => [6.6014097, 51.5458979]], ['coords' => [6.5467641, 51.4413742]], ['coords' => [6.5128805, 51.5767474]], ['coords' => [6.3760442, 51.6094069]], ['coords' => [6.4543203, 51.661519]]]],
    ['name' => 'Kleve / Geldern', 'points' => [['coords' => [6.1376008, 51.7895571]], ['coords' => [6.3228189, 51.5169736]], ['coords' => [6.2456273, 51.5802996]], ['coords' => [6.1593045, 51.6755765]], ['coords' => [6.2694388, 51.4439341]], ['coords' => [6.2428283, 51.8322137]], ['coords' => [6.2927546, 51.7388793]], ['coords' => [6.1932936, 51.7624802]], ['coords' => [6.0074359, 51.7895581]], ['coords' => [6.4245085, 51.5347321]], ['coords' => [6.3908707, 51.4533998]], ['coords' => [6.2011559, 51.6267298]], ['coords' => [6.2735726, 51.6686375]], ['coords' => [6.4690655, 51.4677722]]]],
    ['name' => 'Rees / Bocholt', 'points' => [['coords' => [6.6148669, 51.8382715]], ['coords' => [6.696091, 51.8363135]], ['coords' => [6.4614134, 51.8344194]], ['coords' => [6.3956605, 51.7581242]], ['coords' => [6.590865, 51.7306922]], ['coords' => [6.617087, 51.6576909]], ['coords' => [6.8391929, 51.6898739]]]],
    ['name' => 'Essen', 'points' => [['coords' => [7.0158171, 51.4582235]]]],
    ['name' => 'Wuppertal / Niederberg', 'points' => [['coords' => [7.1780374, 51.264018]], ['coords' => [7.0439912, 51.3406713]], ['coords' => [6.9710401, 51.32658]], ['coords' => [7.0328063, 51.2818569]]]],
    ['name' => 'Solingen', 'shrink' => 0.98, 'points' => [['coords' => [7.0845893, 51.1721629]], ['coords' => [7.0140304, 51.1059639]], ['coords' => [7.0085328, 51.1954956]]]],
    ['name' => 'Remscheid', 'shrink' => 0.98, 'points' => [['coords' => [7.1943544, 51.1798706]], ['coords' => [7.3571392, 51.2029228]], ['coords' => [7.3413999, 51.1504872]]]],
];

$nrwMunicipalityGeoJson = (static function (): array {
    $empty = ['type' => 'FeatureCollection', 'features' => []];

    $geoJsonRaw = rex_file::get(rex_path::addon('echarts', 'assets/maps/de-nrw-gemeinden.geo.json'));
    if (!is_string($geoJsonRaw) || $geoJsonRaw === '') {
        return $empty;
    }

    try {
        $municipalityGeoJson = json_decode($geoJsonRaw, true, 512, JSON_THROW_ON_ERROR);
    } catch (Throwable) {
        return $empty;
    }

    if (!is_array($municipalityGeoJson) || !isset($municipalityGeoJson['features']) || !is_array($municipalityGeoJson['features'])) {
        return $empty;
    }

    return $municipalityGeoJson;
})();

$fvnMergedGeoJson = (static function (array $municipalityGeoJson, array $pointGroups): array {
    $empty = ['type' => 'FeatureCollection', 'features' => []];

    if (!isset($municipalityGeoJson['features']) || !is_array($municipalityGeoJson['features'])) {
        return $empty;
    }

    $pointInRing = static function (float $x, float $y, array $ring): bool {
        $inside = false;
        $count = count($ring);
        if ($count < 3) {
            return false;
        }

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            if (!is_array($ring[$i]) || !is_array($ring[$j]) || count($ring[$i]) < 2 || count($ring[$j]) < 2) {
                continue;
            }
            $xi = (float) $ring[$i][0];
            $yi = (float) $ring[$i][1];
            $xj = (float) $ring[$j][0];
            $yj = (float) $ring[$j][1];

            $intersects = (($yi > $y) !== ($yj > $y))
                && ($x < (($xj - $xi) * ($y - $yi) / (($yj - $yi) === 0.0 ? 1e-12 : ($yj - $yi)) + $xi));
            if ($intersects) {
                $inside = !$inside;
            }
        }

        return $inside;
    };

    $pointInPolygon = static function (float $x, float $y, array $polygon) use ($pointInRing): bool {
        if (!isset($polygon[0]) || !is_array($polygon[0])) {
            return false;
        }

        if (!$pointInRing($x, $y, $polygon[0])) {
            return false;
        }

        $holes = array_slice($polygon, 1);
        foreach ($holes as $hole) {
            if (is_array($hole) && $pointInRing($x, $y, $hole)) {
                return false;
            }
        }

        return true;
    };

    $pointInGeometry = static function (float $x, float $y, array $geometry) use ($pointInPolygon): bool {
        if (!isset($geometry['type']) || !is_string($geometry['type']) || !isset($geometry['coordinates']) || !is_array($geometry['coordinates'])) {
            return false;
        }

        if ($geometry['type'] === 'Polygon') {
            return $pointInPolygon($x, $y, $geometry['coordinates']);
        }

        if ($geometry['type'] === 'MultiPolygon') {
            foreach ($geometry['coordinates'] as $polygon) {
                if (is_array($polygon) && $pointInPolygon($x, $y, $polygon)) {
                    return true;
                }
            }
        }

        return false;
    };

    $extractPolygons = static function (array $geometry): array {
        if (!isset($geometry['type']) || !is_string($geometry['type']) || !isset($geometry['coordinates']) || !is_array($geometry['coordinates'])) {
            return [];
        }

        if ($geometry['type'] === 'Polygon') {
            return [$geometry['coordinates']];
        }

        if ($geometry['type'] === 'MultiPolygon') {
            return $geometry['coordinates'];
        }

        return [];
    };

    $centroidFromGeometry = static function (array $geometry): ?array {
        if (!isset($geometry['coordinates']) || !is_array($geometry['coordinates'])) {
            return null;
        }

        $lonSum = 0.0;
        $latSum = 0.0;
        $count = 0;

        $collectRing = static function (array $ring) use (&$lonSum, &$latSum, &$count): void {
            foreach ($ring as $point) {
                if (!is_array($point) || count($point) < 2) {
                    continue;
                }
                $lonSum += (float) $point[0];
                $latSum += (float) $point[1];
                ++$count;
            }
        };

        if (($geometry['type'] ?? '') === 'Polygon') {
            if (isset($geometry['coordinates'][0]) && is_array($geometry['coordinates'][0])) {
                $collectRing($geometry['coordinates'][0]);
            }
        } elseif (($geometry['type'] ?? '') === 'MultiPolygon') {
            foreach ($geometry['coordinates'] as $polygon) {
                if (is_array($polygon) && isset($polygon[0]) && is_array($polygon[0])) {
                    $collectRing($polygon[0]);
                }
            }
        }

        if ($count === 0) {
            return null;
        }

        return [$lonSum / $count, $latSum / $count];
    };

    $groupPolygons = [];
    $groupCenters = [];
    foreach ($pointGroups as $group) {
        if (!is_array($group) || !isset($group['name']) || !is_string($group['name'])) {
            continue;
        }
        $groupPolygons[$group['name']] = [];

        if (isset($group['points']) && is_array($group['points'])) {
            $lonSum = 0.0;
            $latSum = 0.0;
            $count = 0;
            foreach ($group['points'] as $point) {
                if (!is_array($point) || !isset($point['coords']) || !is_array($point['coords']) || count($point['coords']) < 2) {
                    continue;
                }
                $lonSum += (float) $point['coords'][0];
                $latSum += (float) $point['coords'][1];
                ++$count;
            }
            if ($count > 0) {
                $groupCenters[$group['name']] = [$lonSum / $count, $latSum / $count];
            }
        }
    }

    if ($groupPolygons === []) {
        return $empty;
    }

    $assignedMunicipalityIndexes = [];
    foreach ($pointGroups as $group) {
        if (!is_array($group) || !isset($group['name']) || !is_string($group['name']) || !isset($group['points']) || !is_array($group['points'])) {
            continue;
        }

        $groupName = $group['name'];
        $matchedMunicipalityIndexes = [];

        foreach ($group['points'] as $point) {
            if (!is_array($point) || !isset($point['coords']) || !is_array($point['coords']) || count($point['coords']) < 2) {
                continue;
            }

            $lon = (float) $point['coords'][0];
            $lat = (float) $point['coords'][1];

            foreach ($municipalityGeoJson['features'] as $featureIndex => $feature) {
                if (isset($matchedMunicipalityIndexes[$featureIndex])) {
                    continue;
                }

                if (!is_array($feature) || !isset($feature['geometry']) || !is_array($feature['geometry'])) {
                    continue;
                }

                if ($pointInGeometry($lon, $lat, $feature['geometry'])) {
                    $matchedMunicipalityIndexes[$featureIndex] = true;
                    $assignedMunicipalityIndexes[$featureIndex] = true;
                }
            }
        }

        foreach (array_keys($matchedMunicipalityIndexes) as $featureIndex) {
            $feature = $municipalityGeoJson['features'][$featureIndex] ?? null;
            if (!is_array($feature) || !isset($feature['geometry']) || !is_array($feature['geometry'])) {
                continue;
            }

            $polygons = $extractPolygons($feature['geometry']);
            foreach ($polygons as $polygon) {
                if (is_array($polygon) && isset($polygon[0]) && is_array($polygon[0]) && $polygon[0] !== []) {
                    $groupPolygons[$groupName][] = [$polygon[0]];
                }
            }
        }
    }

    // Fallback: fehlende Gemeinden nur in der Naehe eines Kreiszentrums zuordnen.
    // So vermeiden wir, dass entfernte Ost-NRW-Flaechen in einen FVN-Kreis rutschen.
    $maxFallbackDistanceSquared = 0.05;

    foreach ($municipalityGeoJson['features'] as $featureIndex => $feature) {
        if (isset($assignedMunicipalityIndexes[$featureIndex])) {
            continue;
        }

        if (!is_array($feature) || !isset($feature['geometry']) || !is_array($feature['geometry'])) {
            continue;
        }

        $centroid = $centroidFromGeometry($feature['geometry']);
        if ($centroid === null) {
            continue;
        }

        $bestGroupName = null;
        $bestDistance = INF;
        foreach ($groupCenters as $groupName => $center) {
            $dx = $centroid[0] - $center[0];
            $dy = $centroid[1] - $center[1];
            $distance = ($dx * $dx) + ($dy * $dy);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $bestGroupName = $groupName;
            }
        }

        if ($bestGroupName === null || !isset($groupPolygons[$bestGroupName])) {
            continue;
        }

        if ($bestDistance > $maxFallbackDistanceSquared) {
            continue;
        }

        $polygons = $extractPolygons($feature['geometry']);
        foreach ($polygons as $polygon) {
            if (is_array($polygon) && isset($polygon[0]) && is_array($polygon[0]) && $polygon[0] !== []) {
                $groupPolygons[$bestGroupName][] = [$polygon[0]];
            }
        }
    }

    $features = [];
    foreach ($pointGroups as $group) {
        if (!is_array($group) || !isset($group['name']) || !is_string($group['name'])) {
            continue;
        }

        $groupName = $group['name'];
        if (!isset($groupPolygons[$groupName]) || $groupPolygons[$groupName] === []) {
            continue;
        }

        $features[] = [
            'type' => 'Feature',
            'properties' => ['name' => $groupName],
            'geometry' => [
                'type' => 'MultiPolygon',
                'coordinates' => $groupPolygons[$groupName],
            ],
        ];
    }

    return [
        'type' => 'FeatureCollection',
        'features' => $features,
    ];
})($nrwMunicipalityGeoJson, $fvnCirclePointGroups);

$fvnCircleMetaByName = [];
foreach ($fvnCircleRows as $row) {
    if (!is_array($row) || !isset($row['name']) || !is_string($row['name'])) {
        continue;
    }

    $fvnCircleMetaByName[$row['name']] = [
        'value' => isset($row['value']) ? (int) $row['value'] : 0,
    ];
}

$fvnLabelLineData = [];
$fvnLabelPointData = [];
$fvnContourShadowGeoJson = ['type' => 'FeatureCollection', 'features' => []];

$fvnCentroidByName = (static function (array $merged): array {
    $result = [];

    foreach ($merged['features'] ?? [] as $feature) {
        if (!is_array($feature) || !isset($feature['properties']['name']) || !is_string($feature['properties']['name'])) {
            continue;
        }

        $name = $feature['properties']['name'];
        $geometry = $feature['geometry'] ?? null;
        if (!is_array($geometry) || !isset($geometry['type']) || !isset($geometry['coordinates']) || !is_array($geometry['coordinates'])) {
            continue;
        }

        $lonSum = 0.0;
        $latSum = 0.0;
        $count = 0;

        $collectRing = static function (array $ring) use (&$lonSum, &$latSum, &$count): void {
            foreach ($ring as $point) {
                if (!is_array($point) || count($point) < 2) {
                    continue;
                }
                $lonSum += (float) $point[0];
                $latSum += (float) $point[1];
                ++$count;
            }
        };

        if ($geometry['type'] === 'Polygon') {
            if (isset($geometry['coordinates'][0]) && is_array($geometry['coordinates'][0])) {
                $collectRing($geometry['coordinates'][0]);
            }
        } elseif ($geometry['type'] === 'MultiPolygon') {
            foreach ($geometry['coordinates'] as $polygon) {
                if (is_array($polygon) && isset($polygon[0]) && is_array($polygon[0])) {
                    $collectRing($polygon[0]);
                }
            }
        }

        if ($count === 0) {
            continue;
        }

        $result[$name] = [
            'lon' => $lonSum / $count,
            'lat' => $latSum / $count,
        ];
    }

    return $result;
})($fvnMergedGeoJson);

if ($fvnCentroidByName !== []) {
    $fvnLabelPlacements = [
        'Rees / Bocholt' => ['labelLon' => 6.63, 'labelLat' => 51.86, 'position' => 'inside'],
        'Kleve / Geldern' => ['labelLon' => 6.20, 'labelLat' => 51.68, 'position' => 'inside'],
        'Kempen / Krefeld' => ['labelLon' => 6.37, 'labelLat' => 51.19, 'position' => 'inside'],
        'Grevenbroich / Neuss' => ['labelLon' => 6.67, 'labelLat' => 50.99, 'position' => 'inside'],
        'Moers' => ['labelLon' => 6.57, 'labelLat' => 51.57, 'position' => 'inside'],
        'Oberhausen / Bottrop' => ['labelLon' => 7.00, 'labelLat' => 51.52, 'position' => 'inside'],
        'Duisburg / Mülheim / Dinslaken' => ['labelLon' => 6.84, 'labelLat' => 51.40, 'position' => 'inside'],
        'Essen' => ['labelLon' => 7.30, 'labelLat' => 51.42, 'position' => 'inside'],
        'Wuppertal / Niederberg' => ['labelLon' => 7.18, 'labelLat' => 51.18, 'position' => 'inside'],
        'Remscheid' => ['labelLon' => 7.31, 'labelLat' => 51.02, 'position' => 'inside'],
        'Düsseldorf' => ['labelLon' => 6.86, 'labelLat' => 51.16, 'position' => 'inside'],
        'Solingen' => ['labelLon' => 7.08, 'labelLat' => 50.88, 'position' => 'inside'],
    ];

    foreach ($fvnCentroidByName as $name => $center) {
        $placed = $fvnLabelPlacements[$name] ?? [
            'labelLon' => (float) $center['lon'],
            'labelLat' => (float) $center['lat'],
            'position' => 'inside',
        ];

        $value = $fvnCircleMetaByName[$name]['value'] ?? 0;
        $fvnLabelPointData[] = [
            'name' => $name,
            'value' => [$placed['labelLon'], $placed['labelLat'], $value],
            'label' => [
                'position' => $placed['position'],
            ],
        ];
    }
}

if (($fvnMergedGeoJson['features'] ?? []) !== []) {
    $allPolygons = [];
    foreach ($fvnMergedGeoJson['features'] as $feature) {
        if (!is_array($feature) || !isset($feature['geometry']['type']) || !isset($feature['geometry']['coordinates']) || !is_array($feature['geometry']['coordinates'])) {
            continue;
        }

        if ($feature['geometry']['type'] === 'Polygon') {
            $allPolygons[] = $feature['geometry']['coordinates'];
            continue;
        }

        if ($feature['geometry']['type'] === 'MultiPolygon') {
            foreach ($feature['geometry']['coordinates'] as $polygon) {
                if (is_array($polygon)) {
                    $allPolygons[] = $polygon;
                }
            }
        }
    }

    if ($allPolygons !== []) {
        $fvnContourShadowGeoJson = [
            'type' => 'FeatureCollection',
            'features' => [[
                'type' => 'Feature',
                'properties' => ['name' => 'FVN-Kontur'],
                'geometry' => [
                    'type' => 'MultiPolygon',
                    'coordinates' => $allPolygons,
                ],
            ]],
        ];
    }
}

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

$options = PresetFactory::multiLine(
    $months,
    $series,
    'Quartalsvergleich',
    true,
    ['#1f77b4', '#2ca02c', '#ff7f0e'],
    true,
    false,
    true
);

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

$lineRaceApacheDatasetWithFilters = [];
$lineRaceApacheSeriesList = [];
foreach ($lineRaceApacheCountries as $countryName) {
    $datasetId = 'dataset_' . str_replace(' ', '_', strtolower($countryName));
    $lineRaceApacheDatasetWithFilters[] = [
        'id' => $datasetId,
        'fromDatasetId' => 'dataset_raw',
        'transform' => [
            'type' => 'filter',
            'config' => [
                'and' => [
                    ['dimension' => 'Year', 'gte' => 1950],
                    ['dimension' => 'Country', '=' => $countryName],
                ],
            ],
        ],
    ];

    $lineRaceApacheSeriesList[] = [
        'type' => 'line',
        'datasetId' => $datasetId,
        'showSymbol' => false,
        'name' => $countryName,
        'endLabel' => [
            'show' => true,
            'formatter' => '{@[3]}: {@[0]}',
        ],
        'labelLayout' => ['moveOverlap' => 'shiftY'],
        'emphasis' => ['focus' => 'series'],
        'encode' => [
            'x' => 'Year',
            'y' => 'Income',
            'label' => ['Country', 'Income'],
            'itemName' => 'Year',
            'tooltip' => ['Income'],
        ],
    ];
}

$lineRaceApacheOptions = [
    'animationDuration' => 10000,
    'dataset' => array_merge(
        [[
            'id' => 'dataset_raw',
            'source' => $lineRaceApacheRawData,
        ]],
        $lineRaceApacheDatasetWithFilters
    ),
    'title' => [
        'text' => rex_i18n::msg('echarts_demo_line_race_apache_chart_title'),
    ],
    'tooltip' => [
        'order' => 'valueDesc',
        'trigger' => 'axis',
    ],
    'xAxis' => [
        'type' => 'category',
        'nameLocation' => 'middle',
    ],
    'yAxis' => [
        'name' => 'Income',
    ],
    'grid' => ['right' => 140],
    'series' => $lineRaceApacheSeriesList,
    '_echartsAddon' => [
        'startInViewport' => true,
    ],
];

$lineRaceApacheOptions = PresetFactory::withExportToolbox($lineRaceApacheOptions, true, true, true, 'line-race-apache-dataset');
$lineRaceApacheCode = <<<'PHP'
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;

$countries = ['Finland', 'France', 'Germany', 'Iceland', 'Norway', 'Poland', 'Russia', 'United Kingdom'];

$rawData = [
    ['Income', 'LifeExpectancy', 'Population', 'Country', 'Year'],
    [5600, 62, 2000000, 'Finland', 1950],
    [9200, 64, 2120000, 'Finland', 1960],
    // ... weitere Zeilen je Land/Jahr
];

$datasetWithFilters = [];
$seriesList = [];
foreach ($countries as $country) {
    $datasetId = 'dataset_' . strtolower(str_replace(' ', '_', $country));

    $datasetWithFilters[] = [
        'id' => $datasetId,
        'fromDatasetId' => 'dataset_raw',
        'transform' => [
            'type' => 'filter',
            'config' => [
                'and' => [
                    ['dimension' => 'Year', 'gte' => 1950],
                    ['dimension' => 'Country', '=' => $country],
                ],
            ],
        ],
    ];

    $seriesList[] = [
        'type' => 'line',
        'datasetId' => $datasetId,
        'showSymbol' => false,
        'name' => $country,
        'endLabel' => ['show' => true, 'formatter' => '{@[3]}: {@[0]}'],
        'labelLayout' => ['moveOverlap' => 'shiftY'],
        'emphasis' => ['focus' => 'series'],
        'encode' => [
            'x' => 'Year',
            'y' => 'Income',
            'label' => ['Country', 'Income'],
            'itemName' => 'Year',
            'tooltip' => ['Income'],
        ],
    ];
}

// Inspiration: https://echarts.apache.org/examples/en/editor.html?c=line-race
// Neu: Start erst, wenn der Chart im Viewport sichtbar ist.
$options = [
    'animationDuration' => 10000,
    'dataset' => array_merge([
        ['id' => 'dataset_raw', 'source' => $rawData],
    ], $datasetWithFilters),
    'title' => ['text' => 'Line Race (Apache-style)'],
    'tooltip' => ['order' => 'valueDesc', 'trigger' => 'axis'],
    'xAxis' => ['type' => 'category', 'nameLocation' => 'middle'],
    'yAxis' => ['name' => 'Income'],
    'grid' => ['right' => 140],
    'series' => $seriesList,
    '_echartsAddon' => ['startInViewport' => true],
];
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
    'tooltip' => [
        'trigger' => 'item',
        'formatter' => '{b}<br/>Anzahl Vereine: {c}',
    ],
    '_echartsAddon' => [
        'labelHoverSync' => [
            'mapSeriesName' => rex_i18n::msg('echarts_demo_fvn_series_title'),
            'labelSeriesName' => 'FVN Innenlabels',
        ],
    ],
    'geo' => [
        'map' => 'de_fvn_kreise_php_merge',
        'roam' => false,
        'silent' => true,
        'layoutCenter' => ['46.5%', '53%'],
        'layoutSize' => '87%',
        'itemStyle' => [
            'areaColor' => 'transparent',
            'borderColor' => 'transparent',
        ],
        'label' => ['show' => false],
        'emphasis' => [
            'disabled' => true,
        ],
    ],
    'series' => [
        [
            'name' => 'FVN-Kontur-Schatten',
            'type' => 'map',
            'map' => 'de_fvn_kontur_shadow',
            'layoutCenter' => ['46.5%', '53%'],
            'layoutSize' => '87%',
            'silent' => true,
            'tooltip' => ['show' => false],
            'label' => ['show' => false],
            'zlevel' => 1,
            'z' => 1,
            'itemStyle' => [
                'areaColor' => 'rgba(255, 255, 255, 0)',
                'borderColor' => 'transparent',
                'borderWidth' => 0,
                'shadowColor' => 'rgba(0, 0, 0, 0.18)',
                'shadowBlur' => 24,
                'shadowOffsetX' => 0,
                'shadowOffsetY' => 10,
            ],
            'data' => [['name' => 'FVN-Kontur', 'value' => 1]],
        ],
        [
            'name' => rex_i18n::msg('echarts_demo_fvn_series_title'),
            'type' => 'map',
            'map' => 'de_fvn_kreise_php_merge',
            'layoutCenter' => ['46.5%', '53%'],
            'layoutSize' => '87%',
            'zlevel' => 2,
            'z' => 3,
            'animation' => true,
            'animationDurationUpdate' => 320,
            'animationEasingUpdate' => 'cubicOut',
            'label' => ['show' => false],
            'itemStyle' => [
                'borderColor' => 'rgba(18, 54, 41, 0.42)',
                'borderWidth' => 0.9,
            ],
            'emphasis' => [
                'scale' => (bool) $fvnVisualConfig['hoverZoom'],
                'scaleSize' => 8,
                'focus' => 'self',
                'blurScope' => 'coordinateSystem',
                'label' => ['show' => false],
                'itemStyle' => [
                    'borderColor' => 'rgba(14, 44, 33, 0.55)',
                    'borderWidth' => 1.1,
                    'shadowColor' => 'rgba(0, 0, 0, 0.26)',
                    'shadowBlur' => 18,
                    'shadowOffsetX' => 0,
                    'shadowOffsetY' => 7,
                ],
            ],
            'blur' => [
                'itemStyle' => [
                    'opacity' => 0.86,
                ],
                'label' => [
                    'opacity' => 0.68,
                ],
            ],
            'data' => $fvnCircleRows,
        ],
        [
            'name' => 'FVN Innenlabels',
            'type' => 'scatter',
            'coordinateSystem' => 'geo',
            'geoIndex' => 0,
            'zlevel' => 3,
            'z' => 4,
            'silent' => true,
            'tooltip' => ['show' => false],
            'symbolSize' => 1,
            'itemStyle' => ['color' => 'transparent'],
            'label' => [
                'show' => true,
                'position' => 'inside',
                'color' => '#ffffff',
                'fontSize' => 8,
                'fontWeight' => '600',
                'backgroundColor' => 'rgba(0, 0, 0, 0.72)',
                'padding' => [1, 5],
                'borderRadius' => 5,
                'formatter' => '{b}',
            ],
            'emphasis' => [
                'disabled' => false,
                'label' => [
                    'show' => true,
                    'color' => '#ffffff',
                    'fontSize' => 11,
                    'fontWeight' => '600',
                    'backgroundColor' => 'rgba(0, 0, 0, 0.88)',
                    'padding' => [3, 7],
                ],
            ],
            'data' => $fvnLabelPointData,
        ],
    ],
];
$fvnOptionsJson = json_encode($fvnOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$fvnOptionsBase64 = is_string($fvnOptionsJson) && $fvnOptionsJson !== '' ? base64_encode($fvnOptionsJson) : '';
$fvnMergedGeoJsonJson = json_encode($fvnMergedGeoJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$fvnMergedGeoJsonBase64 = is_string($fvnMergedGeoJsonJson) && $fvnMergedGeoJsonJson !== '' ? base64_encode($fvnMergedGeoJsonJson) : '';
$fvnContourShadowGeoJsonJson = json_encode($fvnContourShadowGeoJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$fvnContourShadowGeoJsonBase64 = is_string($fvnContourShadowGeoJsonJson) && $fvnContourShadowGeoJsonJson !== '' ? base64_encode($fvnContourShadowGeoJsonJson) : '';
$fvnExtraMaps = [['name' => 'de_fvn_kontur_shadow', 'geoJson' => $fvnContourShadowGeoJson]];
$fvnExtraMapsJson = json_encode($fvnExtraMaps, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$fvnExtraMapsBase64 = is_string($fvnExtraMapsJson) && $fvnExtraMapsJson !== '' ? base64_encode($fvnExtraMapsJson) : '';
$fvnCode = <<<'PHP'
<?php
// Gemeinden per PHP je FVN-Kreis zusammenfassen (eine Feature-Fläche pro Kreis).

// $mergedGeoJson entsteht in PHP (Dissolve nach Kreisname).
// Farben, Verlaeufe, Hover-Zoom und zentrierte Labels sind zentral in $fvnVisualConfig steuerbar.

$options = [
    'tooltip' => [
        'trigger' => 'item',
        'formatter' => '{b}<br/>Anzahl Vereine: {c}',
    ],
    'series' => [
        [
            'type' => 'map',
            'map' => 'de_fvn_kreise_php_merge',
            'animationDurationUpdate' => 320,
            'animationEasingUpdate' => 'cubicOut',
            'label' => [
                'show' => true,
                'position' => 'inside',
                'color' => '#fff',
                'fontSize' => 8,
                'backgroundColor' => 'rgba(0,0,0,0.72)',
                'padding' => [1, 5],
                'borderRadius' => 5,
                'formatter' => '{b}',
            ],
            'itemStyle' => [
                'borderWidth' => 0,
                'borderColor' => 'rgba(18, 54, 41, 0.42)',
                'borderWidth' => 0.9,
            ],
            'emphasis' => [
                'scale' => true,
                'scaleSize' => 8,
                'focus' => 'self',
                'label' => [
                    'show' => true,
                    'fontSize' => 11,
                    'backgroundColor' => 'rgba(0,0,0,0.88)',
                    'padding' => [3, 7],
                ],
            ],
            'data' => [
                [
                    'name' => 'Düsseldorf',
                    'value' => 74,
                    'itemStyle' => [
                        'areaColor' => [
                            'type' => 'linear',
                            'x' => 0,
                            'y' => 0,
                            'x2' => 1,
                            'y2' => 1,
                            'colorStops' => [
                                ['offset' => 0, 'color' => '#2d7359'],
                                ['offset' => 1, 'color' => '#0b3d2e'],
                            ],
                        ],
                    ],
                    'emphasis' => [
                        'itemStyle' => [
                            'areaColor' => [
                                'type' => 'linear',
                                'x' => 0,
                                'y' => 0,
                                'x2' => 1,
                                'y2' => 1,
                                'colorStops' => [
                                    ['offset' => 0, 'color' => '#6db89a'],
                                    ['offset' => 1, 'color' => '#14553f'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Solingen',
                    'value' => 61,
                    'itemStyle' => [
                        'areaColor' => [
                            'type' => 'linear',
                            'x' => 0,
                            'y' => 0,
                            'x2' => 1,
                            'y2' => 1,
                            'colorStops' => [
                                ['offset' => 0, 'color' => '#8ed5a8'],
                                ['offset' => 1, 'color' => '#2f8f50'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'type' => 'lines',
            'coordinateSystem' => 'geo',
            'polyline' => true,
            'lineStyle' => [
                'color' => 'rgba(124, 190, 226, 0.9)',
                'width' => 7,
            ],
            'data' => [[
                'coords' => [
                    [6.393, 51.771],
                    [6.430, 51.726],
                    [6.474, 51.659],
                    [6.552, 51.657],
                    [6.620, 51.659],
                    [6.667, 51.628],
                    [6.688, 51.596],
                    [6.709, 51.579],
                    [6.734, 51.561],
                    [6.741, 51.520],
                    [6.730, 51.454],
                    [6.690, 51.410],
                    [6.647, 51.351],
                    [6.680, 51.329],
                    [6.723, 51.299],
                    [6.746, 51.259],
                    [6.772, 51.227],
                    [6.750, 51.212],
                    [6.708, 51.198],
                    [6.734, 51.165],
                    [6.815, 51.120],
                    [6.862, 51.101],
                    [6.894, 51.091],
                ],
            ]],
        ],
    ],
];

$optionsBase64 = base64_encode((string) json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$geoJsonBase64 = base64_encode((string) json_encode($mergedGeoJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo '<div class="js-echarts-geo-map"'
    . ' data-map-name="de_fvn_kreise_php_merge"'
    . ' data-map-geojson="' . $geoJsonBase64 . '"'
    . ' data-map-options="' . $optionsBase64 . '"'
    . ' style="height:620px"'
    . '></div>';
PHP;

echo '<div class="panel panel-default" style="margin-bottom:20px">';
echo '<div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_demo_example_fvn_title') . '</h3></div>';
echo '<div class="panel-body">';
echo '<p class="text-muted" style="margin-bottom:14px">' . rex_i18n::msg('echarts_demo_example_fvn_desc') . '</p>';
echo '<div class="row">';
echo '<div class="col-md-8">';
echo '<div class="js-echarts-geo-map" style="height:620px" data-map-name="de_fvn_kreise_php_merge" data-map-geojson="' . rex_escape($fvnMergedGeoJsonBase64) . '" data-map-extra-maps="' . rex_escape($fvnExtraMapsBase64) . '" data-map-options="' . rex_escape($fvnOptionsBase64) . '"></div>';
echo '</div>';
echo '<div class="col-md-4">';
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
