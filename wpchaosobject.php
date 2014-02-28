<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */

/**
 *
 * Class for CHAOS material
 * 
 * @property-read string $title 		Get title
 * @property-read string $organisation 	Get name of organisation
 * @property-read string $thumbnail     Get url to thumbnail
 * @property-read string $type 			Get type
 * @property-read int 	 $views 		Get number of views
 * @property-read int 	 $likes 		Get number of likes
 * @property-read string $created_date  Get date of creation (XMLDateTime)
 * @property-read array  $tags 			Get list of tags
 * @property-read string $slug			Get the slug (generated from title).
 * @property-read string $url			Get the url on which this object is viewable.
 * @property-read string $caption		Get the caption to display ontop of a thumbnail.
 * @property-read mixed  $var
 */
class WPChaosObject extends \CHAOS\Portal\Client\Data\Object {

	/**
	 * Filter prefix for this object
	 * Default is WPChaosClient::OBJECT_FILTER_PREFIX
	 * Use in: add_filter($object_filter_prefix.<name>,<callback>);
	 * @var string
	 */
	private $object_filter_prefix;

	/**
	 * Variables created by WP filters are cached
	 * @var array
	 */
	private $variable_cache = array();
	
	const CHAOS_OBJECT_CONSTRUCTION_ACTION = 'chaos-object-constrution';

	/**
	 * Constructor
	 * 
	 * @param \CHAOS\Portal\Client\Data\Object $chaos_object
	 * @param string $prefix 
	 */
	public function __construct(\stdClass $chaos_object, $prefix = WPChaosClient::OBJECT_FILTER_PREFIX) {
		parent::__construct($chaos_object);
		$this->object_filter_prefix = $prefix;
		do_action(self::CHAOS_OBJECT_CONSTRUCTION_ACTION, $this);
	}

	/**
	 * Magic getter for various metadata in CHAOS object
	 * Use like $class->$name
	 * Add filters like add_filter('wpchaos-object-'.$name,callback,priority,2)
	 * 
	 * @param  string $name Variable to get
	 * @return mixed 		Filtered data (from $chaos_object)
	 */
	public function __get($name) {

		//Check if variable exist in cache and return
		if(isset($this->variable_cache[$name])) {
			return $this->variable_cache[$name];
		//Check if a WP filter exist for variable, populate cache and return
		} else if(array_key_exists($this->object_filter_prefix.$name, $GLOBALS['wp_filter'])) {
			// throw new RuntimeException("There are no filters for this variable: $".$name);
			return $this->variable_cache[$name] = apply_filters($this->object_filter_prefix.$name, "", $this);
		//Fallback to parent getter
		} else {
			return parent::__get($name);
		}
	}
	
	public function clear_cache($name = null) {
		if($name) {
			unset($this->variable_cache[$name]);
		} else {
			$this->variable_cache = array();
		}
	}
	
	/**
	 * Takes an Object/Get response from the CHAOS service and wraps every object in a WPChaosObject.
	 * @param \CHAOS\Portal\Client\Data\ServiceResult $response The CHAOS response on an Object/Get request.
	 * @param string $prefix
	 * @return WPChaosObject[] An array of WPChaosObjects.
	 */
	public static function parseResponse(\CHAOS\Portal\Client\Data\ServiceResult $response, $prefix = WPChaosClient::OBJECT_FILTER_PREFIX) {
		$result = array();
		foreach($response->MCM()->Results() as $object) {
			$result[] = new WPChaosObject($object,$prefix);
		}
		return $result;
	}

	public function increment_metadata_field($metadata_schema_guid, $metadata_language, $xpath, $value, $fields_invalidated = array()) {
		$metadata = $this->get_metadata($metadata_schema_guid);
		$element = $metadata->xpath($xpath);
		if($element !== false && count($element) == 1) {
			$DOMElement = dom_import_simplexml($element[0]);
			$DOMElement->nodeValue = intval($DOMElement->nodeValue) + 1;
			$revisionID = $this->get_metadata_revision_id($metadata_schema_guid);
			$this->set_metadata(WPChaosClient::instance(), $metadata_schema_guid, $metadata, $metadata_language, $revisionID);
			foreach($fields_invalidated as $field) {
				$this->clear_cache($field);
			}
		} else {
			throw new \RuntimeException("The element found using the provided xpath expression, wasn't exactly a single.");
		}
	}
	
	public function set_metadata_field($metadata_schema_guid, $metadata_language, $xpath, $value, $fields_invalidated = array()) {
		$metadata = $this->get_metadata($metadata_schema_guid);
		$element = $metadata->xpath($xpath);
		if($element !== false && count($element) == 1) {
			$DOMElement = dom_import_simplexml($element[0]);
			$DOMElement->nodeValue = $value;
			$revisionID = $this->get_metadata_revision_id($metadata_schema_guid);
			$this->set_metadata(WPChaosClient::instance(), $metadata_schema_guid, $metadata, $metadata_language, $revisionID);
			foreach($fields_invalidated as $field) {
				$this->clear_cache($field);
			}
		} else {
			throw new \RuntimeException("The element found using the provided xpath expression, wasn't exactly a single.");
		}
	}

	/**
	 * Override parent refresh
	 * Fetches an updated version of the CHAOS object from the webservice and invalidates all caches.
	 * Call construction action again because the object has been refreshed
	 * @param \CHAOS\Portal\Client\PortalClient $client The PortalCliet to use when communicating with CHAOS.
	 */	
	public function refresh(\CHAOS\Portal\Client\PortalClient $client) {
		parent::refresh($client);
		do_action(self::CHAOS_OBJECT_CONSTRUCTION_ACTION, $this);
	}


}

//eol