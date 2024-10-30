<?php
/*
Plugin Name: mg Donations
Plugin URI: http://mgiulio.info/projects/mg-donations
Description: Donations
Version: 1.1
Author: Giulio 'mgiulio' Mainardi
Author URI: http://mgiulio.info
License: GPL2
*/

if (!defined('ABSPATH')) exit;
 
class mg_dn_Plugin {

	public $template_manager;
	public $settings;
	public $money;
	public $path = array();
	public $url = array();

	public function __construct() {
		$this->setup_paths_and_urls();
		
		if (function_exists('__autoload'))
			spl_autoload_register('__autoload');
		spl_autoload_register(array($this, 'autoload'));
	}
	
	public function autoload($class_name) {
		if (strpos($class_name, 'mg_dn_') !== 0)
			return;
			
		$class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
		
		$class_path = $this->path['INCLUDES'];
		
		if (strpos($class_name, 'mg_dn_Widget') === 0)
			$class_path .= 'widgets/';
		else if (in_array($class_name, array(
			'mg_dn_Admin_Menu',
			'mg_dn_List_Table_Donation',
			'mg_dn_List_Table_Donation_Cause',
			'mg_dn_Post_Editor_Donation_Cause',
			'mg_dn_Settings_UI',
		)))
			$class_path .= 'admin/';
			
		
		require $class_path . $class_file;
	}
	
	public function bootstrap() {
		$this->settings = new mg_dn_Settings();
		
		$this->money = new mg_dn_Money();
		
		$this->cpt = new mg_dn_CPT();
		
		$this->template_manager = new mg_dn_Template_Manager();
		
		new mg_dn_Shortcodes();
		new mg_dn_Widgets();
		
		if (is_admin())
			$this->on_admin();
		else
			$this->on_front_end();
			
		//new mg_dn_Tester();
		
		new mg_dn_Installer();
	}
	
	private function on_admin() {
		new mg_dn_Admin_Menu(new mg_dn_Settings_UI($this->settings));
		new mg_dn_List_Table_Donation_Cause();
		new mg_dn_List_Table_Donation();
		new mg_dn_Post_Editor_Donation_Cause();
		
		add_action('admin_enqueue_scripts', array($this, 'inject_scripts_styles'));
	}
	
	private function on_front_end() {
		new mg_dn_Listener();
		
		$settings = $this->settings->get();
		
		$this->paypal = new mg_dn_PayPal($settings);
		
		add_action('wp_enqueue_scripts', array($this, 'inject_scripts_styles'));
	}
	
	public function inject_scripts_styles() {
		wp_enqueue_style('mg_dn_css', $this->url['ASSETS'] . "css/style.css");
	}
	
	private function setup_paths_and_urls() {
		$this->path['PLUGIN_FILE'] = __FILE__;
		$this->path['PLUGIN_DIR'] = plugin_dir_path(__FILE__);
		$this->path['INCLUDES'] = $this->path['PLUGIN_DIR'] . 'includes/';
		
		$this->url['PLUGIN_DIR'] = plugin_dir_url(__FILE__);
		$this->url['ASSETS'] = $this->url['PLUGIN_DIR'] . 'assets/';
	}
	
	public function log($x) {	
		$out = 'mg_dn: ';
		
		/* ob_start();
		var_dump($x);
		$out .= ob_get_flush(); */
		
		//$out. = print_r($x, true);
		
		$out .= $x;
		
		error_log($out);
	}
	
}

global $mg_dn_plugin;
$mg_dn_plugin = new mg_dn_Plugin();
$mg_dn_plugin->bootstrap();
