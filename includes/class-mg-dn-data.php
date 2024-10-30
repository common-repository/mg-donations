<?php

if (!defined('ABSPATH')) exit;

class mg_dn_Data {

	private function __construct() {
	}
	
	public static function get_donation_cause($post) {
		$post = get_post($post);
		if (empty($post))
			return null;
			
		$dc = new StdClass();
		
		$dc->id = $post->ID;
		$dc->title = $post->post_title;
		$dc->description = $post->post_content;
		$dc->short_description = $post->post_excerpt;
		$dc->date_pub = $post->post_date;
		$dc->amount = (float)get_post_meta($post->ID, 'mg_dn_amount', true);
		$dc->featured_image_id = has_post_thumbnail($post->ID) ? get_post_thumbnail_id($post->ID) : null;
		$dc->permalink = get_permalink($post->ID);
		
		return $dc;
	}

	public static function create_donation($args) {
		$post_arr = array(
			'post_type' => 'mg_donation',
			'post_status' => 'publish',
			'post_parent' => $args['cause']
		);
		
		$post_id = wp_insert_post($post_arr, true);
		if (is_wp_error($post_id))
			return $post_id;
		
		update_post_meta($post_id, 'mg_dn_amount', $args['amount']);
		update_post_meta($post_id, 'mg_dn_donor', $args['donor']);
		update_post_meta($post_id, 'mg_dn_txn_id', $args['txn_id']);
		
		// Increase the collected so far amount of the relative DC
		$dc_amount = (float)get_post_meta($args['cause'], 'mg_dn_amount', true);
		$dc_amount += $args['amount'];
		update_post_meta($args['cause'], 'mg_dn_amount', $dc_amount);
		
		return $post_id;
	}
	
	public static function get_donation($id) {
		return self::get_donation_by('id', $id);
	}
	
	public static function get_donation_by($field, $value) {
		$post = null;
		
		switch($field) {
			case 'id':
				$post = get_post($value);
				break;
			case 'txn_id':
				$posts = get_posts(array(
					'post_type' => 'mg_donation',
					'posts_per_page' => 1,
					'meta_key' => 'mg_dn_txn_id',
					'meta_value' => $value
				));
				if (!empty($posts))
					$post = array_shift($posts);
				break;
			default:
		}
	
		if (empty($post))
			return null;

		$dnt = new StdClass();
		
		$dnt->id = $post->ID;
		$dnt->dc_id = $post->post_parent;
		$dnt->date = $post->post_date;
		$dnt->amount = (float)get_post_meta($post->ID, 'mg_dn_amount', true);
		$dnt->donor = get_post_meta($post->ID, 'mg_dn_donor', true);
		$dnt->txn_id = get_post_meta($post->ID, 'mg_dn_txn_id', true);
		
		return $dnt;
	}
	
	public static function get_donation_cause_titles() {
		$causes = array();
		
		$posts = get_posts(array(
			'post_type' => 'mg_donation_cause',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));
		
		foreach ($posts as $post)
			$causes[$post->ID] = $post->post_title;
			
		// Alt solution: Do direct SQL query?
		
		// Yet another solution: 
		/* $query = new WP_Query(array(
			'post_type' => 'mg_donation_cause',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));
		
		global $mg_dn_dc;
		while ($query->have_posts()) {
			$query->the_post();
			$causes[$mg_dn_dc->id] = $mg_dn_dc->title;
		}
		
		wp_reset_postdata(); */
		
		return $causes;
	}

}
