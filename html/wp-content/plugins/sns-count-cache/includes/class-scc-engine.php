<?php
/**
class-scc-engine.php

Description: This class is a engine
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
 * SCC_Engine
 */
abstract class SCC_Engine {

	/**
	 * Cron name to schedule cache processing
	 *
	 * @var string
	 */
	protected $prime_cron = null;

	/**
	 * Cron name to execute cache processing
	 *
	 * @var string
	 */
	protected $execute_cron = null;

	/**
	 * Schedule name for cache processing
	 *
	 * @var string
	 */
	protected $event_schedule = null;

	/**
	 * Schedule description for cache processing
	 *
	 * @var string
	 */
	protected $event_description = null;

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
	 * @return SCC_Engine
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
	 * Register base schedule for this engine
	 *
	 * @return void
	 */
	public function register_schedule() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		if ( ! wp_next_scheduled( $this->prime_cron ) ) {
			wp_schedule_event( time(), $this->event_schedule, $this->prime_cron );
		}
	}

	/**
	 * Unregister base schedule for this engine
	 *
	 * @return void
	 */
	public function unregister_schedule() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		wp_clear_scheduled_hook( $this->prime_cron );
		SCC_WP_Cron::clear_scheduled_hook( $this->execute_cron );
	}

	/**
	 * Get name of prime cron
	 *
	 * @return string
	 */
	public function get_prime_cron() {
		return $this->prime_cron;
	}

	/**
	 * Get name of execute cron
	 *
	 * @return string
	 */
	public function get_excute_cron() {
		return $this->execute_cron;
	}

	/**
	 * Initialization
	 *
	 * @param array $options Option.
	 * @return void
	 */
	abstract public function initialize( $options = array() );

}

?>
