# Apache ECharts AddOn

Dieses AddOn integriert Apache ECharts in REDAXO mit Fokus auf:

- Builder-Element für gängige Chart-Typen
- YForm-Value für Chart-Optionen als JSON
- Nutzung in Modulen und Templates per PHP-Helper
- Demo-Seite im Backend

Projektseite Apache ECharts:

- https://echarts.apache.org/

## Features

- Chart-Typen: Balken, Linie, Fläche, Pie, Donut, Scatter
- Datenquellen im Builder:
  - Manuelle Werte im Format `Name|Wert|Link(optional)|Tooltip(optional)`
  - YForm-Tabelle (Label-/Value-Feld)
- Optionale volle JSON-Optionen als Override
- Automatisches Frontend-Asset-Loading bei erkannter Chart-Ausgabe

## Vendor Update (npm/pnpm)

Für `echarts.min.js` gibt es ein kleines Node-Tooling direkt im AddOn.

Arbeitsverzeichnis:

`public/redaxo/src/addons/echarts`

### Erstinstallation + Sync

Mit npm:

```bash
npm run vendor:install
```

Mit pnpm:

```bash
pnpm install
pnpm run vendor:sync
```

### ECharts auf aktuelle Version aktualisieren

Mit npm:

```bash
npm run vendor:update
```

Mit pnpm:

```bash
pnpm up echarts
pnpm run vendor:sync
```

Ergebnis:

- `assets/echarts.min.js` wird aus `node_modules/echarts/dist/echarts.min.js` aktualisiert
- `assets/echarts.vendor-version.txt` enthält die verwendete Vendor-Version
- `package-lock.json` hält die installierte Version reproduzierbar fest

CI-Prüfung:

- Workflow: `.github/workflows/vendor-sync-check.yml`
- Führt `npm ci` + `npm run vendor:sync` aus
- Schlägt fehl, wenn `assets/echarts.min.js` oder `assets/echarts.vendor-version.txt`
  nach dem Sync Änderungen haben (also nicht committed sind)

## Frontend Einbindung

### 1) Standardfall: automatisch

Wenn im finalen Frontend-HTML mindestens ein Chart-Container mit
`data-echarts-options` vorkommt, bindet das AddOn die Assets automatisch ein:

- `echarts-addon.css` im `<head>`
- `echarts.min.js` und `echarts-addon.js` vor `</body>`

Du musst dann in der Regel nur den Chart-HTML-Output rendern, z. B. in Modul,
Template oder Fragment.

Automatik optional deaktivieren:

Backend: `ECharts -> Einstellungen` und Checkbox
`Frontend-Assets automatisch einbinden` deaktivieren.

Danach bitte die Assets manuell einbinden (siehe Punkt 3).

### 2) Chart im Template/Modul per PHP ausgeben

```php
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
  ['name' => 'Q1', 'value' => 120],
  ['name' => 'Q2', 'value' => 180],
  ['name' => 'Q3', 'value' => 150],
  ['name' => 'Q4', 'value' => 210],
];

$options = PresetFactory::fromType('bar', $rows, 'Umsatz 2026');
echo ChartRenderer::render($options, 380);
```

### 3) Manueller Asset-Fallback (nur falls nötig)

Nur verwenden, wenn der normale OUTPUT_FILTER bei dir nicht greift
(Spezial-Response, eigenes Rendering ohne `</head>`/`</body>` etc.) oder wenn
die Automatik per Config deaktiviert wurde.

```php
<?php
$addon = rex_addon::get('echarts');
echo '<link rel="stylesheet" href="' . $addon->getAssetsUrl('echarts-addon.css') . '">';
echo '<script defer src="' . $addon->getAssetsUrl('echarts.min.js') . '"></script>';
echo '<script defer src="' . $addon->getAssetsUrl('echarts-addon.js') . '"></script>';
```

## Manuelle Daten (Builder)

