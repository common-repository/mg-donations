<?php

class mg_dn_List_Table_Donation_Cause {

	public function __construct() {
		add_filter('manage_mg_donation_cause_posts_columns', array($this, 'column_headers'));
		add_action('manage_mg_donation_cause_posts_custom_column', array($this, 'render_col'), 10, 2);
		add_filter('post_row_actions', array($this, 'row_actions'), 10, 2);
	}
	
	public function column_headers($curr_cols) {
		$cols = array();
		
		$cols['cb'] = '<input type="checkbox" />';
		//$cols['id'] = __('ID', 'mg_dn');
		$cols['title'] = __('Title', 'mg_dn');
		$cols['amount'] = __('Amount', 'mg_dn');
		//$cols['author'] = __('Author', 'mg_dn');
		$cols['date'] = __('Date', 'mg_dn');
	
		return $cols;
	}
	
	public function render_col($col_name, $post_id) {
		global $mg_dn_dc, $mg_dn_plugin;
		
		switch ($col_name) {
			case 'amount':
				echo esc_html($mg_dn_plugin->money->format($mg_dn_dc->amount));
				break;
		}
	}
	
	public function row_actions($actions, $post) {
		if ($post->post_type === 'mg_donation_cause')
			$actions = array_merge(array('id' => "ID: $post->ID"), $actions);
		
		return $actions;
	}

}
