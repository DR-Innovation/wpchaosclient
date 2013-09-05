<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */
$settings = array(
	array(
		/*Sections*/
		'name'		=> 'default',
		'title'		=> __('General Settings','wpchaosclient'),
		'fields'	=> array(
			/*Section fields*/
			array(
				'name' => 'wpchaos-servicepath',
				'title' => __('Service Path','wpchaosclient'),
				'type' => 'text'
			),
			array(
				'name' => 'wpchaos-clientguid',
				'title' => __('Client GUID','wpchaosclient'),
				'type' => 'text'
			),
			array(
				'name' => 'wpchaos-accesspoint-guid',
				'title' => __('Access Point GUID','wpchaosclient'),
				'type' => 'text'
			),
			array(
				'name' => 'wpchaos-email',
				'title' => __('E-mail used for authentication','wpchaosclient'),
				'type' => 'text'
			),
			array(
				'name' => 'wpchaos-password',
				'title' => __('Password','wpchaosclient'),
				'type' => 'password'
			)
		)
	)
);
//eol