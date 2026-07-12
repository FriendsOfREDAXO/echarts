<?php

return [
    'label' => 'ECharts',
    'icon' => 'fa fa-bar-chart',
    'description' => 'Bar, Line, Area, Pie, Donut und Scatter aus manuellen Daten oder YForm.',
    'version' => '1.0.0',
    'category' => 'content',
    'fields' => [
        'headline' => [
            'type' => 'text',
            'label' => 'Überschrift',
        ],
        'chart_title' => [
            'type' => 'text',
            'label' => 'Chart-Titel',
        ],
        'chart_type' => [
            'type' => 'choice',
            'label' => 'Chart-Typ',
            'choices' => [
                'bar' => 'Balken',
                'line' => 'Linie',
                'area' => 'Fläche',
                'pie' => 'Pie',
                'donut' => 'Donut',
                'scatter' => 'Scatter',
            ],
            'default' => 'bar',
        ],
        'height' => [
            'type' => 'text',
            'label' => 'Höhe',
            'default' => '380',
            'notice' => 'Zahl in Pixel oder CSS-Wert wie 28rem.',
        ],
        'source_type' => [
            'type' => 'choice',
            'label' => 'Datenquelle',
            'choices' => [
                'manual' => 'Manuelle Werte',
                'yform' => 'YForm-Tabelle',
            ],
            'default' => 'manual',
        ],
        'manual_data' => [
            'type' => 'textarea',
            'label' => 'Manuelle Daten',
            'rows' => 6,
            'notice' => 'Eine Zeile pro Wert: Name|Wert|Link(optional)|Tooltip(optional). Link: Artikel-ID oder https://... Link darf leer sein (z. B. Name|12||Mein Tooltip).',
            'default' => "Q1|120\nQ2|180\nQ3|150\nQ4|210",
        ],
        'manual_colors' => [
            'type' => 'textarea',
            'label' => 'Manuelle Farben',
            'rows' => 4,
            'notice' => 'Optional: Eine Farbe pro Zeile (z. B. #3b82f6, #10b981, rgb(239,68,68)). Bei genau 1 Hex-Farbe werden automatisch passende Nuancen erzeugt.',
            'default' => "#3b82f6\n#10b981\n#f59e0b\n#ef4444",
        ],
        'show_legend' => [
            'type' => 'checkbox',
            'label' => 'Legende anzeigen',
            'default' => 1,
        ],
        'show_tooltip' => [
            'type' => 'checkbox',
            'label' => 'Tooltip anzeigen',
            'default' => 1,
        ],
        'show_labels' => [
            'type' => 'checkbox',
            'label' => 'Werte-Labels anzeigen',
            'notice' => 'Zeigt Werte direkt am Datenpunkt/Balken an.',
        ],
        'show_grid' => [
            'type' => 'checkbox',
            'label' => 'Grid-Linien anzeigen',
            'default' => 1,
        ],
        'yform_table' => [
            'type' => 'text',
            'label' => 'YForm-Tabelle',
            'notice' => 'Beispiel: rex_sales',
        ],
        'yform_label_field' => [
            'type' => 'text',
            'label' => 'YForm Label-Feld',
            'default' => 'name',
        ],
        'yform_value_field' => [
            'type' => 'text',
            'label' => 'YForm Value-Feld',
            'default' => 'value',
        ],
        'yform_limit' => [
            'type' => 'text',
            'label' => 'YForm Limit',
            'default' => '12',
        ],
        'chart_options_json' => [
            'type' => 'echarts_option',
            'label' => 'Chart-Konfiguration (JSON-Override)',
            'notice' => 'Optional: vollständige ECharts-Optionen. Wenn gesetzt, überschreibt es Presets.',
        ],
    ],
];
