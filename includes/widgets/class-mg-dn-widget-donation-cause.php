<?php

if (!defined('ABSPATH')) exit;

class mg_dn_Widget_Donation_Cause extends WP_Widget {

	private $button_templates  = array(
		'paypal-1',
		'paypal-2',
		'paypal-3',
		'paypal-4',
		'default'
	);

	private $factory_settings = array(
		'title' => '', 
		'dc' => 0,
		'show_dc_title' => true,
		'show_dc_permalink' => true,
		'show_featured_image' => true,
		'show_excerpt' => true,
		'button_template' => 'default',
		'button_text' => 'Donate'
	);

	public function __construct() {
		parent::__construct(
			'mg_dn_widget_donation_cause',
			__('mg-dn: Donation Cause', 'mg_dn'),
			array(
				'description' => __('Display a Donation Cause', 'mg_dn'),
				'classname' => 'mg-dn-widget-donation-cause'
			) 
		);
	}
	
	public function form($instance) {
		$instance = wp_parse_args((array)$instance, $this->factory_settings);
		
		$title = $instance['title'];
		$causes = mg_dn_Data::get_donation_cause_titles();
		$title_field_id = $this->get_field_id('title');
		$title_field_name = $this->get_field_name('title');
		
		$current_dc = $instance['dc'];
		if ($current_dc !== 0 && !isset($causes[$current_dc])) // Check for deleted causes
			$current_dc = 0;
		$dc_field_id = $this->get_field_id('dc');
		$dc_field_name = $this->get_field_name('dc');
		
		$show_dc_title_field_id = $this->get_field_id('show_dc_title');
		$show_dc_title_field_name = $this->get_field_name('show_dc_title');
		
		$show_dc_permalink_field_id = $this->get_field_id('show_dc_permalink');
		$show_dc_permalink_field_name = $this->get_field_name('show_dc_permalink');
		
		$show_featured_image_field_id = $this->get_field_id('show_featured_image');
		$show_featured_image_field_name = $this->get_field_name('show_featured_image');
		
		$show_excerpt_field_id = $this->get_field_id('show_excerpt');
		$show_excerpt_field_name = $this->get_field_name('show_excerpt');
		
		$button_template = $instance['button_template'];
		$button_template_field_id = $this->get_field_id('button_template');
		$button_template_field_name = $this->get_field_name('button_template');
		
		$button_text = $instance['button_text'];
		$button_text_field_id = $this->get_field_id('button_text');
		$button_text_field_name = $this->get_field_name('button_text');
		
		?>
			<p>
				<label for="<?php echo $title_field_id; ?>"><?php _e('Widget title:', 'mg_dn'); ?></label> 
				<input class="widefat" id="<?php echo $title_field_id; ?>" name="<?php echo $title_field_name; ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				<span class="hint">Leave blank it you dont' need it</span>
			</p>
			
			<p>
				<label for="<?php echo $dc_field_id; ?>"><?php _e('Select a Donation Cause:', 'mg_dn'); ?></label> 
				<select id="<?php echo $dc_field_id?>" name="<?php echo $dc_field_name; ?>" class="widefat">
					<option value="0"<?php selected($current_dc, 0); ?>><?php _e('Nothing selected', 'mg_dn'); ?></option>
					<?php foreach ($causes as $id => $title): ?>
						<?php
						if (strlen($title) > 30)
							$title = substr($title, 0, 30) . '...';
						?>
						<option 
							value="<?php echo esc_attr($id); ?>"
							<?php selected($current_dc, $id); ?>
						>
							<?php echo esc_html($title); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
			
			<p>Choose which DC information to display: </p>
			
			<p>
				<input type="checkbox"<?php checked($instance['show_dc_title'], true); ?> id="<?php echo $show_dc_title_field_id; ?>" name="<?php echo $show_dc_title_field_name; ?>" value="yes">
				<label for="<?php echo $show_dc_title_field_id; ?>"><?php _e('Title', 'mg_dn'); ?></label>
			</p>
			
			<p>
				<input type="checkbox"<?php checked($instance['show_featured_image'], true); ?> id="<?php echo $show_featured_image_field_id; ?>" name="<?php echo $show_featured_image_field_name; ?>" value="yes">
				<label for="<?php echo $show_featured_image_field_id; ?>"><?php _e('Featured image', 'mg_dn'); ?></label>
			</p>
				
			<p>
				<input type="checkbox"<?php checked($instance['show_dc_permalink'], true); ?> id="<?php echo $show_dc_permalink_field_id; ?>" name="<?php echo $show_dc_permalink_field_name; ?>" value="yes">
				<label for="<?php echo $show_dc_permalink_field_id; ?>"><?php _e('Permalink', 'mg_dn'); ?></label>
			</p>
			
			<p>
				<input type="checkbox"<?php checked($instance['show_excerpt'], true); ?> id="<?php echo $show_excerpt_field_id; ?>" name="<?php echo $show_excerpt_field_name; ?>" value="yes">
				<label for="<?php echo $show_excerpt_field_id; ?>"><?php _e('Excerpt', 'mg_dn'); ?></label>
			</p>
			
			<script>
				jQuery(document).ready(function($) {
					var
						titleCb = $('#<?php echo $show_dc_title_field_id; ?>'),
						fiCb = $('#<?php echo $show_featured_image_field_id; ?>'),
						permalinkCb = $('#<?php echo $show_dc_permalink_field_id; ?>')
					;
					
					$(titleCb).add(fiCb).on('change', function() {
						var titleChecked = titleCb.prop('checked');
						var fiChecked = fiCb.prop('checked');
						
						if (titleChecked || fiChecked) {
							permalinkCb.prop('disabled', false);
						}
						else {
							permalinkCb.prop('checked', false);
							permalinkCb.prop('disabled', true);
						}
				});
			});
			</script>
			
			<p>Choose the donate button:</p>
			<?php
				foreach ($this->button_templates as $bt) {
					?>
					<p>
						<input type="radio"<?php checked($button_template, $bt); ?> id="<?php echo $button_template_field_id; ?>" name="<?php echo $button_template_field_name; ?>" value="<?php echo esc_attr($bt); ?>">
						<label for="<?php echo $button_template_field_id; ?>"><?php mg_dn_get_template("buttons/$bt"); ?></label>
					</p>
					<?php
				}
			?>
			
			<p>
				<label for="<?php echo $button_text_field_id; ?>"><?php _e('Button text', 'mg_dn'); ?></label>
				<input type="text" id="<?php echo $button_text_field_id; ?>" name="<?php echo $button_text_field_name; ?>" value="<?php echo esc_attr($button_text); ?>" class="widefat">
				<span class="hint">Not used for PayPal buttons</span>
			</p>
		<?php
	}
	
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']); // sanitize_text_field()?
		
