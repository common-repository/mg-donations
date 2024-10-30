<?php

class mg_dn_Admin_Menu {

	private $settings_ui;

	public function __construct($settings_ui) {
		$this->settings_ui = $settings_ui;
		
		add_action('admin_menu', array($this, 'setup'));
	}
	
	public function setup() {
		global $mg_dn_plugin;
		
		add_menu_page(
			'mg Donations',
			'mg Donations',
			'manage_options',
			'mg_donations', // slug
			'',//$mg_dn_plugin->settings->get_page()
			//'dashicons-groups'
			'dashicons-heart'
			//'dashicons-universal-access'
			//'dashicons-email-alt'
			,26
		);
		
		add_submenu_page(
			'mg_donations', //'edit.php?post_type=mg_donation',
			'Settings',
			'Settings',
			'manage_options',
			'mg_dn_settings', // slug
			$this->settings_ui->get_page()
		);
		
		add_submenu_page(
			'mg_donations', //'edit.php?post_type=mg_donation',
			'Site Info',
			'Site Info',
			'manage_options',
			'mg_dn_site_info', // slug
			array($this, 'page_placeholder')
		);
		
		add_submenu_page(
			'mg_donations', //'edit.php?post_type=mg_donation',
			'About',
			'About',
			'manage_options',
			'mg_dn_about', // slug
			array($this, 'page_placeholder')
		);
	}
	
	public function page_placeholder() {
		?>
		<div class="wrap">
			<h1>Coming Soon</h1>
		</div>
		<?php
	}

}
