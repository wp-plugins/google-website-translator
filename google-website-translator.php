<?php

/*
 * Plugin Name: Google Website Translator by Prisna.net
 * Plugin URI: http://wordpress.org/extend/plugins/google-website-translator/
 * Description: Bring the power of the WordPress.com cloud to your self-hosted WordPress. Jetpack enables you to connect your blog to a WordPress.com account to use the powerful features normally only available to WordPress.com users.
 * Author: Prisna
 * Version: 1.0
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