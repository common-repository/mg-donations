<?php

class mg_dn_Installer {

	private $plugin;

	public function __construct() {
		global $mg_dn_plugin;
		$this->plugin = $mg_dn_plugin;
		
		register_activation_hook($this->plugin->path['PLUGIN_FILE'], array($this, 'activation'));
	}
	
	public function activation() {
		$this->plugin->settings->setup();
		
		// Refresh permalinks
		$this->plugin->cpt->setup_post_types();
		flush_rewrite_rules();
	}

}

