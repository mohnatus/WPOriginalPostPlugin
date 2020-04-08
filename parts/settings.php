<?php

add_action('admin_init', 'original_settings');
function original_settings() {
  add_settings_section(
    'original_settings_section',
    __('Original post', 'original10n'),
    '',
    'reading'
  );

  add_settings_field(
    'original_position_on_top',
    __('Set original link on top of the content', 'original10n'),
    'original_position_view',
    'reading',
    'original_settings_section'
  );
  add_settings_field(
    'original_template',
    __('Template', 'original10n'),
    'original_template_view',
    'reading',
    'original_settings_section'
  );
  add_settings_field(
    'original_prefix',
    __('CSS prefix', 'original10n'),
    'original_prefix_view',
    'reading',
    'original_settings_section'
  );
  add_settings_field(
    'original_custom_css',
    __('Custom CSS', 'original10n'),
    'original_custom_css_view',
    'reading',
    'original_settings_section'
  );

  register_setting('reading', 'original_position_on_top', 'intval');
  register_setting('reading', 'original_custom_css');
  register_setting('reading', 'original_options', 'original_sanitize');

  if (!get_option('original_options')) {
    update_option('original_options', [
      'template' => '%LINK%{, by %AUTHOR%}',
      'prefix' => 'original'
    ]);
  }

}

function original_sanitize($options) {
  foreach($options as $name => &$val) {
    if ($name == 'template') {
      if (!$val) $val = '%LINK%{, by %AUTHOR%}';
    }
    if ($name == 'prefix') {
      if (!$val) $val = 'original';
    }
  }
  return $options;
}

function original_position_view() {
    $value = get_option('original_position_on_top');
  ?>
    <input type="checkbox" name="original_position_on_top" value="1" <?php checked(1, $value) ?> >
  <?php
}

function original_template_view() {
    $option = get_option('original_options');
    $value = $option['template'];
  ?>
    <fieldset>
      <input style="width: 100%" type="text" name="original_options[template]" value="<?= $value ?>">
      <p class="description">
        <code>%LINK%</code> - <?= __('Original link', 'original10n') ?>
        <br>
        <code>%AUTHOR%</code> - <?= __('Original post author', 'original10n') ?>
        <br>
        <code>{   }</code> - <?= __('Hide if no author', 'original10n') ?>
      </p>
    </fieldset>

  <?php
}

function original_prefix_view() {
  $option = get_option('original_options');
  $value = isset($option['prefix']) ? $option['prefix'] : 'original';
  ?>
    <input type="text" name="original_options[prefix]" value="<?= $value ?>">
  <?php
}

function original_custom_css_view() {
  $value = get_option('original_custom_css');
  ?>
    <textarea name="original_custom_css" style="width: 100%" rows="10"><?= $value ?></textarea>
  <?php
}
