<?php
/**
class-scc-memory.php

Description: This class is a common utility
Author: Daisuke Maruyama
Author URI: http://logicore.cc/
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

class SCC_Memory {

	/**
	 * Constructor
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Get current usage
	 *
	 * @return integer
	 */
	public static function get_current_usage() {
		return round( memory_get_usage( true ) / 1024 / 1024, 2 );
	}

	/**
	 * Get peak usage
	 *
	 * @return integer
	 */
	public static function get_peak_usage() {
		return round( memory_get_peak_usage( true ) / 1024 / 1024, 2 );
	}

}

?>
