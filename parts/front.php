<?php

/**
 * Выводит кастомные стили для блока на странице поста, если есть метаданные
 */
add_action('wp_head', function() {
  if (!is_single()) return;

  global $post;
  if (!get_post_meta($post->ID, 'original_link')) {
    return;
  }

  $css = get_option('original_custom_css');
  echo "<style id='original-custom'>$css</style>";
});

/**
 * Добавляет блок к контенту
 */
add_filter('the_content', function ($content) {
  global $post;
  $postId = $post->ID;

  /** Позиция блока в контенте */
  $onTop = get_option('original_position_on_top');

  $block = originalPluginCreateBlockView($postId);

  if ($onTop) return $block.$content;
  return $content.$block;
});


/**
 * Генерирует блок со ссылкой на оригинальный пост
 */
function originalPluginCreateBlockView($postId) {
  $link = get_post_meta($postId, 'original_link', 1);
  if (!$link) return '';

  // мета-данные
  $title = get_post_meta($postId, 'original_title', 1);
  $author = get_post_meta($postId, 'original_author', 1);
  $authorLink = get_post_meta($postId, 'original_author_link', 1);
  $template = get_post_meta($postId, 'original_template', 1);

  if (!$title) $title = $link;

  // опции
  $options = get_option('original_options');
  $prefix = isset($options['prefix']) ? $options['prefix'] : 'original'; // css-prefix

  if (!$template) {
    $template = isset($options['template']) ? $options['template'] : '%LINK%{, by %AUTHOR%}'; // шаблонная строка
  }

  $rel = isset($options['rel']) ? $options['rel'] : ''; // аттрибут rel
  $blank = isset($options['blank']) ? $options['blank'] : 0; // открывать в новой вкладке

  $linkAttrs = ($rel ? " rel='$rel'" : "").($blank ? " target='_blank'" : "");

  /**
   * Подготавливает шаблонную строку
   */
  if (!$author) {
    $template = preg_replace('~{.+?}~', '', $template, 1);
  } else {
    $template = str_replace('{', '', $template);
    $template = str_replace('}', '', $template);
  }

  /**
   * Фрагменты для замены в шаблоне
   */
  $LINK_TMP = "<a class='{$prefix}__link' href='$link' $linkAttrs>$title</a>"; // полная ссылка на оригинал
  $AUTHOR_TMP = ""; // полная ссылка на автора
  if ($author) {
    $AUTHOR_TMP = $authorLink ?
      "<a href='$authorLink' $linkAttrs class='{$prefix}__author'>$author</a>" :
      "<span class='{$prefix}__author'>$author</span>";
  }
  $ONLY_LINK = $link;
  $ONLY_TITLE = $title;
  $AUTHOR_LINK = $authorLink;
  $AUTHOR_NAME = $author;

  /**
   * Замена плейсхолдеров
   */
  $html = $template;
  $html = str_replace('%LINK%', $LINK_TMP, $html);
  $html = str_replace('%AUTHOR%', $AUTHOR_TMP, $html);
  $html = str_replace('%ORIGINAL_LINK%', $ONLY_LINK, $html);
  $html = str_replace('%ORIGINAL_TITLE%', $ONLY_TITLE, $html);
  $html = str_replace('%AUTHOR_LINK%', $AUTHOR_LINK, $html);
  $html = str_replace('%AUTHOR_NAME%', $AUTHOR_NAME, $html);

  return "<div class='{$prefix}'>
    <meta itemprop='isBasedOn' content='{$link}'>
    $html
  </div>";
}
