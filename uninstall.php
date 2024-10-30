<?php

class mg_dn_Uninstall {

	public function __construct() {
		require_once 'includes/class-mg-dn-cpt.php';
		mg_dn_CPT::delete_all_posts();
		
		// Refresh permalinks
		flush_rewrite_rules();
		
		require_once 'includes/class-mg-dn-settings.php';
		mg_dn_Settings::delete_settings();
	}
	
}

new mg_dn_Uninstall();
