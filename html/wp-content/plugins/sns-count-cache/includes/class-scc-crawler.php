<?php
/**
class-scc-crawler.php

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
 * SCC_Crawler
 */
abstract class SCC_Crawler {

	/**
	 * Target URL
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * Method to crawl
	 *
	 * @var string
	 */
	protected $crawl_method = 1;

	/**
	 * Timeout
	 *
	 * @var integer
	 */
	protected $timeout = 10;

	/**
	 * Ssl verification
	 *
	 * @var boolean
	 */
	protected $ssl_verification = true;

	/**
	 * Retry flag
	 *
	 * @var boolen
	 */
	protected $crawl_retry = false;

	/**
	 * limit of crawl retry
	 *
	 * @var integer
	 */
	protected $retry_limit = 0;

	/**
	 * Fault tolerance mode
	 *
	 * @var integer
	 */
	protected $fault_tolerance = 1;

	/**
	 * Instance
	 *
	 * @var array
	 */
	private static $instance = array();

	/**
	 * Instance of crawl strategies
	 *
	 * @var array
	 */
	protected $crawl_strategies = array();

	/**
	 * Cache target
	 *
	 * @var array
	 */
	protected $target_sns = array();

	/**
	 * Instance for delegation
	 *
	 * @var SNS_Count_Cache
	 */
	protected $delegate = null;

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 *
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		//$this->get_object_id();
	}

	/**
	 * Get instance
	 *
	 * @return SCC_Crawler
	 */
	public static function get_instance() {

		$class_name = get_called_class();

		if ( ! isset( self::$instance[ $class_name ] ) ) {
			self::$instance[ $class_name ] = new $class_name();
		}

		return self::$instance[ $class_name ];
	}

	/**
	 * Get object ID
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
	 * Inhibit clone processing
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
	 * Set option
	 *
	 * @param array $options Option.
	 * @return void
	 */
	abstract public function set_option( $options = array() );

	/**
	 * Get data
	 *
	 * @param array $target_sns Target SNS.
	 * @param array $options Option.
	 * @return array
	 */
	abstract public function get_data( $target_sns, $options );

	/**
	 * Initialize crawl strategy with option
	 *
	 * @param string $sns Target SNS.
	 * @param array $options Option.
	 * @return void
	 */
	public function initialize_crawl_strategy( $sns, $options ) {
		if ( isset( $this->crawl_strategies[ $sns ] ) ) {
			$this->crawl_strategies[ $sns ]->initialize( $options );
		}
	}

	/**
	 * Set option of crawl strategy
	 *
	 * @param sting $sns Target SNS.
	 * @param array $options Option.
	 * @return void
	 */
	public function set_crawl_strategy_parameters( $sns, $options ) {
		if ( isset( $this->crawl_strategies[ $sns ] ) ) {
			$this->crawl_strategies[ $sns ]->set_parameters( $options );
		}
	}

	/**
	 * [get_crawl_strategy_parameters description]
	 * @param  [type] $sns [description]
	 * @return [type]      [description]
	 */

	/**
	 * Get parameter of crawl strategy
	 *
	 * @param string target SNS.
	 * @return array
	 */
	public function get_crawl_strategy_parameters( $sns ) {
		return $this->crawl_strategies[ $sns ]->get_parameters();
	}

	/**
	 * Chceck Configuration of crawl strategy
	 *
	 * @param $sns Target SNS.
	 * @return array
	 */
	public function check_crawl_strategy_configurations( $sns ) {
		return $this->crawl_strategies[ $sns ]->check_configuration();
	}

	/**
	 * Delegate order
	 *
	 * @param string $order Order.
	 * @param array  $options Option.
	 * @return mixed Result of delegation
	 */
	protected function delegate_order( $order, $options = array() ) {
		if ( ! is_null( $this->delegate ) && ( $this->delegate instanceof SCC_Order ) && method_exists( $this->delegate, 'order' ) ) {
			return $this->delegate->order( $this, $order, $options );
		}
	}

}

?>
