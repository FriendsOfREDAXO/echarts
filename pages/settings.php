<?php

$addon = rex_addon::get('echarts');
$token = rex_csrf_token::factory('echarts_settings');
$message = '';

if (rex_post('echarts_settings_save', 'int', 0) === 1) {
    if ($token->isValid()) {
        $autoAssets = rex_post('auto_frontend_assets', 'int', 0) === 1;
        $addon->setConfig('auto_frontend_assets', $autoAssets);
        $message = rex_view::success(rex_i18n::msg('echarts_settings_saved'));
    } else {
        $message = rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    }
}

$autoAssetsEnabled = (bool) $addon->getConfig('auto_frontend_assets', true);

if ($message !== '') {
    echo $message;
}

echo '<div class="panel panel-default">';
echo '<div class="panel-heading"><h3 class="panel-title">' . rex_i18n::msg('echarts_settings_title') . '</h3></div>';
echo '<div class="panel-body">';
echo '<p>' . rex_i18n::msg('echarts_settings_intro') . '</p>';
echo '<form method="post">';
echo $token->getHiddenField();
echo '<input type="hidden" name="echarts_settings_save" value="1">';
echo '<div class="checkbox">';
echo '<label>';
echo '<input type="checkbox" name="auto_frontend_assets" value="1"' . ($autoAssetsEnabled ? ' checked' : '') . '> ';
echo rex_i18n::msg('echarts_settings_auto_assets');
echo '</label>';
echo '</div>';
echo '<p class="help-block">' . rex_i18n::msg('echarts_settings_auto_assets_notice') . '</p>';
echo '<button type="submit" class="btn btn-primary">' . rex_i18n::msg('save') . '</button>';
echo '</form>';
echo '</div>';
echo '</div>';
