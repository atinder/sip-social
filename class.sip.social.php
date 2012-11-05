<?php

/**
 * Sip Social Class
 * @author Atinder <shopitpress.com>
 * @link http://shopitpress.com
 * @example sip-social.php
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if(!class_exists('SipWidget')){
require_once dirname(__FILE__) . '/classes/abstract.class.sip.widget.php';
}
if(!class_exists('SipSocial')){

	class SipSocial{

		private $services;
		private $settings;
		private $root_path;

		public function __construct($settings,$root_path){

			$this->services = array(
				'facebook',
				'twitter',
				'google_plus',
				'youtube'
				);
			$this->settings =  array_filter(array_intersect_key($settings, array_flip($this->services)));

			$defaults = array(
				'social_size' => '32',
				'social_target' => 'same_window'
				);
			$this->config = array_intersect_key($settings, $defaults);
			$this->root_path = $root_path;
			add_shortcode('sip_social', array(&$this, 'shortcode'));
        	add_action('widgets_init', create_function('', 'register_widget("SipSocial_Widget");'));

		}

		public function shortcode( $atts ){
			extract( shortcode_atts( array(
				'size' => '',
				'services' => ''
			), $atts ) );

			$sc_services = array();
			if($services) $sc_services = explode(',', str_replace(' ', '', esc_attr($services)));
			return $this->doSocial(esc_attr($size), $sc_services);
		}
		
		public function doSocial( $size = '', $sc_services = array() ){

			if(isset($size) && $size != '') $this->config['social_size'] = $size;
			
			$output = '<div class="sip-social size-'. $this->config['social_size'] .'">';
			
			if(empty($sc_services)){
				foreach($this->services as $service){
					if(isset($this->settings[$service]) && $this->settings[$service] != ''){
						$target = ($this->config['social_target'] != 'same_window') ? ' target="_blank"' : '';
						$output .= sprintf('<a href="%s" class="%s" %s>',$this->settings[$service],$service, $target);
						$src = $this->root_path . '/img/' . $this->config['social_size'] . '/' . $service . '.png';
						$output .= sprintf('<img src="%s" alt="%s" />',$src,$service);
						$output .=	'</a> ';
					}
				}
			} else {
				foreach($sc_services as $service){
					if(isset($this->settings[$service]) && $this->settings[$service]){
						$target = ($this->config['social_target'] != 'same_window') ? ' target="_blank"' : '';
						$output .= sprintf('<a href="%s" class="%s" %s>',$this->settings[$service],$service, $target);
						$src = $this->root_path . '/img/' . $this->config['social_size'] . '/' . $service . '.png';
						$output .= sprintf('<img src="%s" alt="%s" />',$src,$service);
						$output .=	'</a> ';
					}
				}
			}
			
			$output .= '</div>';
			return $output;
		}
		
	}

}

class SipSocialWidget extends SipWidget{

	public function w_id(){ 
		return 'sip-social-wdget';
	}

	public function w_name(){
		return	'Sip Social Widget';
	}

    /** Return the dashboard admin form */
    public function w_form($instance){
        $w_form = '<p>'.$this->w_form_input($instance, 'title').'</p>';
        $w_form .= '<p>' . $this->w_form_select($instance, 'select' , array('one','two'));
        $w_form .= '<p>' . $this->w_form_radio($instance, 'radio' , array('one','two'));
        return $w_form;
    }

    /** Return the real widget content */
    public function w_content($instance){
        $w_content = SipSocial::doSocial($size = '', $sc_services = array());
        return $w_content;
    }    

    /** Widget Default Options, abstract */
    public function w_defaults(){
        return array(
            'title' => __($this->w_name()),
            'check' => '0',
            'check2' => '1'
        );
    }

}
SipSocialWidget::w_init();