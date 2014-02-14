<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */

/**
 * WordPress Widget that makes it possible to style
 * and display several data from a CHAOS object
 */
class WPChaosObjectMultiWidget extends WPChaosWidget {

	/**
	 * Regex pattern for attribute
	 */
	const PATTERN_ATTRIBUTE = "/\[(\w+)\]/";

	/**
	 * Regex pattern for template
	 */
	const PATTERN_TEMPLATE = "/\{(\w+)\}/";

	/**
	 * Prefix for templates to be used
	 */
	const TEMPLATE_PREFIX = 'multiattrwidget-';

	/**
	 * Constructor
	 */
	public function __construct() {
		
		parent::__construct(
			'chaos-object-multi-widget',
			__('CHAOS Object Multi Attributes','wpchaosclient'),
			array( 'description' => __('Style and display several data from a CHAOS object','wpchaosclient') )
		);

		$this->fields = array(
			array(
				'title' => __('Markup','wpchaosclient'),
				'name' => 'markup',
				'type' => 'textarea',
				'val' => '',
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
			
			//Find all occurences of [foo] in the markup and replace them
			//with a foo method on the current CHAOS object.
			$result = preg_replace_callback(self::PATTERN_ATTRIBUTE, 
				function($matches) {

					return WPChaosClient::get_object()->$matches[1];

				}, $instance['markup']);

			//Find all occurences of {foo} in the markup and replace them
			//with an inclusion of a chaos-object-foo.php template
			$result = preg_replace_callback(self::PATTERN_TEMPLATE, 
				function($matches) {

					// Buffering the output as this method is returning markup - not printing it.
					ob_start();
					//Look in theme dir and include if found
					locate_template('/templates/'.WPChaosObjectMultiWidget::TEMPLATE_PREFIX.$matches[1].'.php', true);		
					// Return the markup generated in the template and clean the output buffer.
					return ob_get_clean();

				}, $result);

			echo $result;

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

		//Check if used template exist in list
		$templates = $this->get_template_names();
		$matches;
		$markup = isset( $instance[ 'markup' ]) ? $instance[ 'markup' ] : $this->fields[0]['val'];
		preg_match_all(self::PATTERN_TEMPLATE,$markup,$matches);
		foreach($matches[1] as $template) {
			if(!in_array($template,$templates)) {
				printf('<div class="error"><p>'.__('Template "%s" not found for CHAOS Object Multi Attributes Widget','wpchaosclient').'</p></div>',WPChaosObjectMultiWidget::TEMPLATE_PREFIX.$template.'.php');	
			}
		}

		parent::form($instance);

		echo '<p>'.__('Allowed attributes:','wpchaosclient').'<br>';
		//List the attribute methods defined by WPChaosClient and wrap them with [].
		if(count(WPChaosClient::get_chaos_attributes()) > 0) {
			echo '['.implode('], [',array_keys(WPChaosClient::get_chaos_attributes())).']</p>';
		} else {
			echo __('None','wpchaosclient').'</p>';
		}
		
		echo '<p>'.__('Found template files:','wpchaosclient').'<br>';
		//List the templates files found in current theme and wrap them with {}.
		if(count($templates) > 0) {
			echo '{'. implode('}, {', $templates) .'}</p>';
		} else {
			echo __('None','wpchaosclient').'</p>';
		}
		
	}

	/**
	 * Get list of names of found templates
	 * @return array 
	 */
	private function get_template_names() {
		$templates = array();
		$matches;
		foreach(glob(get_stylesheet_directory().'/templates/'.self::TEMPLATE_PREFIX.'*.php') as $file) {
			preg_match('/'.self::TEMPLATE_PREFIX.'(\w*)\.php/',basename($file),$matches);
			$templates[] = $matches[1];
		}
		return $templates;
	}

}

//eol