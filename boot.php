<?php

declare(strict_types=1);

use FriendsOfREDAXO\ECharts\Fields\EChartsOptionField;

if (\rex_addon::get('builder')->isAvailable()) {
    require_once __DIR__ . '/lib/Fields/EChartsOptionField.php';

    if (class_exists(\FriendsOfREDAXO\Builder\Fields\FieldRegistry::class)) {
        \FriendsOfREDAXO\Builder\Fields\FieldRegistry::register(new EChartsOptionField());
    }

    \rex_extension::register(
        'BUILDER_ELEMENT_PATHS',
        static function (\rex_extension_point $ep): array {
            $paths = (array) $ep->getSubject();
            $paths['echarts'] = \rex_path::addon('echarts', 'elements');
            return $paths;
        },
        \rex_extension::EARLY
    );
}

if (\rex_addon::get('yform')->isAvailable()) {
    \rex_yform::addTemplatePath(__DIR__ . '/ytemplates');
}

if (\rex::isBackend() && \rex::getUser()) {
    $addon = \rex_addon::get('echarts');
    $versionedUrl = static function (string $rel) use ($addon): string {
        $path = \rex_path::addonAssets('echarts', $rel);
        $mtime = file_exists($path) ? filemtime($path) : 0;
        return $addon->getAssetsUrl($rel) . '?v=' . $mtime;
    };

    \rex_view::addCssFile($versionedUrl('echarts-addon.css'));
    \rex_view::addJsFile($versionedUrl('echarts.min.js'));
    \rex_view::addJsFile($versionedUrl('echarts-addon.js'));

    $pdfOutAvailable = \rex_addon::exists('pdfout') && \rex_addon::get('pdfout')->isAvailable() && class_exists(\FriendsOfRedaxo\PdfOut\PdfOut::class);
    $pdfExportToken = \rex_csrf_token::factory('echarts_pdf_export');
    \rex_view::setJsProperty('echarts_pdf_export', [
        'enabled' => $pdfOutAvailable,
        'url' => \rex_url::backendController(['rex-api-call' => 'echarts_pdf_export']),
        'token' => $pdfExportToken->getValue(),
    ]);

    if (\rex_be_controller::getCurrentPage() === 'echarts/demo') {
        \rex_view::addJsFile($versionedUrl('echarts-demo-geo.js'));
        \rex_view::addJsFile($versionedUrl('echarts-demo-switcher.js'));
        \rex_view::setJsProperty('echarts_demo', true);
    }
}

if (!\rex::isBackend()) {
    $addon = \rex_addon::get('echarts');
    $autoFrontendAssets = (bool) $addon->getConfig('auto_frontend_assets', true);

    if (!$autoFrontendAssets) {
        return;
    }

    \rex_extension::register('OUTPUT_FILTER', static function (\rex_extension_point $ep) use ($addon): void {
        $subject = (string) $ep->getSubject();

        if (!str_contains($subject, 'data-echarts-options=')) {
            return;
        }

        $assetUrl = static function (string $rel) use ($addon): string {
            $path = \rex_path::addonAssets('echarts', $rel);
            $mtime = file_exists($path) ? filemtime($path) : 0;
            return $addon->getAssetsUrl($rel) . '?v=' . $mtime;
        };

        $css = '<link rel="stylesheet" href="' . $assetUrl('echarts-addon.css') . '">' . "\n";
        $js = '<script defer src="' . $assetUrl('echarts.min.js') . '"></script>' . "\n"
            . '<script defer src="' . $assetUrl('echarts-addon.js') . '"></script>' . "\n";

        $subject = str_replace('</head>', $css . '</head>', $subject);
        $subject = str_replace('</body>', $js . '</body>', $subject);
        $ep->setSubject($subject);
    });
}
