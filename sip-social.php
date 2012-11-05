<?php
/*
Plugin Name: Sip Social Demo
Plugin URI: http://shopitpress.com
Author: atinder
Version: 1.0
*/

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

require_once dirname(__FILE__) . '/class.sip.social.php';

class SipSocialDemo{

	private $settings;

	public function __construct(){
		add_action('wp_loaded', array($this , 'config'));
	}

	public function config(){

		$root_path = plugins_url('' , __FILE__);

		$this->settings =get_option('sip_settings');

		new SipSocial($this->settings,$root_path);

	}
	
}

new SipSocialDemo();