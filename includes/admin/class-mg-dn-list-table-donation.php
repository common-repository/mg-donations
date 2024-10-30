<?php

class mg_dn_List_Table_Donation {

	public function __construct() {
		add_filter('manage_mg_donation_posts_columns', array($this, 'column_headers'));
		add_action('manage_mg_donation_posts_custom_column', array($this, 'render_col'), 10, 2);
	}
	
	public function column_headers($curr_cols) {
		$cols = array();

		$cols['cb'] = '<input type="checkbox" />';
		$cols['id'] = __('ID', 'mg_dn');
		$cols['amount'] = __('Amount', 'mg_dn');
		$cols['donor'] = __('Donor', 'mg_dn');
		$cols['cause'] = __('Cause', 'mg_dn');
		$cols['date'] = __('Date', 'mg_dn');
	
		return $cols;
	}
	
	public function render_col($col_name, $post_id) {
		global $mg_dn_dnt;
		
		switch ($col_name) {
			case 'id':
				echo esc_html($mg_dn_dnt->id);
				break;
			case 'amount':
				global $mg_dn_plugin;
				echo esc_html($mg_dn_plugin->money->format($mg_dn_dnt->amount));
				break;
			case 'donor':
				echo esc_html($mg_dn_dnt->donor);
				break;
			case 'cause':
				$dc = mg_dn_Data::get_donation_cause($mg_dn_dnt->dc_id);
				echo '<a href="' . get_edit_post_link($mg_dn_dnt->dc_id) . '">' . esc_html($dc->title) . '</a>';
				break;
			case 'date':
				echo esc_html($mg_dn_dnt->date);
				break;
		}
	}

}
