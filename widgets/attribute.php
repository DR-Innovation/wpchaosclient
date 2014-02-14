<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */

/**
 * WordPress Widget that makes it possible to style
 * and display one data attribute from a CHAOs object
 */
class WPChaosObjectAttrWidget extends WPChaosWidget {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		parent::__construct(
			'chaos-object-attribute-widget',
			__('CHAOS Object Attribute','wpchaosclient'),
			array( 'description' => __('Style and display data from a CHAOS object','wpchaosclient') )
		);

		$this->fields = array(
			array(
				'title' => __('Attribute','wpchaosclient'),
				'name' => 'attribute',
				'type' => 'select',
				'list' => array(),
				'val' => '',
			),
			array(
				'title' => __('Markup','wpchaosclient'),
				'name' => 'markup',
				'type' => 'textarea',
				'val' => '%s',
			)
		);
	}

	/**
	 * GUI for widget content
	 * 
	 * @param  array $args Sidebar arguments
	 * @param  array $instance Widget values from database
	 * @return void 
	 */
	public function widget( $args, $instance ) {
		if(WPChaosClient::get_object()) {
			echo $args['before_widget'];
			printf($instance['markup'], WPChaosClient::get_object()->$instance['attribute']);
			echo $args['after_widget'];
		}
	}

	/**
	 * GUI for widget form in the administration
	 * 
	 * @param  array $instance Widget values from database
	 * @return void           
	 */
	public function form( $instance ) {

		// Populate list of allowed attributes (added filters)
		$this->fields[0]['list'] = WPChaosClient::get_chaos_attributes();

		//Set attribute as title of widget for better UX
		$title = isset( $instance[ 'attribute' ]) ? ucfirst($instance['attribute']) : "";
		echo '<input type="hidden" id="'.$this->get_field_id('title').'" value="'.$title.'">';

		parent::form( $instance );
	}

}

//eol