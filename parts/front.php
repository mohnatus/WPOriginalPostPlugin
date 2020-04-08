<?php

add_action('wp_head', function() {
  if (!is_singular()) return;
  global $post;
  if (!get_post_meta($post->ID, 'original_link')) {
    return;
  }
  $css = get_option('original_custom_css');
  echo "<style id='origina-custom'>$css</style>";
});

add_filter('the_content', 'original_view');
function original_view($content) {
  global $post;
  $postId = $post->ID;
  $onTop = get_option('original_position_on_top');

  $block = original_create_block($postId);

  if ($onTop) return $block.$content;
  return $content.$block;
}

function original_create_block($postId) {
  $link = get_post_meta($postId, 'original_link', 1);
  if (!$link) return '';

  $options = get_option('original_options');
  $prefix = $options['prefix'];

  $title = get_post_meta($postId, 'original_title', 1);
  if (!$title) $title = $link;

  $author = get_post_meta($postId, 'original_author', 1);
  $authorLink = get_post_meta($postId, 'original_author_link', 1);

  $authorHTML = '';
  if ($author) {
    $authorHTML = $authorLink ?
      "<a href='$authorLink' rel='noopener noreferrer nofollow' target='_blank' class='{$prefix}__author'>$author</a>" :
      "<span class='{$prefix}__author'>$author</span>";
  }

  $linkHTML = "<a class='{$prefix}__link' href='$link' rel='noopener noreferrer nofollow' target='_blank'>$title</a>";

  $template = $options['template'];

  if (!$authorHTML) {
    $template = preg_replace('~{.+?}~', '', $template, 1);
  } else {
    $template = str_replace('{', '', $template);
    $template = str_replace('}', '', $template);
  }

  $html = $template;

  $html = str_replace('%LINK%', $linkHTML, $html);
  $html = str_replace('%AUTHOR%', $authorHTML, $html);

  return "<div class='{$prefix}'>
    <meta itemprop='isBasedOn' content='{$link}'>
  $html</div>";
}
