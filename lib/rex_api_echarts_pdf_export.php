<?php

declare(strict_types=1);

use FriendsOfRedaxo\PdfOut\PdfOut;

class rex_api_echarts_pdf_export extends rex_api_function
{
    protected $published = true;

    public function execute(): void
    {
        rex_response::cleanOutputBuffers();

        if (!rex::isBackend() || null === rex::getUser()) {
            $this->sendJsonError('not_authorized', 403);
        }

        $token = rex_request('_csrf_token', 'string', '');
        $csrf = rex_csrf_token::factory('echarts_pdf_export');
        if ('' === $token || !$csrf->isValid($token)) {
            $this->sendJsonError('invalid_csrf', 403);
        }

        if (!rex_addon::exists('pdfout') || !rex_addon::get('pdfout')->isAvailable() || !class_exists(PdfOut::class)) {
            $this->sendJsonError('pdfout_not_available', 400);
        }

        $imageData = rex_post('image_data', 'string', '');
        if ('' === $imageData || !$this->isAllowedDataUrl($imageData)) {
            $this->sendJsonError('invalid_image_data', 400);
        }

        $requestedFileName = trim(rex_post('file_name', 'string', 'chart'));
        $fileName = rex_string::normalize($requestedFileName);
        if ('' === $fileName) {
            $fileName = 'chart';
        }

        $pageTitle = trim(rex_post('page_title', 'string', ''));
        $sourceUrl = trim(rex_post('source_url', 'string', ''));
        $chartTitle = trim(rex_post('chart_title', 'string', ''));
        if ('' === $chartTitle) {
            $chartTitle = $requestedFileName !== '' ? $requestedFileName : 'Chart Export';
        }

        $downloadedAt = date('d.m.Y H:i:s');

        $pageTitleHtml = '';
        if ($pageTitle !== '') {
            $pageTitleHtml = '<tr><th>Seitentitel</th><td>' . rex_escape($pageTitle) . '</td></tr>';
        }

        $sourceUrlHtml = '';
        if ($sourceUrl !== '') {
            $sourceUrlHtml = '<tr><th>Quelle</th><td class="url">' . rex_escape($sourceUrl) . '</td></tr>';
        }

        $html = '<!doctype html><html><head><meta charset="utf-8"><style>'
            . '@page{margin:14mm 16mm 14mm 16mm;}'
            . 'html,body{margin:0;padding:0;background:#ffffff;color:#1f2937;font-family:DejaVu Sans,Arial,sans-serif;font-size:11px;}'
            . '.header{margin-bottom:10mm;padding-bottom:4mm;border-bottom:1px solid #cbd5e1;}'
            . '.title{font-size:21px;font-weight:700;line-height:1.2;color:#0f172a;margin:0 0 4mm 0;}'
            . '.meta{width:100%;border-collapse:collapse;table-layout:fixed;}'
            . '.meta th,.meta td{padding:1.4mm 0;vertical-align:top;border-bottom:1px solid #e5e7eb;}'
            . '.meta th{width:28mm;color:#475569;font-weight:700;text-align:left;}'
            . '.meta td{color:#111827;word-break:break-word;}'
            . '.meta td.url{font-size:9px;color:#334155;}'
            . '.chart-card{border:1px solid #dbe3ea;border-radius:3mm;padding:5mm;background:#ffffff;}'
            . '.chart-title{font-size:14px;font-weight:700;color:#0f172a;margin:0 0 3.5mm 0;}'
            . '.chart-hint{font-size:9px;color:#64748b;margin:0 0 4mm 0;}'
            . '.chart-image-wrap{border:1px solid #e2e8f0;border-radius:2mm;padding:4mm;background:#ffffff;}'
            . 'img{max-width:100%;height:auto;display:block;margin:0 auto;}'
            . '</style></head><body>'
            . '<div class="header">'
            . '<h1 class="title">ECharts PDF-Export</h1>'
            . '<table class="meta">'
            . '<tr><th>Dateiname</th><td>' . rex_escape($fileName) . '.pdf</td></tr>'
            . '<tr><th>Exportiert am</th><td>' . rex_escape($downloadedAt) . '</td></tr>'
            . $pageTitleHtml
            . $sourceUrlHtml
            . '</table>'
            . '</div>'
            . '<div class="chart-card">'
            . '<h2 class="chart-title">' . rex_escape($chartTitle) . '</h2>'
            . '<p class="chart-hint">Direkt aus ECharts exportierte Grafik</p>'
            . '<div class="chart-image-wrap">'
            . '<img src="' . rex_escape($imageData) . '" alt="chart">'
            . '</div>'
            . '</div>'
            . '</body></html>';

        try {
            $pdf = new PdfOut();
            $pdf->setName($fileName)
                ->setAttachment(true)
                ->setPaperSize('A4', 'landscape')
                ->setDpi(150)
                ->setFont('DejaVu Sans')
                ->setHtml($html)
                ->run();
            exit;
        } catch (Throwable $exception) {
            $this->sendJsonError('pdf_generation_failed', 500, $exception->getMessage());
        }
    }

    private function isAllowedDataUrl(string $value): bool
    {
        if (!str_starts_with($value, 'data:image/')) {
            return false;
        }

        // Accept common ECharts variants, e.g. data:image/png;charset=utf-8;base64,...
        if (preg_match('/^data:image\/(png|svg\+xml)(;[^,]*)?,/i', $value) === 1) {
            return true;
        }

        return false;
    }

    private function sendJsonError(string $code, int $statusCode, string $message = ''): void
    {
        rex_response::setStatus($statusCode);

        $payload = ['ok' => false, 'error' => $code];
        if ('' !== $message) {
            $payload['message'] = $message;
        }

        rex_response::sendJson($payload);
        exit;
    }
}
