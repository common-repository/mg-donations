<?php

class mg_dn_Settings {

	const OPTION_NAME = 'mg_dn';

	public function __construct() {
	}
	
	public function get() {
		return get_option(self::OPTION_NAME);
	}
	
	public function get_option_name() {
		return self::OPTION_NAME;
	}
	
	public function get_factory_settings() {
		return array(
			'recipient_email' => '', 
			'currency' => 'EUR',
			'sandbox' => false,
			'debug' => false,
			'return_url' => '',
			'cancel_url' => '',
			'return_link_text' => ''
		);
	}
	
	public function sanitize($new_settings) {
		$settings = $this->get();
		
		$settings['sandbox'] = empty($new_settings['sandbox']) ? false : true;
		$settings['debug'] = empty($new_settings['debug']) ? false : true;
		
		if (is_email($new_settings['recipient_email']))
			$settings['recipient_email'] = $new_settings['recipient_email'];
		else
			add_settings_error('mg_dn', 'ko_invalid_email', __('The PayPal email is invalid', 'mg_dn'), 'error');
		
		global $mg_dn_plugin;
		if (in_array($new_settings['currency'], array_keys($mg_dn_plugin->money->get_currencies())))
			$settings['currency'] = $new_settings['currency'];
		else
			add_settings_error('mg_dn', 'ko_invalid_currency', __('The currency  is invalid', 'mg_dn'), 'error');
		
		$settings['return_url'] = esc_url_raw($new_settings['return_url'], array('http', 'https'));
		$settings['cancel_url'] = esc_url_raw($new_settings['cancel_url'], array('http', 'https'));
		$settings['return_link_text'] = sanitize_text_field($new_settings['return_link_text']);
		
		return $settings;
	}
	
	public static function delete_settings() {
		delete_option(self::OPTION_NAME);
	}
	
	public function setup() {
		$factory_settings = $this->get_factory_settings();
		$curr_settings = get_option(self::OPTION_NAME);
		
		if ($curr_settings === false) {
			add_option(self::OPTION_NAME, $factory_settings); // This is a fresh install
		}
		else {
			// Remove from the current settings all the key that are not present in factory settings
			foreach (array_keys($curr_settings) as $key)
				if (!key_exists($key, $factory_settings))
					unset($curr_settings[$key]);
					
			// Add to current settings all the new keys in factory settings
			foreach (array_keys($factory_settings) as $key)
				if (!key_exists($key, $curr_settings))
					$curr_settings[$key] = $factory_settings[$key];
			
			update_option(self::OPTION_NAME, $curr_settings);
		}
	}
	
}