		$instance['dc'] = absint($new_instance['dc']);
		
		$instance['show_dc_title'] = empty($new_instance['show_dc_title']) ? false : true;
		
		$instance['show_featured_image'] = empty($new_instance['show_featured_image']) ? false : true;

		if (!$instance['show_dc_title'] && !$instance['show_featured_image'])
			$instance['show_dc_permalink'] = false;
		else
			$instance['show_dc_permalink'] = empty($new_instance['show_dc_permalink']) ? false : true;

		$instance['show_excerpt'] = empty($new_instance['show_excerpt']) ? false : true;
		
		if (in_array($new_instance['button_template'], $this->button_templates))
			$instance['button_template'] = $new_instance['button_template'];
		else
			$instance['button_template'] = 'default';
		
		$instance['button_text'] = strip_tags($new_instance['button_text']);
		
		return $instance;
	}
	
	public function widget($args, $instance) {
		$instance = wp_parse_args((array)$instance, $this->factory_settings);
		
		if ($instance['dc'] === 0)
			return;
			
		$q = new WP_Query(array(
			'p' => $instance['dc'],
			'post_type' => 'mg_donation_cause'
		));
		
		if (!$q->have_posts()) // The DC could be have deleted
			return;
		
		// Setup DC template tags
		$q->the_post();
		
		ob_start();
			mg_dn_get_template('widgets/donation-cause', array(
				'show_title' => $instance['show_dc_title'],
				'show_permalink' => $instance['show_dc_permalink'],
				'show_featured_image' => $instance['show_featured_image'],
				'show_excerpt' => $instance['show_excerpt'],
				'button_template' => $instance['button_template'],
				'button_text' => $instance['button_text']
			));
		$widget_content = ob_get_clean();
		wp_reset_postdata();
		
		$widget_content = apply_filters('mg_dn_widget_dc_content', $widget_content);
		
		if (empty($widget_content))
			return;
			
		extract($args);
		$markup = $before_widget;
		
		$widget_title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		if ($widget_title)
			$markup .= $before_title . $widget_title . $after_title;
		
		$markup .= $widget_content;
		$markup .= $after_widget;
		
		$markup = apply_filters('mg_dn_widget_dc_markup', $markup);
		
		echo $markup;
	}

}
