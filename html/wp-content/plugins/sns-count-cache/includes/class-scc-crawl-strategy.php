<?php
/**
class-scc-crawl-strategy.php

Description: This class is abstract class of a data crawler
Author: Daisuke Maruyama
Author URI: https://logicore.cc/
License: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
Copyright (C) 2014 - 2019 Daisuke Maruyama

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * SCC_Crawl_Strategy
 */
abstract class SCC_Crawl_Strategy {

	/**
	 * URL for request
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * HTTP method for request
	 *
	 * @var string
	 */
	protected $http_method = null;

	/**
	 * Parameters for request
	 *
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * Query parameters for request
	 *
	 * @var array
	 */
	protected $query_parameters = array();

	/**
	 * Instance
	 *
	 * @var array
	 */
	private static $instance = array();

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		// $this->get_object_id();
	}

	/**
	 * Get instance
	 *
	 * @return SCC_Crawl_Strategy
	 */
	public static function get_instance() {
		$class_name = get_called_class();

		if ( ! isset( self::$instance[ $class_name ] ) ) {
			self::$instance[ $class_name ] = new $class_name();
		}

		return self::$instance[ $class_name ];
	}

	/**
	 * Return object ID
	 *
	 * @return string
	 */
	public function get_object_id() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$object_id = spl_object_hash( $this );

		SCC_Logger::log( '[' . __METHOD__ . '] object ID: ' . $object_id );

		return $object_id;
	}

	/**
	 * Inhibit clone
	 *
	 * @return void
	 * @throws Exception Clone is not allowed against.
	 */
	final public function __clone() {
		throw new Exception( 'Clone is not allowed against' . get_class( $this ) );
	}

	/**
	 * Initialize
	 *
	 * @param array $options Option.
	 * @return void
	 */
	abstract public function initialize( $options = array() );

	/**
	 * Get URL
	 *
	 * @return string
	 */
	public function get_url() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		return $url;
	}

	/**
	 * Set URL
	 *
	 * @param string $url URL.
	 * @return void
	 */
	public function set_url( $url ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		$this->url = $url;
	}

	/**
	 * Get parameters
	 *
	 * @return array
	 */
	public function get_parameters() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		return $this->parameters;
	}

	/**
	 * Get parameter
	 *
	 * @param string $key Key of parameters.
	 * @return string
	 */
	public function get_parameter( $key ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		return $this->parameters[ $key ];
	}

	/**
	 * Set parameters
	 *
	 * @param array $parameters Parameters.
	 * @return void
	 */
	public function set_parameters( $parameters = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		foreach ( $parameters as $key => $value ) {
			$this->set_parameter( $key, $value );
		}
	}

	/**
	 * Set parameter
	 *
	 * @param string $key Key.
	 * @param string $value Value.
	 * @return void
	 */
	public function set_parameter( $key, $value ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		$this->parameters[ $key ] = $value;
	}

	/**
	 * Get HTTP method
	 *
	 * @return string
	 */
	public function get_http_method() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		return $this->http_method;
	}

	/**
	 * Set HTTP method
	 *
	 * @param string $http_method HTTP method.
	 * @return void
	 */
	public function set_http_method( $http_method ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		$this->http_method = $http_method;
	}

	/**
	 * Build request URL
	 *
	 * @return void
	 */
	abstract public function build_request_url();


	/**
	 * Extract response body
	 *
	 * @param string $content Response data,
	 * @return array
	 */
	abstract public function extract_response_body( $content );

	/**
	 * Extract response header
	 *
	 * @param string $content Response data,
	 * @return array
	 */
	abstract public function extract_response_header( $content );

	/**
	 * Build request header
	 *
	 * @return array
	 */
	abstract public function build_request_header();

	/**
	 * Build request body
	 *
	 * @return array
	 */
	abstract public function build_request_body();

	/**
	 * Check configuration
	 *
	 * @return array
	 */
	abstract public function check_configuration();

	/**
	 * Check rate limiting
	 *
	 * @param string $content Response data,
	 * @return void
	 */
	abstract public function check_rate_limiting( $content );

}
