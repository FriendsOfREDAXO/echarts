<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\ECharts\Fields;

use FriendsOfREDAXO\Builder\Fields\FieldAbstract;
use rex_escape;
use rex_i18n;

final class EChartsOptionField extends FieldAbstract
{
    public static function getType(): string
    {
        return 'echarts_option';
    }

    public function render(string $fieldName, array $fieldConfig, mixed $value, array $sliceData = []): void
    {
        if (!$this->hasPermission($fieldConfig)) {
            return;
        }

        $label = (string) ($fieldConfig['label'] ?? rex_i18n::msg('echarts_field_option_label'));
        $notice = isset($fieldConfig['notice'])
            ? (string) $fieldConfig['notice']
            : rex_i18n::msg('echarts_field_option_notice');

        $this->openFormGroup();
        $this->renderLabel($label);

        echo '<textarea class="form-control" rows="10" name="' . rex_escape($fieldName) . '" data-echarts-json="1">'
            . rex_escape((string) $value)
            . '</textarea>';

        $this->closeFormGroup($notice);
    }
}
