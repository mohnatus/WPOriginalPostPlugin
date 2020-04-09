<?php

/**
 * Выводит настройки плагина на странице Чтение (options-reading.php)
 */
add_action('admin_init', 'originalPluginSettings');
function originalPluginSettings() {
  $pageName = 'reading';
  $sectionName = 'original_plugin_settings';

  /**
   * Добавление секции original_plugin_settings на страницу Чтение
   */
  add_settings_section(
    $sectionName,
    __('Original post', 'original10n'),
    '',
    $pageName
  );

  /**
   * Добавление полей настроек в секцию original_plugin_settings
   * (field_name, field_label, field_callback, page_name, section_name)
   */
  add_settings_field(
    'original_position_on_top_field',
    __('Set original link on top of the content', 'original10n'),
    'originalPluginPositionOnTopCallback',
    $pageName,
    $sectionName
  );
  add_settings_field(
    'original_template_string_field',
    __('Template', 'original10n'),
    'originalPluginTemplateStringCallback',
    $pageName,
    $sectionName
  );
  add_settings_field(
    'original_link_rel_field',
    __('Rel attribute', 'original10n'),
    'originalPluginLinkRelCallback',
    $pageName,
    $sectionName
  );
  add_settings_field(
    'original_link_blank_field',
    __('Open links in a new tab', 'original10n'),
    'originalPluginLinkBlankCallback',
    $pageName,
    $sectionName
  );
  add_settings_field(
    'original_css_prefix_field',
    __('CSS prefix', 'original10n'),
    'originalPluginCssPrefixCallback',
    $pageName,
    $sectionName
  );
  add_settings_field(
    'original_custom_css_field',
    __('Custom CSS', 'original10n'),
    'originalPluginCustomCssCallback',
    $pageName,
    $sectionName
  );

  /**
   * Регистрация настроек плагина для сохранения
   * (page_name, option_name, option_callback)
   */
  register_setting($pageName, 'original_position_on_top', 'intval');
  register_setting($pageName, 'original_custom_css', 'strip_tags');
  register_setting($pageName, 'original_options', 'originalPluginSanitizeCallback');

  /**
   * Установка настроек по умолчанию
   */
  if (!get_option('original_options')) {
    update_option('original_options', [
      'template' => '%LINK%{, by %AUTHOR%}',
      'prefix' => 'original',
      'rel' => 'noopener noreferrer',
      'blank' => 0,
    ]);
  }
}

/**
 * Очистка настроек перед сохранением
 */
function originalPluginSanitizeCallback($options) {
  foreach($options as $name => &$val) {
    if ($name == 'template') {
      if (!$val) $val = '%LINK%{, by %AUTHOR%}';
    }
    if ($name == 'prefix') {
      if (!$val) $val = 'original';
    }
    if ($name == 'rel') {
      $val = strip_tags($val);
    }
    if ($name == 'blank') {
      $val = intval($val);
    }
  }
  return $options;
}

/**
 * Поле Позиция блока
 */
function originalPluginPositionOnTopCallback() {
  $optionName = 'original_position_on_top';
  $value = get_option($optionName);
  ?>
    <fieldset>
      <input type="checkbox" name="<?= $optionName ?>" value="1" <?php checked(1, $value) ?> >
    </fieldset>
  <?php
}

/**
 * Поле Шаблонная строка
 */
function originalPluginTemplateStringCallback() {
  $optionName = 'original_options';
  $propertyName = 'template';
  $fullName = "{$optionName}[{$propertyName}]";

  $option = get_option($optionName);
  $value = isset($option[$propertyName]) ? $option[$propertyName] : '%LINK%{, by %AUTHOR%}';
  ?>
    <fieldset>
      <input style="width: 100%" type="text" name="<?= $fullName ?>" value="<?= htmlentities($value) ?>">
      <p class="description">
        <code>%LINK%</code> - <?= __('Original link', 'original10n') ?>
        <br>
        <code>%AUTHOR%</code> - <?= __('Original post author', 'original10n') ?>
        <br>
        <code>{   }</code> - <?= __('Hide if no author', 'original10n') ?>
        <hr>
        <code>%ORIGINAL_LINK%</code> - <?= __('Only url', 'original10n') ?>
        <br>
        <code>%ORIGINAL_TITLE%</code> - <?= __('Only title', 'original10n') ?>
        <br>
        <code>%AUTHOR_LINK%</code> - <?= __('Only author link', 'original10n') ?>
        <br>
        <code>%AUTHOR_NAME%</code> - <?= __('Only author name', 'original10n') ?>
      </p>
    </fieldset>
  <?php
}

/**
 * Поле Значение атрибута rel у ссылок
 */
function originalPluginLinkRelCallback() {
  $optionName = 'original_options';
  $propertyName = 'rel';
  $fullName = "{$optionName}[{$propertyName}]";

  $option = get_option($optionName);
  $value = isset($option[$propertyName]) ? $option[$propertyName] : '';
  ?>
    <fieldset>
      <input style="width: 100%" type="text" name="<?= $fullName ?>" value="<?= $value ?>">
    </fieldset>
  <?php
}

/**
 * Поле Открывать ссылки в новой вкладке
 */
function originalPluginLinkBlankCallback() {
  $optionName = 'original_options';
  $propertyName = 'blank';
  $fullName = "{$optionName}[{$propertyName}]";

  $option = get_option($optionName);
  $value = isset($option[$propertyName]) ? $option[$propertyName] : 0;
  ?>
    <fieldset>
      <input type="checkbox" name="<?= $optionName ?>" value="1" <?php checked(1, $value) ?> >
    </fieldset>
  <?php
}

/**
 * Поле CSS-префикс
 */
function originalPluginCssPrefixCallback() {
  $optionName = 'original_options';
  $propertyName = 'prefix';
  $fullName = "{$optionName}[{$propertyName}]";

  $option = get_option($optionName);
  $value = isset($option[$propertyName]) ? $option[$propertyName] : 'original';
  ?>
    <fieldset>
      <input type="text" name="<?= $fullName ?>" value="<?= $value ?>">
    </fieldset>
  <?php
}

/**
 * Поле Пользовательский CSS-код
 */
function originalPluginCustomCssCallback() {
  $optionName = 'original_custom_css';
  $value = get_option($optionName);
  ?>
    <fieldset>
      <textarea name="<?= $optionName ?>" style="width: 100%" rows="10"><?= $value ?></textarea>
    </fieldset>
  <?php
}
