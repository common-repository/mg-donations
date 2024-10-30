<?php

class mg_dn_Listener {

	public function __construct() {
		add_filter('query_vars', array($this, 'register_query_var'));
		//add_action('init', array($this, 'add_endpoint'), 0);
		add_action('parse_request', array($this, 'handle_api_requests'), 0);
		//add_action('init', array($this, 'check_for_notify'));
	}
	
	public function register_query_var($vars) {
		$vars[] = 'mg_dn';
		return $vars;
	}

	/* public function add_endpoint() {
		add_rewrite_endpoint('mg_dn', EP_ALL);
	} */

	public function handle_api_requests($wp) {
		global $mg_dn_plugin;
		
		if (empty($wp->query_vars['mg_dn']))
			return;
			
		$action = $wp->query_vars['mg_dn'];
		//$action = strtolower(esc_attr(wp->query_vars['mg_dn']); // sanitize_*?
		
		$mg_dn_plugin->log("Received action request: $action");

		ob_start();
			do_action('mg_dn_' . $action);
		ob_end_clean();
		
		die('1');
	}
	
	/* private function check_for_notify() {
		if (!isset($_GET['mg_dn']))
			return;
			
		$action = $_GET['mg_dn'];
		
		do_action("mg_dn_action_$action");
		
		exit;
	} */

}
		
	
	