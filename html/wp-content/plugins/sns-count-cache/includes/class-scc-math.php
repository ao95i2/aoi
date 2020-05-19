<?php
/**
class-scc-math.php

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

class SCC_Math {

	/**
	 * Constructor
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Generates a random number.
	 *
	 * @param integer $min
	 * @param integer $max
	 * @return float
	 */
	public static function mt_randf( $min, $max ) {
		return $min + abs( $max - $min ) * mt_rand( 0, mt_getrandmax() ) / mt_getrandmax();
	}

}

?>
