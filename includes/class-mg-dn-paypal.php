<?php

class mg_dn_PayPal {

	private $paypal_url;
	private $debug;
	private $recipient_email;
	private $currency;
	private $return_url;
	private $cancel_url;
	private $return_button_text;
	private $return_link_text;

	public function __construct($cfg) {
		$this->paypal_url = $cfg['sandbox'] ? 'https://www.sandbox.paypal.com/cgi-bin/websrc' : 'https://www.paypal.com/cgi-bin/websrc';
		$this->debug = $cfg['debug'];
		$this->recipient_email = $cfg['recipient_email'];
		$this->currency = $cfg['currency'];
		$this->return_url = $cfg['return_url'];
		$this->cancel_url = $cfg['cancel_url'];
		$this->return_link_text = $cfg['return_link_text'];
		
		add_action('mg_dn_ipn', array($this, 'ipn_listener'));
	}
	
	public function get_button($cause_title, $cause_id, $button_template, $button_args = array()) {
		?>
		<form method="POST" action="<?php echo esc_url($this->paypal_url); ?>" class="mg-dn-button-form">
			<input type="hidden" name="cmd" value="_donations">
			<input type="hidden" name="business" value="<?php echo esc_attr($this->recipient_email); ?>">
			<input type="hidden" name="notify_url" value="<?php echo esc_url(add_query_arg('mg_dn', 'ipn', home_url('/'))); ?>">
			
			<input type="hidden" name="currency_code" value="<?php echo esc_attr($this->currency); ?>">
			<!--<input type="hidden" name="amount" value="10">-->
			<input type="hidden" name="item_name" value="<?php echo esc_attr($cause_title); ?>">
			<input type="hidden" name="item_number" value="<?php echo esc_attr($cause_id); ?>">
			
			<?php if (!empty($this->return_url)): ?>
				<input type="hidden" name="return" value="<?php echo esc_url($this->return_url); ?>">
			<?php endif; ?>
			<?php if (!empty($this->cancel_url)): ?>
				<input type="hidden" name="cancel_return" value="<?php echo esc_url($this->cancel_url); ?>">
			<?php endif; ?>
			<?php if (!empty($this->return_link_text)): ?>
				<input type="hidden" name="cbt" value="<?php echo esc_attr($this->return_link_text); ?>">
			<?php endif; ?>
			
			<?php mg_dn_get_template("buttons/$button_template", $button_args); ?>

		</form>
		<?php
	}
	
	public function ipn_listener() {
		if ($this->verify_ipn_message($_POST)) {
			header('HTTP/1.1 200 OK'); // Reply to IPN message request
			
			$this->process_donation($_POST);
		}
		else {
			wp_die( "PayPal IPN Request Failure", "PayPal IPN", array( 'response' => 200 ) );
		}
		
	}
	
	private function verify_ipn_message($msg) {
		global $mg_dn_plugin;
		
		$mg_dn_plugin->log("IPN message verification");
		$mg_dn_plugin->log("IPN Message: " . print_r($msg, true));
		$msg = stripslashes_deep($msg);
		//$mg_dn_plugin->log("IPN Message after stripslashes_deep(): " . print_r($msg, true));
		
		$paypal_url = $this->paypal_url;
		$mg_dn_plugin->log("PayPal URL: $this->paypal_url");
		
		$payload = array('cmd' => '_notify-validate');
		$payload = array_merge($payload, $msg); // stripslashdeep?
		//$mg_dn_plugin->log("Sending back: " . print_r($payload, true));
		
		$args = array(
			'body' => $payload,
			'sslverify' => false,
			'httpversion' => '1.1',
			'timeout' => 60,
			'compress' => false,
			'decompress' => false
			//'user-agent' => 'WooCommerce/' . WC()->version
		);
		//$mg_dn_plugin->log("HTTP POST args: " . print_r($args, true));
		
		$response = wp_remote_post($this->paypal_url, $args);
		//$mg_dn_plugin->log("PayPal response: " . print_r($response, true));
		
		if (
			!is_wp_error($response) && 
			200 <= $response['response']['code'] && $response['response']['code'] < 300 && 
			(strcmp($response['body'], "VERIFIED") == 0) 
		) {
			$mg_dn_plugin->log('Received valid response from PayPal');
			return true;
		}
		else {
			$mg_dn_plugin->log('Received invalid response from PayPal');
			
			if ( is_wp_error($response))
				$m_dn->log('WP_Error response: ' . $response->get_error_message() );
			
			return false;
		}
	}
	
	private function process_donation($msg) {
		global $mg_dn_plugin;
		
		$mg_dn_plugin->log('Processing donation...');
		
		if (!$this->donation_checks($msg)) {
			$mg_dn_plugin->log('Donation not created');
		} else {
			$dtn_id = mg_dn_Data::create_donation(array(
				'amount' => (float)$msg['mc_gross'],
				'cause' => (int)$msg['item_number'],
				'donor' => $msg['payer_email'],
				'txn_id' => $msg['txn_id']
			));
		
			if (is_wp_error($dtn_id))
				$mg_dn_plugin->log('Cannot create donation'); 
			else
				$mg_dn_plugin->log("Donation # $dtn_id created");
		}
	}
	
	private function donation_checks($msg) {
		global $mg_dn_plugin;
		
		if (!isset($msg['business'])) {
			$mg_dn_plugin->log('Invalid: business email not provided');
			return false;
		}
		if ($msg['business'] != $this->recipient_email) {
			$mg_dn_plugin->log("Invalid: business email {$msg['business']} is not the same as the one provided in plugin settings {$this->recipient_email}");
			return false;
		}
		 
		if ($msg['txn_type'] !== 'web_accept') {
			$mg_dn_plugin->log('Invalid: txn_type invalid');
			return false;
		}
		 
		if ($msg['payment_status'] !== 'Completed') {
			$mg_dn_plugin->log("Invalid: payment_status: {$msg['payment_status']}");
			return false;
		}
		
		$dnt = mg_dn_Data::get_donation_by('txn_id', $msg['txn_id']);
		if (!empty($dnt)) { // Duplicate IPN
			$mg_dn_plugin->log("Invalid: duplicate IPN: {$msg['tax_id']}");
			return false;
		}
		
		return true;
	}

}

