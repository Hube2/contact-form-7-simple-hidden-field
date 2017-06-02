<?php 

	/*
		Plugin Name: Hidden Field for Contact Form 7
		Plugin URI: https://github.com/Hube2/contact-form-7-simple-hidden-field
		Description: Simple Hidden Fields for Contact Form 7. Requires contact form 7
		Version: 2.0.2
		Author: John A. Huebner II
		Author URI: https://github.com/Hube2/
		License: GPL
	*/
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) { die; }
	
	new wpcf7_simple_hidden_field();
	
	class wpcf7_simple_hidden_field {
		
		public function __construct() {
			add_action('plugins_loaded', array($this, 'init'), 20);
		} // end public function __construct
		
		public function init() {
			if(function_exists('wpcf7_add_form_tag')){
				/* Shortcode handler */		
				wpcf7_add_form_tag('simplehidden', array($this, 'simple_shortcode_handler'), true);
				wpcf7_add_form_tag('dynamichidden2', array($this, 'dynamic_shortcode_handler'), true);
			} else {
				wpcf7_add_shortcode('simplehidden', array($this, 'simple_shortcode_handler'), true);
				wpcf7_add_shortcode('dynamichidden2', array($this, 'dynamic_shortcode_handler'), true);
			}
			add_filter('wpcf7_validate_simplehidden', array($this, 'validation_filter'), 10, 2);
			add_filter('wpcf7_validate_dynamichidden2', array($this, 'validation_filter'), 10, 2);
			add_action('admin_init', array($this, 'add_tg_generator'), 25);
			add_filter('test_hidden_field_filter', array($this, 'test_filter'), 10, 2);
		} // end public function init
		
		public function test_filter($value, $args) {
			return 'this is a test';
		}
		
		public function simple_shortcode_handler($tag) {
			// generates html for form field
			if (is_a($tag, 'WPCF7_FormTag')) {
				$tag = (array)$tag;
			}
			if (empty($tag)) {
				return '';
			}
			$name = $tag['name'];
			if (empty($name)) {
				return '';
			}
			$wpcf7_contact_form = WPCF7_ContactForm::get_current();
			// most attributes not really needed, not included
			$name_att = $name;
			$value = '';
			$values = (array)$tag['values'];
			if (isset($values[0])) {
				$value = $values[0];
			} elseif (isset($_GET[$name])) {
				$value = sanitize_text_field($_GET[$name]);
			}
			$atts = ' name="'.$name.'" value="'.$value.'" autocomplete="off"';
			$html = '<input type="hidden"'.$atts.' />';
			return $html;
		} // end public function shortcode_handler
		
		public function dynamic_shortcode_handler($tag) {
			// generates html for form field
			$tag_o = $tag;
			if (is_a($tag, 'WPCF7_FormTag')) {
				$tag = (array)$tag;
			}
			$name = $tag['name'];
			if (empty($name)) {
				return '';
			}
			$wpcf7_contact_form = WPCF7_ContactForm::get_current();
			// most attributes not really needed, not included
			$name_att = $name;
			$filter = '';
			$filter_args = array();
			$filter_string = '';
			$values = $tag['values'];
			if (isset($values[0])) {
				$filter_string = $values[0];
			}
			//echo $filter_string;
			if ($filter_string != '') {
				$filter_parts = explode(' ', $filter_string);
				$filter = trim($filter_parts[0]);
				$count = count($filter_parts);
				for($i=1; $i<$count; $i++) {
					if (trim($filter_parts[$i]) != '') {
						$arg_parts = explode('=', $filter_parts[$i]);
						if (count($arg_parts) == 2) {
							$filter_args[trim($arg_parts[0])] = trim($arg_parts[1], ' \'');
						} else {
							$filter_args[] = trim($arg_parts[0], ' \'');
						}
					} // end if filter part
				} // end for
			} // end if filter string
			$value = '';
			if ($filter != '') {
				$value = apply_filters($filter, $value, $filter_args);
			}
			$atts = ' name="'.$name.'" value="'.$value.'" autocomplete="off"';
			$html = '<input type="hidden"'.$atts.' />';
			return $html;
		} // end public function shortcode_handler
		
		public function validation_filter($result, $tag) {
			// validates field on submit
			// not required, it's a simple hidden field
			return $result;
		} // end public function validation_filter
		
		public function add_tg_generator() {
			// called on init to add the tag generator or cf7
			// wpcf7_add_tag_generator($name, $title, $elm_id, $callback, $options = array())
			if (!function_exists('wpcf7_add_tag_generator')) {
				return;
			}
			$name = 'simplehidden';
			$title = __('Simple Hidden Field', 'wpcf7');
			$elm_id = 'wpcf7-tg-pane-simplehidden';
			$callback = array($this, 'simple_tg_pane');
			wpcf7_add_tag_generator($name, $title, $elm_id, $callback);
			$name = 'dynamichidden2';
			$title = __('Dynamic Hidden Field', 'wpcf7');
			$elm_id = 'wpcf7-tg-pane-dynamichidden2';
			$callback = array($this, 'dynamic_tg_pane');
			wpcf7_add_tag_generator($name, $title, $elm_id, $callback);
		} // end public function add_tag_generator
		
		public function simple_tg_pane($form, $args = '') {
			// output the code for CF7 tag generator
			if (class_exists('WPCF7_TagGenerator')) {
				// tag generator for CF7 >= v4.2
				$args = wp_parse_args( $args, array() );
				$desc = __('Generate a form-tag for a Simple Hidden field. For more details, see %s.');
				$desc_link = '<a href="https://wordpress.org/plugins/contact-form-7-simple-hidden-field/" target="_blank">'.__( 'Contact Form 7 - Simple Hidden Field').'</a>';
				?>
					<div class="control-box">
						<fieldset>
							<legend><?php echo sprintf(esc_html($desc), $desc_link); ?></legend>
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row">
											<label for="<?php 
													echo esc_attr($args['content'].'-name'); ?>"><?php 
													echo esc_html(__('Name', 'contact-form-7')); ?></label>
										</th>
										<td>
											<input type="text" name="name" class="tg-name oneline" id="<?php 
													echo esc_attr($args['content'].'-name' ); ?>" />
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="<?php 
													echo esc_attr($args['content'].'-values'); ?>"><?php 
													echo esc_html(__('Value', 'contact-form-7')); ?></label>
										</th>
										<td>
											<input type="text" name="values" class="oneline" id="<?php 
													echo esc_attr($args['content'].'-values' ); ?>" /><br />
											<?php echo esc_html(__('Enter the value for the hidden field')); ?>
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
					</div>
					<div class="insert-box">
						<input type="text" name="simplehidden" class="tag code" readonly="readonly" onfocus="this.select()" />
						<div class="submitbox">
							<input type="button" class="button button-primary insert-tag" value="<?php 
									echo esc_attr(__('Insert Tag', 'contact-form-7')); ?>" />
						</div>
					</div>
				<?php 
			} else {
				$type='simplehidden'
				// tag generator for CF7 <v4.2
				// but modified slightly so it will still work with with >= v4.2
				?>
					<div id="wpcf7-tg-pane-<?php echo $type; ?>" class="control-box">
						<form action="">
							<table>
								<tr>
									<td>
										<?php echo esc_html(__('Name', 'contact-form-7')); ?><br />
										<input type="text" name="name" class="tg-name oneline" />
									</td>
									<td></td>
								</tr>
							</table>
							<table>
								<tr>
									<td>
										<?php echo esc_html(__('Value', 'contact-form-7')); ?><br />
											<input type="text" name="values" class="oneline" /><br />
											<?php echo esc_html(__('Enter the value for the hidden field')); ?>
									</td>
								</tr>
							</table>
							<div class="tg-tag">
								<?php echo esc_html(__('Copy this code and paste it into the form left.')); ?><br />
								<input type="text" name="<?php 
										echo $type; ?>" class="tag" readonly="readonly" onfocus="this.select()" style="width:100%;" />
							</div>
							<div class="tg-mail-tag">
								<?php echo esc_html(__('And, put this code into the Mail fields below.')); ?><br />
								<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" style="width:100%;" />
							</div>
						</form>
					</div>
				<?php 
			}
		} // end public function simple_tg_pane
		
		public function dynamic_tg_pane($form, $args = '') {
			// output the code for CF7 tag generator
			if (class_exists('WPCF7_TagGenerator')) {
				// tag generator for CF7 >= v4.2
				$args = wp_parse_args( $args, array() );
				$desc = __('Generate a form-tag for a Dynamic Hidden field. For more details, see %s.');
				$desc_link = '<a href="https://wordpress.org/plugins/contact-form-7-simple-hidden-field/" target="_blank">'.__( 'Contact Form 7 - Simple Hidden Field').'</a>';
				?>
					<div class="control-box">
						<fieldset>
							<legend><?php echo sprintf(esc_html($desc), $desc_link); ?></legend>
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row">
											<label for="<?php 
													echo esc_attr($args['content'].'-name'); ?>"><?php 
													echo esc_html(__('Name', 'contact-form-7')); ?></label>
										</th>
										<td>
											<input type="text" name="name" class="tg-name oneline" id="<?php 
													echo esc_attr($args['content'].'-name' ); ?>" />
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="<?php 
													echo esc_attr($args['content'].'-values'); ?>"><?php 
													echo esc_html(__('Filter')); ?></label>
										</th>
										<td>
											<input type="text" name="values" class="tg-name oneline" id="<?php 
													echo esc_attr($args['content'].'-values' ); ?>" /><br />
													<?php 
														echo esc_html(__('You can enter any filter. Use single quotes only. 
														                  See docs &amp; examples.'));
													?>
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
					</div>
					<div class="insert-box">
						<input type="text" name="dynamichidden2" class="tag code" readonly="readonly" onfocus="this.select()" />
						<div class="submitbox">
							<input type="button" class="button button-primary insert-tag" value="<?php 
									echo esc_attr(__('Insert Tag', 'contact-form-7')); ?>" />
						</div>
					</div>
				<?php 
			} else {
				$type='dynamichidden2'
				?>
					<div id="wpcf7-tg-pane-<?php echo $type; ?>" class="control-box">
						<form action="">
							<table>
								<tr>
									<td>
										<?php echo esc_html(__('Name', 'contact-form-7')); ?><br />
										<input type="text" name="name" class="tg-name oneline" />
									</td>
									<td></td>
								</tr>
							</table>
							<table>
								<tr>
									<td>
										<?php echo esc_html(__('Filter')); ?><br />
											<input type="text" name="values" class="oneline" /><br />
											<?php echo esc_html(__('You can enter any filter. Use single quotes only. See docs &amp; examples.')); ?>
									</td>
								</tr>
							</table>
							<div class="tg-tag">
								<?php echo esc_html(__('Copy this code and paste it into the form left.', 'contact-form-7')); ?><br />
								<input type="text" name="<?php 
										echo $type; ?>" class="tag" readonly="readonly" onfocus="this.select()" style="width:100%;" />
							</div>
							<div class="tg-mail-tag">
								<?php echo esc_html(__('And, put this code into the Mail fields below.', 'contact-form-7')); ?><br />
								<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" style="width:100%;" />
							</div>
						</form>
					</div>
				<?php 
			}
		} // end public function dynamic_tg_pane
		
	} // end class wpcf7_simple_hidden_field
	
?>