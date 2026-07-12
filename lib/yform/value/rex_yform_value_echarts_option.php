<?php

use FriendsOfREDAXO\ECharts\ChartRenderer;

class rex_yform_value_echarts_option extends rex_yform_value_abstract
{
    public function enterObject(): void
    {
        $value = trim((string) $this->getValue());
        $required = (bool) $this->getElement('required');

        if ($this->params['send'] && $required && $value === '') {
            $this->params['warning'][$this->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$this->getId()] = rex_i18n::msg('yform_values_required_msg');
        }

        $this->setValue($value);
        $this->params['value_pool']['email'][$this->getName()] = $value;
        if ($this->saveInDb()) {
            $this->params['value_pool']['sql'][$this->getName()] = $value;
        }

        if ($this->needsOutput() && $this->isViewable()) {
            $preview = '';
            if ($value !== '') {
                try {
                    $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($decoded)) {
                        $preview = ChartRenderer::render($decoded, (string) $this->getElement('height'));
                    }
                } catch (Throwable) {
                    $preview = '';
                }
            }

            $this->params['form_output'][$this->getId()] = $this->parse('value.echarts_option.tpl.php', [
                'value' => $value,
                'preview' => $preview,
            ]);
        }
    }

    public function getDescription(): string
    {
        return 'echarts_option|name|label|[height]|[required]|[notice]';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'echarts_option',
            'values' => [
                'name' => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'height' => [
                    'type' => 'text',
                    'label' => 'Chart-Höhe',
                    'default' => '360',
                ],
                'required' => [
                    'type' => 'boolean',
                    'label' => rex_i18n::msg('echarts_yform_required'),
                ],
                'notice' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => rex_i18n::msg('echarts_yform_description'),
            'db_type' => ['text'],
            'famous' => false,
        ];
    }
}
