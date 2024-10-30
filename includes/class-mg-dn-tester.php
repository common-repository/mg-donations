<?php

class mg_dn_Tester {

	public function __construct() {
		add_action('init', array($this, 'create_donation'));
		 //add_action('admin_init', array($this, 'test_fetch_donation'));
	}
	
	public function create_donation() {
		if (defined( 'DOING_AJAX' ))
			return;

		$dtn_id = mg_dn_Data::create_donation(array(
			'amount' => 10,
			'cause' => 368,
			'donor' => 'shop.customer@example.it',
			'txn_id' => 'foobar99'
		));
		
		if (is_wp_error($dtn_id))
			trigger_error('Test Donation creation failed');
		else
			trigger_error('Test Donation created');
		
	}

/* public function test_fetch_donation() {
	 if (defined( 'DOING_AJAX' ))
	 return;
	require_once MG_DN_INCLUDES . 'class-mg-donation.php';
	$donation = new mg_Donation(45);
	trigger_error($donation->id);
	 trigger_error($donation->amount);
	 trigger_error($donation->cause);
	 trigger_error($donation->donor);
	 trigger_error($donation->date);
	 }
			
		} */
	

}