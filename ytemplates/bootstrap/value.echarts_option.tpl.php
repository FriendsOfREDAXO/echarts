<?php

/** @var rex_yform_value_echarts_option $this */

$wrapperClass = 'form-group';
if ($this->getWarningClass() !== '') {
    $wrapperClass .= ' ' . $this->getWarningClass();
}

$warningText = '';
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $warningText = (string) $this->params['warning_messages'][$this->getId()];
}

$previewHtml = '';
if (isset($preview) && is_string($preview)) {
    $previewHtml = $preview;
}
?>
<div class="<?= rex_escape($wrapperClass) ?>" id="<?= rex_escape($this->getHTMLId()) ?>">
    <label class="control-label" for="<?= rex_escape($this->getFieldId()) ?>"><?= rex_i18n::translate($this->getElement('label')) ?></label>
    <textarea
        class="form-control"
        rows="10"
        id="<?= rex_escape($this->getFieldId()) ?>"
        name="<?= rex_escape($this->getFieldName()) ?>"
    ><?= rex_escape((string) ($value ?? '')) ?></textarea>

    <?php if ($warningText !== ''): ?>
        <p class="help-block small"><span class="text-warning"><?= rex_escape(rex_i18n::translate($warningText)) ?></span></p>
    <?php endif; ?>

    <?php if ($this->getElement('notice') !== ''): ?>
        <p class="help-block"><?= rex_i18n::translate($this->getElement('notice'), false) ?></p>
    <?php endif; ?>

    <?php if ($previewHtml !== ''): ?>
        <div class="rex-echarts-yform-preview">
            <?= $previewHtml ?>
        </div>
    <?php endif; ?>
</div>
