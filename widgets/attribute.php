<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */

/**
 * WordPress Widget that makes it possible to style
 * and display one data attribute from a CHAOs object
 */
class WPChaosObjectAttrWidget extends WP_Widget {

	/**
	 * Fields in widget. Defines keys for values
	 * @var array
	 */
	private $fields;

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

		//Print each field based on its type
		foreach($this->fields as $field) {
			$value = isset( $instance[ $field['name'] ]) ? $instance[ $field['name'] ] : $field['val'];
			$name = $this->get_field_name( $field['name'] );
			$title = $field['title'];
			$id = $this->get_field_id( $field['name'] );

			echo '<p>';
			echo '<label for="'.$name.'">'.$title.'</label>';
			switch($field['type']) {
				case 'textarea':
					echo '<textarea class="widefat" name="'.$name.'" >'.$value.'</textarea>';
					break;
				case 'select':
					echo '<select class="widefat" name="'.$name.'">';
					foreach($field['list'] as $opt_key => $opt_value) {
						echo '<option value="'.$opt_key.'" '.selected( $value, $opt_key, false).'>'.$opt_value.'</option>';
					}
					echo '</select>';
					break;
				case 'text':
				default:
					echo '<input class="widefat" id="'.$id.'" name="'.$name.'" type="text" value="'.esc_attr( $value ).'" />';
			}
			echo '</p>';

		}
	}

	/**
	 * Callback for whenever the widget values should be saved
	 * 
	 * @param  array $new_instance New values from the form
	 * @param  array $old_instance Previously saved values
	 * @return array               Values to be saved
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		
		foreach($this->fields as $field) {
			$instance[$field['name']] = ( ! empty( $new_instance[$field['name']] ) ) ? $new_instance[$field['name']]  : $field['val'];
		}
		
		return $instance;
	}

}

//eol