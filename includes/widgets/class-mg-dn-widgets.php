<?php

if (!defined('ABSPATH')) exit;

class mg_dn_Widgets {

	public function __construct() {
		add_action('widgets_init', array($this, 'register_widgets'));
	}
	
	public function register_widgets() {
		register_widget('mg_dn_Widget_Donation_Cause');
	}

}

require_once 'class-mg-dn-widget-donation-cause.php';