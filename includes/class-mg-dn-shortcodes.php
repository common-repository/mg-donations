<?php

if (!defined('ABSPATH')) exit;

class mg_dn_Shortcodes {

	public function __construct() {
		add_shortcode('mg_dn_dc', array($this, 'render_donation_cause'));
	}
	
	public function render_donation_cause($atts) {
		$args = shortcode_atts(array(
			'id' => '0',
			'show_title' => true,
			'show_permalink' => true,
			'show_featured_image' => true,
			'show_excerpt' => true,
			'button_template' => 'default',
			'button_text' => 'Donate'
		), $atts);
		
		$id = absint($args['id']);
		
		$q = new WP_Query(array(
			'p' => $id,
			'post_type' => 'mg_donation_cause'
		));
		
		if (!$q->have_posts())
			return '';
		
		$q->the_post();
		
		ob_start();
		unset($args['id']);
		mg_dn_get_template('shortcodes/donation-cause', $args);
		$markup = ob_get_clean();
		wp_reset_postdata();
		
		return apply_filters('mg_dn_shortcode_dc', $markup);
	}
	
}

