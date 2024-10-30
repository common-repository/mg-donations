<?php

class mg_dn_Settings_UI {

	const OPTION_GROUP = 'mg_dn';
	
	private $settings;

	public function __construct($settings) {
		$this->settings = $settings;
		
		add_action('admin_init', array($this, 'setup_settings_api'));
		add_action('admin_notices', array($this, 'user_feedback'));
	}
	
	public function setup_settings_api() {
		register_setting(self::OPTION_GROUP, $this->settings->get_option_name(), array($this, 'sanitize'));
		
		add_settings_section(
			'mg_dn_settings_section_general',
			'General',
			'', // callback
			'mg_dn_settings_page'
		);
		
		add_settings_field(
			'mg_dn_settings_field_recipient_email',
			'PayPal Email',
			array($this, 'render_control_recipient_email'),
			'mg_dn_settings_page',
			'mg_dn_settings_section_general'
			//$args
		);
		
		add_settings_field(
			'mg_dn_settings_field_currency',
			'Currency',
			array($this, 'render_control_currency'),
			'mg_dn_settings_page',
			'mg_dn_settings_section_general'
			//$args
		);
		
		add_settings_field(
			'mg_dn_settings_field_return_url',
			'Return URL',
			array($this, 'render_control_return_url'),
			'mg_dn_settings_page',
			'mg_dn_settings_section_general'
			//$args
		);
		
		add_settings_field(
			'mg_dn_settings_field_cancel_url',
			'Cancel URL',
			array($this, 'render_control_cancel_url'),
			'mg_dn_settings_page',
			'mg_dn_settings_section_general'
			//$args
		);
		
		add_settings_field(
			'mg_dn_settings_field_return_link_text',
			'Return Link Text',
			array($this, 'render_control_return_link_text'),
			'mg_dn_settings_page',
			'mg_dn_settings_section_general'
			//$args
		);
		
		add_settings_field(
			'mg_dn_settings_field_sandbox',
			'PayPal Sandbox',
			array($this, 'render_control_sandbox'),
			'mg_dn_settings_page',
			'mg_dn_settings_section_general'
			//$args
		);
		
		add_settings_field(
			'mg_dn_settings_field_debug',
			'Enable Debug',
			array($this, 'render_control_debug'),
			'mg_dn_settings_page',
			'mg_dn_settings_section_general'
			//$args
		);
	}
	
	public function settings_page() {
		$this->curr_settings = $this->settings->get();
		?>
		<div class="wrap">
			<form action="options.php" method="POST">
				<?php settings_fields(self::OPTION_GROUP); ?>
				<?php do_settings_sections('mg_dn_settings_page');  ?>
				<?php submit_button(__('Save Changes'), 'primary', 'Update'); ?>
				<?php submit_button(__( 'Restore Factory Settings'), 'secondary', 'restore_factory_settings'); ?>
			</form>
		</div>
		<?php
	}
	
	public function get_page() {
		return array($this, 'settings_page');
	}
	
	public function render_control_recipient_email() {
		?>
		<input type="text" name="<?php echo $this->settings->get_option_name() . '[recipient_email]'; ?>" value="<?php echo esc_attr($this->curr_settings['recipient_email']); ?>" size="40">
	<?php
	}
	
	public function render_control_currency() {
		global $mg_dn_plugin;
		$currencies = $mg_dn_plugin->money->get_currencies();
		$current_currency = $this->curr_settings['currency'];
		?>
		<select name="<?php echo $this->settings->get_option_name() . '[currency]'; ?>">
			<?php foreach ($currencies as $code => $info): ?>
				<option value="<?php echo esc_attr($code); ?>"<?php selected($current_currency, $code); ?>><?php echo esc_html($info['name'] . " ({$info['sign']})"); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	public function render_control_return_url() {
		?>
		<input type="text" name="<?php echo $this->settings->get_option_name() . '[return_url]'; ?>" value="<?php echo esc_attr($this->curr_settings['return_url']); ?>" size="40">
		<?php
	}
	
	public function render_control_cancel_url() {
		?>
		<input type="text" name="<?php echo $this->settings->get_option_name() . '[cancel_url]'; ?>" value="<?php echo esc_attr($this->curr_settings['cancel_url']); ?>" size="40">
		<?php
	}
	
	public function render_control_return_link_text() {
		?>
		<input type="text" name="<?php echo $this->settings->get_option_name() . '[return_link_text]'; ?>" value="<?php echo esc_attr($this->curr_settings['return_link_text']); ?>" size="40">
		<?php
	}
	
	public function render_control_sandbox() {
		?>
		<input type="checkbox" name="<?php echo $this->settings->get_option_name() . '[sandbox]'; ?>" value="yes"<?php checked($this->curr_settings['sandbox'], true); ?>>
		<?php
	}
	
	public function render_control_debug() {
		?>
		<input type="checkbox" name="<?php echo $this->settings->get_option_name() . '[debug]'; ?>" value="yes"<?php checked($this->curr_settings['debug'], true); ?>>
		<?php
	}
	
	public function sanitize($new_settings) {
		if (isset($_POST['restore_factory_settings']))
			$settings = $this->get_factory_settings();
		else
			$settings = $this->settings->sanitize($new_settings);
		
		if (count(get_settings_errors('mg_dn')) === 0)
			add_settings_error('mg_dn', 'ok', __('Settings saved', 'mg_dn'), 'updated');
		
		return $settings;
	}
	
	public function user_feedback() {
		settings_errors('mg_dn');
	}
	
}
