<?php
/**
class-scc-sleep-throttle.php

Description: sleep utility
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
 * SCC_Sleep_Throttle
 */
final class SCC_Sleep_Throttle {

	/**
	 * Second to micro seconds
	 */
	const SECOND_TO_MICRO_SECONDS = 1000000;

	/**
	 * Load ratio
	 *
	 * @var float
	 */
	private $load_ratio = 0.9;

	/**
	 * Start time
	 *
	 * @var float
	 */
	private $start_time = null;

	/**
	 * End time
	 *
	 * @var float
	 */
	private $stop_time = null;

	/**
	 * Sleep time
	 *
	 * @var float
	 */
	private $sleep_time = null;

	/**
	 * Constructor
	 *
	 * @param float $load_ratio Load ratio.
	 */
	function __construct( $load_ratio ) {
		$this->load_ratio = $load_ratio;
	}

	/**
	 * Reset
	 *
	 * @return void
	 */
	public function reset() {
		$this->start_time = null;
		$this->stop_time = null;
		$this->sleep_time = null;
	}

	/**
	 * Start
	 *
	 * @return void
	 */
	public function start() {
		$this->start_time = gettimeofday( true );
	}

	/**
	 * Stop
	 *
	 * @return void
	 */
	public function stop() {
		$this->stop_time = gettimeofday( true );

		if ( ! is_null( $this->start_time ) && ! is_null( $this->stop_time ) ) {
			$this->sleep_time = $this->calculate_sleep_time( $this->load_ratio, $this->stop_time - $this->start_time );
		}
	}

	/**
	 * Sleep
	 *
	 * @return void
	 */
	public function sleep() {
		if ( ! is_null( $this->sleep_time ) && $this->sleep_time > 0 ) {
			usleep( $this->sleep_time * self::SECOND_TO_MICRO_SECONDS );
		}
	}

	/**
	 * Get sleep time
	 *
	 * @return float
	 */
	public function get_sleep_time() {
		if ( ! is_null( $this->sleep_time ) && $this->sleep_time > 0 ) {
			return $this->sleep_time;
		} else {
			return 0;
		}
	}

	/**
	 * Calculate sleep time
	 *
	 * @param float $load_ratio Load ratio.
	 * @param float $time Time.
	 * @return float
	 */
	private function calculate_sleep_time( $load_ratio, $time ) {
		if ( $time > 0.0 ) {
			return $time * ( 1 - $load_ratio ) / $load_ratio;
		} else {
			return 0;
		}
	}

}

?>
