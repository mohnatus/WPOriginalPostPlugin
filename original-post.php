<?php
/*
* Plugin Name: Original post
* Description: Creates original post block
* Author: FurryCat
* Author URI: http://portfolio.furrycat.ru/
* Version: 1.0
* Text Domain: original10n
* Domain Path: /lang
*/

add_action('plugins_loaded', function() {
	load_plugin_textdomain('original10n', false, dirname( plugin_basename(__FILE__) ) . '/lang');
});

require_once 'parts/settings.php';
require_once 'parts/admin.php';
require_once 'parts/front.php';
