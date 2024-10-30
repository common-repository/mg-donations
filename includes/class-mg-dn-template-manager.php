<?php

class mg_dn_Template_Manager {

	const TEMPLATES_DIR_NAME = 'mg-dn-templates/';

	public function __construct() {
		global $mg_dn_plugin;
		$this->templates_dir_path = $mg_dn_plugin->path['PLUGIN_DIR'] . self::TEMPLATES_DIR_NAME;
		
		//add_filter('template_include', array($this, 'find_template'));
		add_action('the_post', array($this, 'setup_postdata'));
	}
	
	/* public function find_template($template_path) {
		$files = array();
		
		if (is_single() && get_post_type() === 'mg_donation_cause') {
			$filename = 'single-donation-cause.php';
			$files[] = self::TEMPLATES_DIR_NAME . $filename;
		}
		
		if (!empty($files)) {
			$template_path = locate_template($files);
			if (!$template_path)
				$template_path = $this->templates_dir_path . $filename;
		}
		
		return $template_path;
	} */
	
	public function setup_postdata($post) {
		if ($post->post_type === 'mg_donation_cause') {
			global $mg_dn_dc;
			$mg_dn_dc = mg_dn_Data::get_donation_cause($post);
		}
		else if ($post->post_type === 'mg_donation') {
			global $mg_dn_dnt;
			$mg_dn_dnt = mg_dn_Data::get_donation($post);
		}
	}
	
	public function get_template($tmpl_name, $data) {
		$filename = $tmpl_name . '.php';
		$files[] = self::TEMPLATES_DIR_NAME . $filename;
		
		$abs_path = locate_template($files);
		
		if (!$abs_path)
			$abs_path = $this->templates_dir_path . $filename;
			
		$this->load_template($abs_path, $data);
	}
	
	private function load_template($abs_path, $data = array()) {
		//global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

		/* if ( is_array( $wp_query->query_vars ) )
			extract( $wp_query->query_vars, EXTR_SKIP ); */
	
		if (!empty($data) && is_array($data))
			extract($data);
		unset($data);

		require($abs_path);
	}

}

/*
 * Template Tags
 */
 
function mg_dn_get_template($tmpl_name, $data = array()) {
	global $mg_dn_plugin;

	$mg_dn_plugin->template_manager->get_template($tmpl_name, $data);
}

function mg_dn_donate_button($button_template = 'default', $button_args = array()) {
	global $mg_dn_plugin, $mg_dn_dc;
	
	echo $mg_dn_plugin->paypal->get_button($mg_dn_dc->title, $mg_dn_dc->id, $button_template, $button_args);
}
