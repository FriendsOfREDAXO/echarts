# Changelog

## Unreleased

- Node-basiertes Vendor-Tooling ergänzt (`package.json`, `scripts/sync-echarts-vendor.mjs`)
- `echarts.min.js` kann nun per `npm` oder `pnpm` aktualisiert und in `assets/` synchronisiert werden
- `assets/echarts.vendor-version.txt` dokumentiert die verwendete Vendor-Version
- CI-Workflow `.github/workflows/vendor-sync-check.yml` prüft, dass Vendor-Dateien nach Sync committed sind

## 1.0.0 (2026-07-12)

- Erstes Release des Apache-ECharts-AddOns
- Builder-Integration mit Element `ECharts`
- Eigener Builder-Feldtyp `echarts_option`
- YForm-Value `echarts_option` inklusive Bootstrap-Template
- PHP-Helper für Module/Templates
- Demo-Seite mit gängigen Chart-Typen
