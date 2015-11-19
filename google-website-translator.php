<?php

/*
 * Plugin Name: Google Website Translator by Prisna.net
 * Plugin URI: http://wordpress.org/extend/plugins/google-website-translator/
 * Description: This plugin brings the power of Google's automatic translation service to translate your website into 90+ languages. It also comes with a slick admin panel, so it can be installed into your client's website without losing the professional look and feel of your work.
 * Author: Prisna
 * Version: 1.1
 * Author URI: http://www.prisna.net/
 * License: GPL2+
 * Text Domain: prisna-gwt
 * Domain Path: /languages/
 */

define('PRISNA_GWT__MINIMUM_WP_VERSION', '3.3');
define('PRISNA_GWT__VERSION', '1');

define('PRISNA_GWT__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PRISNA_GWT__PLUGIN_URL', plugin_dir_url(__FILE__));

define('PRISNA_GWT__PLUGIN_CLASSES_DIR', PRISNA_GWT__PLUGIN_DIR . '/classes/');
define('PRISNA_GWT__TEMPLATES', PRISNA_GWT__PLUGIN_DIR . '/templates');

define('PRISNA_GWT__JS', PRISNA_GWT__PLUGIN_URL . 'javascript');
define('PRISNA_GWT__CSS', PRISNA_GWT__PLUGIN_URL . 'styles');
define('PRISNA_GWT__IMAGES', PRISNA_GWT__PLUGIN_URL . 'images');

require_once PRISNA_GWT__PLUGIN_CLASSES_DIR . 'common.class.php';
require_once PRISNA_GWT__PLUGIN_CLASSES_DIR . 'base.class.php';
require_once PRISNA_GWT__PLUGIN_CLASSES_DIR . 'config.class.php';

if (is_admin())
	require_once PRISNA_GWT__PLUGIN_CLASSES_DIR . 'admin.class.php';
else
	require_once PRISNA_GWT__PLUGIN_CLASSES_DIR . 'main.class.php';

?>