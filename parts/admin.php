<?php

/**
 * Добавляет блок произвольных полей на страницу поста
 */
add_action('add_meta_boxes', function() {
  add_meta_box('original_post_meta_box', __('Original post', 'original10n'), 'originalPluginMetaBoxView', array('post'), 'normal', 'default');
}, 1);
function originalPluginMetaBoxView($post) {
  $postId = $post->ID;
  ?>
    <fieldset>
      <?= _originalPluginCreateField($postId, 'link', __('Link', 'original10n')); ?>
      <?= _originalPluginCreateField($postId, 'title', __('Title', 'original10n')); ?>
      <?= _originalPluginCreateField($postId, 'author', __('Author name', 'original10n')); ?>
      <?= _originalPluginCreateField($postId, 'author_link', __('Author link', 'original10n')); ?>

      <input type="hidden" name="original_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
    </fieldset>

  <?php
}
/**
 * Генерирует HTML-код поля
 */
function _originalPluginCreateField($postId, $name, $label) {
  $value = get_post_meta($postId, 'original_'.$name, 1);
  $id = "original_{$name}_field";
  ?>
    <div class="form-group">
      <label for="<?= $id ?>"><?= $label ?></label>
      <input type="text" id="<?= $id ?>" name="original[<?= $name ?>]" value="<?= $value ?>">
    </div>
  <?php
}

/**
 * Добавляет стили для метабокса на страницу редактирования поста
 */
add_action('admin_enqueue_scripts', function($hook_suffix) {
  if ($hook_suffix == 'post.php' || $hook_suffix == 'post-new.php') {
    wp_enqueue_style('original-plugin', plugins_url('../css/admin.css', __FILE__));
  }
});

/**
 * Сохраняет значения произвольных полей
 */
add_action('save_post', 'originalPluginMetaUpdate', 0);
function originalPluginMetaUpdate($postId) {
  $metaBoxName = 'original';
  if (!isset($_POST[$metaBoxName])) {
    return false;
  }

  $metaBox = $_POST[$metaBoxName];
  if (
    empty($metaBox)
    || ! wp_verify_nonce($_POST[$metaBoxName.'_nonce'], __FILE__)
    || wp_is_post_autosave($postId )
    || wp_is_post_revision($postId )
  )
  return false;

  $metaBox = array_map(
    'sanitize_text_field',
    $metaBox
  );

  foreach($metaBox as $key=>$value) {
    $metaKey = $metaBoxName.'_'.$key;

    if(empty($value)){
      delete_post_meta($postId, $metaKey);
      continue;
    }

    update_post_meta($postId, $metaKey, $value);
  }

  return $postId;
}
