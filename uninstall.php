<?php

if (!defined('WP_UNINSTALL_PLUGIN') || !WP_UNINSTALL_PLUGIN || dirname(WP_UNINSTALL_PLUGIN) != dirname(plugin_basename( __FILE__ ))) {
	status_header(404);
	exit;
}

class PrisnaGWTUninstall { 
 
 	public static function run() {
		
		require_once dirname(__FILE__) . '/classes/config.class.php';
		
		$name = PrisnaGWTConfig::getDbSettingsName();
		
		if (get_option($name))
			delete_option($name);

	}
	
}

PrisnaGWTUninstall::run();