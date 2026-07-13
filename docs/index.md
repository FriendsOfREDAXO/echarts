---
title: Apache ECharts AddOn für REDAXO
layout: default
---

# Apache ECharts AddOn

Apache ECharts für REDAXO mit Fokus auf Builder, YForm und modulare PHP-Nutzung.

## Was das AddOn kann

- Standard-Charts: Balken, Linie, Fläche, Pie, Donut, Scatter
- Erweiterte Demos: Nested Pie, Gauge-Varianten, Line Race, Geo-Maps
- Builder-Integration mit manueller Eingabe oder YForm-Datenquellen
- PHP-Rendering über `ChartRenderer::render()` und `PresetFactory`
- Export über Toolbox (SVG und optional PDF via PDFOut)

## Typische Anwendungsmöglichkeiten

- Dashboard für KPIs und Trends
- Vertriebs-/Service-Abdeckung auf Karten (aktiv, teilaktiv, inaktiv)
- Zeitreihen-Animationen (Line Race) für Entwicklungen über Perioden
- Vergleich mehrerer Produkte, Teams oder Regionen
- Redaktionelle Visualisierungen in REDAXO-Modulen

## Schnellstart in PHP

```php
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
    ['name' => 'Jan', 'value' => 120],
    ['name' => 'Feb', 'value' => 160],
    ['name' => 'Mär', 'value' => 185],
];

$options = PresetFactory::fromType('line', $rows, 'Umsatztrend');
echo ChartRenderer::render($options, 360);
```

## Line Race mit Viewport-Start (PHP-gesteuert)

`PresetFactory::lineRace()` unterstützt jetzt optionalen Start erst dann, wenn der Chart im Viewport sichtbar wird.

```php
<?php
$options = PresetFactory::lineRace(
    $years,
    $frames,
    'Line Race (Apache-Style)',
    true,
    ['#3d5a80', '#457b9d', '#2a9d8f', '#e76f51'],
    true,
    true,
    1000,
    true,
    true,
    false
);
```

Parameter am Ende:

- `autoPlay`: soll automatisch abgespielt werden
- `startInViewport`: Start erst bei Sichtbarkeit im Viewport
- `pauseWhenOutOfView`: bei Verlassen des Viewports pausieren

## Geo-Maps und eigene Gebietslogik

Für Geo-Maps nutzt das AddOn einen Loader mit `data-map-*`-Attributen.
Damit können auch eigene Regionen aus Kreis-/Ortslisten abgeleitet werden
(z. B. FVN-Fußballkreise als gruppierte NRW-Kreisgeometrien).

Wichtiger Hinweis:

- Aggregierte/abgeleitete Gebiete sind eine fachliche Näherung.
- Für rechtsverbindliche Grenzen sollten offizielle Geodaten verwendet werden.

## Quellen

- Apache ECharts: https://echarts.apache.org/
- Demo Line Race (Apache): https://echarts.apache.org/examples/en/editor.html?c=line-race
- Deutschland GeoJSON: https://github.com/isellsoap/deutschlandGeoJSON

## Lizenz

- AddOn: MIT (`LICENSE.md`)
- Apache ECharts: Apache License 2.0
