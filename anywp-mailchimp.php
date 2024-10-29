<?php
/*
Plugin Name: AnyWP MailChimp
Plugin URI: http://anywp.com/plugins
Description: Sidebar widget and shortcode to add MailChimp newsletter sign up form.
Author: AnyWP
Version: 1.0
Author URI: http://anywp.com
*/

class AnyWP_MailChimp extends WP_Widget {
	
	function AnyWP_MailChimp() {
		$widget_ops = array('description' => __('Newsletter subscription form', 'AnyWP'));
		parent::WP_Widget(false, __('AnyWP MailChimp', 'AnyWP'), $widget_ops);
	}

	function form($instance) {
		$title = if_var_isset($instance['title'], '');
		$api_key = if_var_isset($instance['api_key'], '');
		$list_name = if_var_isset($instance['list_name'], '');
		$signup_message = if_var_isset($instance['signup_message'], 'Join our newsletter');
		$success_message = if_var_isset($instance['success_message'], 'Thank you for subscribing, please check your email for confirmation.');
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title:', 'AnyWP') . '</label><br /><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" /></p>';
		echo '<p><label for="' . $this->get_field_id('api_key') . '">' . __('API Key:', 'AnyWP') . '</label><br /><input class="widefat" id="' . $this->get_field_id('api_key') . '" name="' . $this->get_field_name('api_key') . '" type="text" value="' . esc_attr($api_key) . '" /></p>';
		echo '<p><label for="' . $this->get_field_id('list_name') . '">' . __('List Name:', 'AnyWP') . '</label><br /><input class="widefat" id="' . $this->get_field_id('list_name') . '" name="' . $this->get_field_name('list_name') . '" type="text" value="' . esc_attr($list_name) . '" /></p>';
		echo '<p><label for="' . $this->get_field_id('signup_message') . '">' . __('Signup Message:', 'AnyWP') . '</label><br /><input class="widefat" id="' . $this->get_field_id('signup_message') . '" name="' . $this->get_field_name('signup_message') . '" type="text" value="' . esc_attr($signup_message) . '" /></p>';
		echo '<p><label for="' . $this->get_field_id('success_message') . '">' . __('Success Message:', 'AnyWP') . '</label><br /><input class="widefat" id="' . $this->get_field_id('success_message') . '" name="' . $this->get_field_name('success_message') . '" type="text" value="' . esc_attr($success_message) . '" /></p>';
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['api_key'] = $new_instance['api_key'];
		$instance['list_name'] = $new_instance['list_name'];
		$instance['signup_message'] = $new_instance['signup_message'];
		$instance['success_message'] = $new_instance['success_message'];
		return $instance;
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		echo $before_widget;
		$title = if_var_isset($instance['title'], '');
		if ($title != '') echo $before_title . $title . $after_title;
		$sc = '[anywp_mailchimp_form ';
		$sc .= 'api_key="' . if_var_isset($instance['api_key'], '') . '" ';
		$sc .= 'list_name="' . if_var_isset($instance['list_name'], '') . '" ';
		$sc .= 'signup_message="' . if_var_isset($instance['signup_message'], '') . '" ';
		$sc .= 'success_message="' . if_var_isset($instance['success_message'], '') . '" ';
		$sc .= ']';
		echo do_shortcode($sc);
		echo $after_widget;
	}
}

if (! function_exists('if_var_isset')) {
	function if_var_isset(&$check, $or = null) {
		return (isset($check) ? $check : $or);
	}
}

function anywp_mailchimp_newsletter($atts, $content = null) {
	$option = get_option('widget_anywp_mailchimp');
	if (! $option) return '';
	$key = array_keys($option);
	$result = '';
	$result .= '<div id="mailchimp-newsletter">';
	$result .= '<form class="form-horizontal" method="post" action="' . admin_url('admin-ajax.php') . '" id="newsletter-form" data-newslettertype="file">';
	$result .= $option[$key[0]]['signup_message'] . ' ';
	$result .= '<input onclick="if (this.value == \'Enter your email...\') this.value = \'\';" type="text" id="newsletter-email" name="newsletter-email" value="Enter your email..." /> ';
	$result .= '<input id="newsletter-submit" value="Subscribe" type="button" />';
	$result .= '</form>';
	$result .= '</div>';
	$result .= '<script type="text/javascript">var success_message = "' . $option[$key[0]]['success_message'] . '"</script>';
	return $result;
}

function anywp_ajax_newsletter() {
	require_once(plugin_dir_path(__FILE__) . 'mailchimp-api.php');
}

function anywp_mailchimp_widget() {
	register_widget('AnyWP_MailChimp');
}

function anywp_newsletter_enqueue_scripts() {
	if (! is_admin()) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('js_jquery_form', plugin_dir_url(__FILE__) . 'jquery.form.js', array('jquery'), false, false);
		wp_enqueue_script('js_newsletter', plugin_dir_url(__FILE__) . 'anywp-mailchimp.js', array('jquery'), false, false);
		wp_enqueue_style('css_newsletter', plugin_dir_url(__FILE__) . 'anywp-mailchimp.css', array(), false);
	}
}

add_action('widgets_init', 'anywp_mailchimp_widget');
add_action('wp_ajax_newsletter', 'anywp_ajax_newsletter');
add_action('wp_ajax_nopriv_newsletter', 'anywp_ajax_newsletter');
add_action('wp_enqueue_scripts', 'anywp_newsletter_enqueue_scripts');
add_shortcode('anywp_mailchimp_form', 'anywp_mailchimp_newsletter');

?>
