<?php
/**
class-scc-analytical-engline.php

Description: This class is a data analytical engine.
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
 * SCC_Analytical_Engine
 */
abstract class SCC_Analytical_Engine extends SCC_Engine {

	/**
	 * Prefix of cache ID
	 *
	 * @var string
	 */
	protected $cache_prefix = null;

	/**
	 * Prefix of base ID
	 *
	 * @var string
	 */
	protected $base_prefix = null;

	/**
	 * Prefix of delta ID
	 *
	 * @var [type]
	 */
	protected $delta_prefix = null;

	/**
	 * Instance for delegation
	 *
	 * @var SNS_Count_Cache
	 */
	protected $delegate = null;

	/**
	 * Get and cache data for a given post
	 *
	 * @param array $options Option.
	 * @return void
	 */
	abstract public function analyze( $options = array() );

	/**
	 * Initialize cache
	 *
	 * @return void
	 */
	abstract public function initialize_base();

	/**
	 * Clear cache
	 *
	 * @return void
	 */
	abstract public function clear_base();

	/**
	 * Get cache key
	 *
	 * @param string $suffix Suffix of cache key.
	 * @return string
	 */
	public function get_cache_key( $suffix ) {
		return $this->cache_prefix . strtolower( $suffix );
	}

	/**
	 * Get base key
	 *
	 * @param string $suffix Suffix of base key.
	 * @return string
	 */
	public function get_base_key( $suffix ) {
		return $this->base_prefix . strtolower( $suffix );
	}

	/**
	 * Get delta key
	 *
	 * @param string $suffix Suffix of delta key.
	 * @return string
	 */
	public function get_delta_key( $suffix ) {
		return $this->delta_prefix . strtolower( $suffix );
	}

}

?>
