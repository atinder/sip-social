<?php

/**
 * Sip Widget Class
 * @author Atinder <shopitpress.com>
 * @link http://shopitpress.com
 * @example sip-widget.php
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if(!class_exists('SipWidget')){

	abstract class SipWidget extends WP_Widget{

		/***********************************************************
	    **  Abstract Functions
	    ***********************************************************/

		/** Return the widget id */
		abstract function w_id();
		/** Return the widget name */
		abstract function w_name();
		/** Return the form content to show in admin dashboard */
    	abstract function w_form($instance);
	    /** Return the real widget content */
	    abstract function w_content($instance);
	    /** Return the plugin default options, a name=>value array, ie: array('title'=>'Sip Widget Title') */
	    abstract function w_defaults();

	    /***********************************************************
	    **  Static Functions
	    ***********************************************************/

	    /** Register the Widget using Wordpress Actions */
	    public static function w_init($classname=null){
	        if (is_null($classname)){
	            $classname=get_called_class();
	        }
	        add_action( 'widgets_init', create_function('', 'register_widget("'.$classname.'");') );
	    }

	    /***********************************************************
	    **  Wordpress Hooks
	    ***********************************************************/
		/**
		 * Register widget with WordPress.
		 */
		public function SipWidget() {

			parent::__construct(
		 		$this->w_id(), // Base ID
				$this->w_name(), // Name
				$this->w_defaults(),
				array('width' => 250)
				);

		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;
	        echo '<div class="'.$this->id_base.'-wrapper">'.$this->w_content($instance).'</div>';
			echo $after_widget;

		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
	        $new_instance = $this->parse_args( $new_instance );
	        $def=$this->w_defaults();
	        foreach($new_instance as $k=>$v){
	            $instance[$k] = strip_tags($v);
	            if (empty($instance[$k]) && !empty($def[$k])){
	                $instance[$k]=$def[$k];
	            }
	        }
	        return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

			$instance = $this->parse_args( $instance );
        	echo $this->w_form($instance);

		}

		/***********************************************************
	    **  Utility Functions
	    ***********************************************************/

	    /** Return an input type=text field ready for admin dashboard */
	    function w_form_input($instance, $name, $title=null){
	        if (is_null($title)) { $title=ucwords($name); }
	        return  '<label for="'.$this->get_field_id($name).'">'.__($title).'</label>'.
	                '<input class="widefat" type="text" id="'.$this->get_field_id($name).'" name="'.$this->get_field_name($name).'" value="'.esc_attr($instance[$name]).'"/>';
	    }    

	    /** Return a textarea ready for admin dashboard */
	    function w_form_textarea($instance, $name, $title=null){
	        if (is_null($title)) { $title=ucwords($name); }
	        return  '<label for="'.$this->get_field_id($name).'">'.__($title).'</label>'.
	                '<textarea class="widefat" id="'.$this->get_field_id($name).'" name="'.$this->get_field_name($name).'">'.esc_attr($instance[$name]).'</textarea>';
	    }

	    /** Return an select field ready for admin dashboard */
	    function w_form_select($instance, $name,$options, $title=null){
	        if (is_null($title)) { $title=ucwords($name); }
	        $output = sprintf('<label for="%s">%s</label>',$this->get_field_id($name),__($title));
	        $output .= sprintf('<select class="widefat"  name="%s">',$this->get_field_name($name));
	        foreach ($options as $option) {
	        	$output .= sprintf('<option value="%s" %s>%s</option>',esc_attr($option),selected($instance[$name],esc_attr($option),false),esc_attr($option));
	        }
	        $output .= sprintf('<select>');
	        return  $output;
	    } 

	    /** Return an radio field ready for admin dashboard */
	    function w_form_radio($instance, $name,$options, $title=null){
	        if (is_null($title)) { $title=ucwords($name); }
	        $output = sprintf('<label for="%s">%s</label>',$this->get_field_id($name),__($title));
	        foreach ($options as $option) {
	        $output .= sprintf('<input type="radio" name="%s" value="%s" %s>',$this->get_field_name($name),$option,checked($instance[$name],$option,false));
	       	$output .= sprintf('<label for="%s">%s</label>',$option,$option);
	       	}
	        return  $output;
	    } 

	    /** Return a textarea ready for admin dashboard *
	    function w_form_checkbox($instance, $name, $title=null){
	    	//$check = isset($instance[$name]) ? '1' : '0';
	    	
	        if (is_null($title)) { $title=ucwords($name); }
	        return  '<label for="'.$this->get_field_id($name).'">'.__($title).'</label>'.
	                '<input type="checkbox" id="'.$this->get_field_id($name).'" name="'.$this->get_field_name($name).'" value="1"' . checked('1',$instance[$name],false)  . '/>';
	    }
		*/
	    /** Simplify wp_parse_args */
	    function parse_args($instance){
	        return wp_parse_args( (array)$instance, $this->w_defaults());
	    }
	}

}