<?php 

	/*
		Plugin Name: Contact Form 7 - Simple Hidden Fields
		Plugin URI: https://github.com/Hube2/contact-form-7-simple-hidden-field
		Description: Simple Hidden Fields for Contact Form 7. Requires contact form 7
		Version: 1.1.3
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
			if(function_exists('wpcf7_add_shortcode')){
				/* Shortcode handler */		
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
			if (!is_array($tag)) {
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
			$values = $tag['values'];
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
			if (!is_array($tag)) {
				return '';
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
			$callback = array($this, 'simple_tg_pane_');
			wpcf7_add_tag_generator($name, $title, $elm_id, $callback);
			$name = 'dynamichidden2';
			$title = __('Dynamic Hidden Field', 'wpcf7');
			$elm_id = 'wpcf7-tg-pane-dynamichidden2';
			$callback = array($this, 'dynamic_tg_pane_');
			wpcf7_add_tag_generator($name, $title, $elm_id, $callback);
		} // end public function add_tag_generator
		
		public function simple_tg_pane_($form) {
			// not sure I understand why this is the callback and then we call the tag generator
			// I believe it is to dump the whatever is in $form
			$this->simple_tg_pane('simplehidden');
		} // end public function simple_tg_pane_
		
		public function simple_tg_pane($type='simplehidden') {
			// output the code for CF7 tag generator
			?>
				<div id="wpcf7-tg-pane-<?php echo $type; ?>" class="hidden">
					<form action="">
						<table>
							<tr>
								<td>
									<?php echo esc_html(__('Name', 'wpcf7')); ?><br />
									<input type="text" name="name" class="tg-name oneline" />
								</td>
								<td></td>
							</tr>
						</table>
						<table>
							<tr>
								<td>
									<?php echo esc_html(__('Value', 'wpcf7')); ?><br />
										<input type="text" name="values" class="oneline" />
										<?php echo esc_html(__('Enter the value for the hidden field', 'wpcf7')); ?>
								</td>
							</tr>
						</table>
						<div class="tg-tag">
							<?php echo esc_html(__('Copy this code and paste it into the form left.', 'wpcf7')); ?><br />
							<input type="text" name="<?php 
									echo $type; ?>" class="tag" readonly="readonly" onfocus="this.select()" />
						</div>
						<div class="tg-mail-tag">
							<?php echo esc_html(__('And, put this code into the Mail fields below.', 'wpcf7')); ?><br />
							<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" />
						</div>
					</form>
				</div>
			<?php 
		} // end public function simple_tg_pane
		
		public function dynamic_tg_pane_($form) {
			// not sure I understand why this is the callback and then we call the tag generator
			// I believe it is to dump the whatever is in $form
			$this->dynamic_tg_pane('dynamichidden2');
		} // end public function dynamic_tg_pane_
		
		public function dynamic_tg_pane($type='dynamichidden2') {
			// output the code for CF7 tag generator
			?>
				<div id="wpcf7-tg-pane-<?php echo $type; ?>" class="hidden">
					<form action="">
						<table>
							<tr>
								<td>
									<?php echo esc_html(__('Name', 'wpcf7')); ?><br />
									<input type="text" name="name" class="tg-name oneline" />
								</td>
								<td></td>
							</tr>
						</table>
						<table>
							<tr>
								<td>
									<?php echo esc_html(__('Filter', 'wpcf7')); ?><br />
										<input type="text" name="values" class="oneline" />
										<?php echo esc_html(__('You can enter any filter. Use single quotes only. See docs &amp; examples.', 'wpcf7')); ?>
								</td>
							</tr>
						</table>
						<div class="tg-tag">
							<?php echo esc_html(__('Copy this code and paste it into the form left.', 'wpcf7')); ?><br />
							<input type="text" name="<?php 
									echo $type; ?>" class="tag" readonly="readonly" onfocus="this.select()" />
						</div>
						<div class="tg-mail-tag">
							<?php echo esc_html(__('And, put this code into the Mail fields below.', 'wpcf7')); ?><br />
							<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" />
						</div>
					</form>
				</div>
			<?php 
		} // end public function dynamic_tg_pane
		
	} // end class wpcf7_simple_hidden_field
	
?>