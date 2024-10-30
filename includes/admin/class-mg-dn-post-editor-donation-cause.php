<?php

if (!defined('ABSPATH')) exit;

class mg_dn_Post_Editor_Donation_Cause {

	public function __construct() {
		add_action('edit_form_top', array($this, 'render_amount'));
	}
	
	public function render_amount($post) {
		if ($post->post_type !== 'mg_donation_cause')
			return;
			
		$dc = mg_dn_Data::get_donation_cause($post->ID);
		global $mg_dn_plugin;
		$amount = $mg_dn_plugin->money->format($dc->amount);
		?>
		<p id="mg_dn_amount">
			Collected Amount: <span class="money"><?php echo esc_html($amount); ?></span>
		</p>
		<?php
	}

}

