<?php

if (!defined('ABSPATH')) exit;

class mg_dn_CPT {

	public function __construct() {
		add_action('init', array($this, 'setup_post_types'));
	}
	
	public function setup_post_types() {
		$this->setup_donation_cause();
		$this->setup_donation();
	}
	
	private function setup_donation_cause() {
		$labels =  array(
			'name' 				=> __('Donation Causes', 'mg_dn'),
			'singular_name' 	=> __('Donation Cause', 'mg_dn'),
			'add_new' 			=> __( 'Add New', 'mg_dn' ),
			'add_new_item' 		=> __( 'Add New Donation Cause', 'mg_dn' ),
			'edit_item' 		=> __( 'Edit Donation Cause', 'mg_dn' ),
			'new_item' 			=> __( 'New Donation Cause', 'mg_dn' ),
			'all_items' 		=> __( 'Donation Causes', 'mg_dn' ),
			'view_item' 		=> __( 'View Donation Cause', 'mg_dn' ),
			'search_items' 		=> __( 'Search Donation Causes', 'mg_dn' ),
			'not_found' 		=> __( 'No Donation Causes found', 'mg_dn' ),
			'not_found_in_trash'=> __( 'No Donation Causes found in Trash', 'mg_dn' ),
			'menu_name' 		=> __( 'Donation Causes', 'mg_dn' )
		);
		
		$args = array(
			'labels'               => $labels,
			'description'          => '',
			'hierarchical'         => false,
			'public'               => true,
			'show_ui' => true,
			'show_in_menu' => 'mg_donations',
			//'menu_position'        => null,
			//'menu_icon'            => 'dashicons-format-quote',
			'capability_type'      => 'post',
			'capabilities'         => array(),
			'map_meta_cap'         => null,
			'supports'             => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'author'),
			'register_meta_box_cb' => null,
			'taxonomies'           => array(),
			'has_archive'          => true,
			'rewrite'              => array('slug' => 'causes'),
			'query_var'            => true,
			'can_export'           => true,
			'delete_with_user'     => null,
			'_builtin'             => false,
			'_edit_link'           => 'post.php?post=%d'
		);
		
		register_post_type('mg_donation_cause', $args);
	}
	
	private function setup_donation() {
		$labels =  array(
			'name' 				=> __('Donations', 'mg_dn'),
			'singular_name' 	=> __('Donation', 'mg_dn'),
			'add_new' 			=> __( 'Add New', 'mg_dn' ),
			'add_new_item' 		=> __( 'Add New Donation', 'mg_dn' ),
			'edit_item' 		=> __( 'Edit Donation', 'mg_dn' ),
			'new_item' 			=> __( 'New Donation', 'mg_dn' ),
			'all_items' 		=> __( 'Donations', 'mg_dn' ),
			'view_item' 		=> __( 'View Donation', 'mg_dn' ),
			'search_items' 		=> __( 'Search Donations', 'mg_dn' ),
			'not_found' 		=> __( 'No Donations found', 'mg_dn' ),
			'not_found_in_trash'=> __( 'No Donations found in Trash', 'mg_dn' ),
			'menu_name' 		=> __( 'Donations', 'mg_dn' )
		);
		
		$args = array(
			'labels'               => $labels,
			'description'          => '',
			'hierarchical'         => false,
			'public'               => true,
			'show_ui' => true,
			'show_in_menu' => 'mg_donations',
			//'menu_position'        => null,
			//'menu_icon'            => 'dashicons-format-quote',
			'capability_type'      => 'post',
			'capabilities'         => array(),
			'map_meta_cap'         => null,
			'supports'             => array(/* 'title', 'editor' */),
			'register_meta_box_cb' => null,
			'taxonomies'           => array(),
			'has_archive'          => false,
			'rewrite'              => array('slug' => 'donations'),
			'query_var'            => true,
			'can_export'           => true,
			'delete_with_user'     => null,
			'_builtin'             => false,
			'_edit_link'           => 'post.php?post=%d'
		);
		
		register_post_type('mg_donation', $args);
	}
	
	public static function delete_all_posts() {
		$posts = array();
		$post_types = array('mg_donation', 'mg_donation_cause');
		foreach ($post_types as $post_type) 
			$posts = array_merge($posts, get_posts(array(
				'post_type' => $post_type, 
				'post_status' => 'any', 
				'numberposts' => -1, 
				'fields' => 'ids'
			)));

		if (!empty($posts))
			foreach ($posts as $post_id)
				wp_delete_post($post_id, true);
	}
	
}