Interaktive Eingabe (Repeater):

- Datenpunkte als Liste mit `Label`, `Wert`, optional `Link (smart_link)`, optional `Tooltip`
- Farbe optional direkt pro Datenpunkt (Color-Picker)
- Optionale globale Standardfarbe als Fallback

CSV als dritte Eingabevariante:

- Datenquelle auf `CSV` stellen
- CSV-Inhalt einfügen (mit oder ohne Header)
- Trennzeichen wählen (`,`, `;`, `Tab`)
- Optional: Header aktivieren/deaktivieren

Empfohlene Header-Spalten:

- `label`, `value`, `link`, `tooltip`, `color`, `has_link`, `has_color`

Beispiel:

```csv
label,value,tooltip,color,has_color
Q1,120,Start,#111111,1
Q2,180,Wachstum,,0
```

Globale Optionen liegen im Modal `Einstellungen & Globals`:

- globale Standardfarbe
- Anzeigeoptionen (Legende, Tooltip, Labels, Grid)
- YForm-Quellfelder
- JSON-Override

Priorität der Farben:

- Datenpunkt-Farbe überschreibt alles
- Wenn keine Datenpunkt-Farbe gesetzt ist, wird die globale Standardfarbe genutzt
- Wenn auch diese leer ist, greift die automatische Default-Palette

Sichtbarkeit im Builder (conditional fields):

- Bei `Datenquelle = Manuell` werden nur manuelle Daten-/Farbfelder angezeigt
- Bei `Datenquelle = YForm` werden nur YForm-Felder angezeigt

Link-Regeln:

- SmartLink im Repeater unterstützt alle Typen: `intern`, `url`, `media`, `mail`, `tel`, `yform`
- SmartLink-Optionen werden berücksichtigt (z. B. PDF.js bei Media-PDF)
- Externe Links werden im neuen Tab geöffnet, interne im selben Tab

Beim Klick auf den Datenpunkt wird der Link geöffnet, wenn einer gesetzt ist.
Tooltip-Text wird angezeigt, wenn eine Tooltip-Spalte befüllt ist.

## Modul-Output Beispiel

```php
<?php
use FriendsOfREDAXO\ECharts\ChartRenderer;
use FriendsOfREDAXO\ECharts\PresetFactory;

$rows = [
  ['name' => 'Jan', 'value' => 120],
  ['name' => 'Feb', 'value' => 160],
  ['name' => 'Mär', 'value' => 185],
];

echo ChartRenderer::render(
  PresetFactory::fromType('line', $rows, 'Umsatztrend'),
  360
);
```

## YForm-Value

Definition:

`value|echarts_option|name|label|[height]|[required]|[notice]`

Beispiel:

`value|echarts_option|chart_json|Chart JSON|360|1|Bitte gültiges JSON eintragen`

## Builder

Elementname: `ECharts`

Wichtige Felder:

- Chart-Typ
- Höhe
- Datenquelle (manuell oder YForm)
- JSON-Override (`echarts_option`-Feldtyp)

## Optionale Demo-Daten (YForm)

Im Backend unter Demo kann eine optionale Beispieltabelle angelegt werden:

- Tabelle: `rex_echarts_demo_data`
- Felder für Builder-YForm-Quelle: `name` (Label), `value` (Wert)

Wichtig: Die Demo-Daten sind bewusst optional und jederzeit wieder entfernbar.
Über den Button „Demo-Daten entfernen“ wird die Tabelle komplett gelöscht.

## Hinweis

Das AddOn nutzt eine lokale `echarts.min.js` im Addon-Asset-Ordner.

## Lizenz

AddOn-Lizenz:

- MIT
- siehe `LICENSE.md`

Vendor (Apache ECharts):

- Projekt: https://echarts.apache.org/
- Lizenz: Apache License 2.0
- Lizenztext: https://www.apache.org/licenses/LICENSE-2.0
