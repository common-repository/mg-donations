<?php

class mg_dn_Money {

	public function __construct() {
	}
	
	public function get_currencies() {
		return array(
			'EUR' => array(
				'name' => __('Euro', 'mg_dn'),
				'sign' => '&euro;'
			),
			'USD' => array(
				'name' => __('U.S. Dollar', 'mg_dn'),
				'sign' => '&#36;'
			),
			'AUD' => array(
				'name' => __('Australian Dollar', 'mg_dn'),
				'sign' => '&#36;'
			),
			'CAD' => array(
				'name' => __('Canadian Dollar', 'mg_dn'),
				'sign' => '&#36;'
			),
			'HKD' => array(
				'name' => __('Honk Kong Dollar', 'mg_dn'),
				'sign' => '&#36;'
			),
			'NZD' => array(
				'name' => __('New Zeland Dollar', 'mg_dn'),
				'sign' => '&#36;'
			),
			'GBP' => array(
				'name' => __('Pound Sterling', 'mg_dn'),
				'sign' => '&pound;'
			),
			'CHF' => array(
				'name' => __('Swiss Franc', 'mg_dn'),
				'sign' => '&#67;&#72;&#70;'
			),
			'JPY' => array(
				'name' => __('Japanese Yen', 'mg_dn'),
				'sign' => '&yen;'
			),
		);
	}
	
	public function format($amount) {
		global $mg_dn_plugin;
		
		$settings = $mg_dn_plugin->settings->get();
		$currencies = $this->get_currencies();
		$currency_sign = $currencies[$settings['currency']]['sign'];
		return "$amount $currency_sign";
	}

}

