<?php

if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

delete_option('original_custom_css');
delete_option('original_options');
delete_option('original_on_top_position');
