<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */

/**
 * WordPress Widget that acts as base for
 * CHAOS related widgets by providing
 * form fields and update mechanism
 */
class WPChaosWidget extends WP_Widget {

	/**
	 * Fields in widget. Defines keys for values
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Constructor
	 */
	public function __construct($id_base, $name, $widget_options = array(), $control_options = array()) {
		
		parent::__construct( $id_base, $name, $widget_options, $control_options );

		/*$this->fields = array(
			array(
				'title' => 'string',
				'name' => 'string',
				'type' => 'checkbox|text|textarea|select',
				'list' => 'callback|array',
				'val' => 'string',
			),
		);*/

	}

	/**
	 * GUI for widget content
	 *
	 * @author Joachim Jensen <jv@intox.dk>
	 * @param  array $args Sidebar arguments
	 * @param  array $instance Widget values from database
	 * @return void 
	 */
	public function widget( $args, $instance ) {
		die('function WPChaosWidget::widget() must be over-ridden in a sub-class.');
	}

	/**
	 * Base GUI for widget form in the administration
	 *
	 * @author Joachim Jensen <jv@intox.dk>
	 * @param  array $instance Widget values from database
	 * @return void           
	 */
	public function form( $instance ) {

		//Print each field based on its type
		foreach($this->fields as $field) {
			$default_value = isset($field['val']) ? $field['val'] : '';
			$value = isset( $instance[ $field['name'] ]) ? $instance[ $field['name'] ] : $default_value;
			$name = $this->get_field_name( $field['name'] );
			$title = $field['title'];
			$id = $this->get_field_id( $field['name'] );
			$type = isset($field['type']) ? $field['type'] : 'text';

			//Populate list with callback
			if(isset($field['list']) && is_array($field['list']) && is_callable($field['list'])) {
				$field['list'] = call_user_func($field['list']);
			}

			echo '<p>';
			echo '<label for="'.$name.'">'.$title.'</label>';
			switch($type) {
				case 'textarea':
					echo '<textarea class="widefat" rows="16" cols="20" name="'.$name.'" >'.$value.'</textarea>';
					break;
				case 'select':
					echo '<select class="widefat" name="'.$name.'">';
					foreach((array)$field['list'] as $opt_key => $opt_value) {
						echo '<option value="'.$opt_key.'" '.selected( $value, $opt_key, false).'>'.$opt_value.'</option>';
					}
					echo '</select>';
					break;
				case 'checkbox':
					echo '<p><input type="checkbox" name="'.$name.'" value="1" '.checked( (int)$value, true, false).'></p>';
					break;
				case 'checkbox-multi':
					foreach((array)$field['list'] as $opt_key => $opt_value) {
						echo '<p><input type="checkbox" name="'.$name.'[]" value="'.$opt_key.'" '.checked( in_array($opt_key,(array)$value), true, false).'> '.$opt_value.'</p>';
					}
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
	 * @author Joachim Jensen <jv@intox.dk>
	 * @param  array $new_instance New values from the form
	 * @param  array $old_instance Previously saved values
	 * @return array               Values to be saved
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		
		foreach($this->fields as $field) {
			$default_value = isset($field['val']) ? $field['val'] : '';
			$instance[$field['name']] = ( ! empty( $new_instance[$field['name']] ) ) ? $new_instance[$field['name']]  : $default_value;
		}
		
		return $instance;
	}

}

//eol