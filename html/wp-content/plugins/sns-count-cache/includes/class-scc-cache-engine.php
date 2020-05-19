<?php
/**
class-scc-cache-engine.php

Description: This class is a data cache engine whitch get and cache data using wp-cron at regular intervals
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
 * SCC_Cache Engine
 */
abstract class SCC_Cache_Engine extends SCC_Engine {

	/**
	 * Prefix of cache ID
	 *
	 * @var string
	 */
	protected $cache_prefix = null;

	/**
	 * Instance for delegation
	 *
	 * @var SNS_Count_Cache
	 */
	protected $delegate = null;

	/**
	 * Retry of cache processing
	 *
	 * @var boolean
	 */
	protected $cache_retry = false;

	/**
	 * Limit of cache retry
	 *
	 * @var integer
	 */
	protected $retry_limit = 3;

	/**
	 * Fault tolerance mode
	 *
	 * @var integer
	 */
	protected $fault_tolerance_mode = 1;

	/**
	 * Crawl throttling mode
	 *
	 * @var integer
	 */
	protected $crawl_throttling_mode = 1;

	/**
	 * Get cache expiration based on current number of total post and page
	 *
	 * @return void
	 */
	abstract protected function get_cache_expiration();

	/**
	 * Get and cache data for a given post
	 *
	 * @param array $options option for cache processing.
	 * @return void
	 */
	abstract public function cache( $options = array() );

	/**
	 * Initialize cache
	 *
	 * @return void
	 */
	abstract public function initialize_cache();

	/**
	 * Clear cache
	 *
	 * @return void
	 */
	abstract public function clear_cache();

	/**
	 * Get cache
	 *
	 * @param array $options Option.
	 * @return void
	 */
	abstract public function get_cache( $options = array() );

	/**
	 * Get cache key
	 *
	 * @param string $suffix Suffix of cache key.
	 * @return string Cache key.
	 */
	public function get_cache_key( $suffix ) {
		return $this->cache_prefix . strtolower( $suffix );
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
