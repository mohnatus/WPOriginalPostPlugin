<?php

add_action('add_meta_boxes', function() {
  add_meta_box('originalpost', __('Original post', 'original10n'), 'original_meta_box_view', array('post'), 'normal', 'default');
}, 1);
function original_meta_box_view($post) {
  $postId = $post->ID;
  $originalLink = get_post_meta($postId, 'original_link', 1);
  $originalTitle = get_post_meta($postId, 'original_title', 1);
  $originalAuthor = get_post_meta($postId, 'original_author', 1);
  $originalAuthorlink = get_post_meta($postId, 'original_author_link', 1);
  ?>

    <div class="form-group" style="margin: 20px 0">
      <label for="original-link"><?= __('Link', 'original10n') ?></label>
      <br>
      <input type="text" id="original-link" name="original[link]" value="<?= $originalLink ?>" style="width: 100%">
    </div>

    <div class="form-group" style="margin: 20px 0">
      <label for="original-title"><?= __('Title', 'original10n') ?></label>
      <br>
      <input type="text" id="original-title" name="original[title]" value="<?= $originalTitle ?>" style="width: 100%">
    </div>

    <div class="form-group" style="margin: 20px 0">
      <label for="original-author"><?= __('Author name', 'original10n') ?></label>
      <br>
      <input type="text" id="original-author" name="original[author]" value="<?= $originalAuthor ?>" style="width: 100%">
    </div>

    <div class="form-group" style="margin: 20px 0">
      <label for="original-authorlink"><?= __('Author link', 'original10n') ?></label>
      <br>
      <input type="text" id="original-authorlink" name="original[author_link]" value="<?= $originalAuthorlink ?>" style="width: 100%">
    </div>

    <input type="hidden" name="original_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
  <?php
}

add_action('save_post', 'original_meta_update', 0);
function original_meta_update($postId) {
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
      delete_post_meta( $postId, $metaKey );
      continue;
    }

    update_post_meta( $postId, $metaKey, $value );
  }

  return $postId;
}
