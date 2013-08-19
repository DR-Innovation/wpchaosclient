<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */

use CHAOS\Portal\Client\PortalClient;

class CHAOSException extends \RuntimeException {}

/**
 * WordPress Portal Client that automatically
 * retrieves and sets the accessPointGUID
 * stored in the database
 */
class WPPortalClient extends PortalClient {
	
	/**
	 * How long time is a CHAOS session timeout?
	 * @var string Will be appended a '-' and used as argument for a call to the strtotime function.
	 */
	const SESSION_TIMEOUT = '18 minutes';
	const WP_CHAOS_CLIENT_SESSION_UPDATED_KEY = 'wpchaosclient-session-updated';
	const WP_CHAOS_CLIENT_SESSION_GUID_KEY = 'wpchaosclient-session-guid';
	
	public function __construct($servicePath, $clientGUID) {
		// Make sure that the constructor is called without the session getting autocreated.
		parent::__construct($servicePath, $clientGUID, false);
	}
	
	/**
	 * This field holds the accumulated response time in seconds.
	 * @var integer
	 */
	protected $accumulatedResponseTime = 0;
	
	public function getAccumulatedResponseTime() {
		return $this->accumulatedResponseTime;
	}

	public function CallService($path, $method, array $parameters = null, $requiresSession = true) {
		if((!isset($parameters['accessPointGUID']) || $parameters['accessPointGUID'] == null) && get_option('wpchaos-accesspoint-guid')) {
			$parameters['accessPointGUID'] = get_option('wpchaos-accesspoint-guid');
		}
		
		if(array_key_exists('query', $parameters) && $parameters['query'] != "") {
			$query = array('(' . $parameters['query'] . ')');
		} else {
			$query = array();
		}
		$parameters['query'] = implode("+AND+", array_merge($query, $this->global_constraints));
		
		$beforeCall = microtime(true);
		$response = parent::CallService($path, $method, $parameters, $requiresSession);
		$this->accumulatedResponseTime += microtime(true) - $beforeCall;

		do_action('wpportalclient-service-call-returned', array(
			'path' => $path,
			'method' => $method,
			'parameters' => $parameters,
			'response' => $response
		));
		
		// Errors should throw exceptions.
		if(!$response->WasSuccess()) {
			throw new \CHAOSException($response->Error()->Message());
		} elseif($response->Portal() != null && !$response->Portal()->WasSuccess()) {
			throw new \CHAOSException($response->Portal()->Error()->Message());
		} elseif($response->Statistics() != null && !$response->Statistics()->WasSuccess()) {
			throw new \CHAOSException($response->Statistics()->Error()->Message());
		} elseif($response->EmailPassword() != null && !$response->EmailPassword()->WasSuccess()) {
			throw new \CHAOSException($response->EmailPassword()->Error()->Message());
		} elseif($response->MCM() != null && !$response->MCM()->WasSuccess()) {
			throw new \CHAOSException($response->MCM()->Error()->Message());
		} elseif($response->SecureCookie() != null && !$response->SecureCookie()->WasSuccess()) {
			throw new \CHAOSException($response->SecureCookie()->Error()->Message());
		}  elseif($response->Upload() != null && !$response->Upload()->WasSuccess()) {
			throw new \CHAOSException($response->Upload()->Error()->Message());
		} else {
			return $response;
		}
	}
	
	public function SessionGUID() {
		if(parent::SessionGUID() == null) {
			// Get the session stored in the database.
			$sessionGUID = get_option(self::WP_CHAOS_CLIENT_SESSION_GUID_KEY, null);
			if($sessionGUID != null) {
				parent::SetSessionGUID($sessionGUID, true);
			} else {
				// The client has no session and the database has no session either.
				// This forces us to create one.
				$this->Session()->Create();
				$this->authenticateSession();
				// Save this session GUID in the database.
				update_option(self::WP_CHAOS_CLIENT_SESSION_GUID_KEY, parent::SessionGUID());
				update_option(self::WP_CHAOS_CLIENT_SESSION_UPDATED_KEY, time());
			}
		}
		
		// Keep the session alive.
		$lastSessionUpdate = get_option(self::WP_CHAOS_CLIENT_SESSION_UPDATED_KEY, null);
		$timeoutTime = strtotime('-'.self::SESSION_TIMEOUT);
		
		if($lastSessionUpdate < $timeoutTime) {
			// The chaos session should be updated.
			// We have to do this to prevent endless recursion.
			update_option(self::WP_CHAOS_CLIENT_SESSION_UPDATED_KEY, time());
			
			try {
				$response = $this->Session()->Update();
			} catch(\Exception $e) {
				error_log("CHAOS Session expired, but couldn't update it: " . $e->getMessage());
				
				// Reset sessions.
				// Try again - this time forgetting any session.
				return $this->resetSession();
			}
		}
		
		return parent::SessionGUID();
	}
	
	protected function authenticateSession() {
		if(get_option('wpchaos-email') && get_option('wpchaos-password')) {
			$this->EmailPassword()->Login(get_option('wpchaos-email'), get_option('wpchaos-password'));
		} else {
			throw new \CHAOSException("Either the email or password was not set.");
		}
	}
	
	public function resetSession() {
		parent::SetSessionGUID(null, false);
		update_option(self::WP_CHAOS_CLIENT_SESSION_GUID_KEY, null);
		return $this->SessionGUID();
	}
	
	protected $global_constraints = array();
	public function addGlobalConstraint($constraint) {
		$this->global_constraints[] = '(' . $constraint . ')';
	}

}

//eol