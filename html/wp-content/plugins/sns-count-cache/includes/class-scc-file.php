<?php
/**
class-scc-file.php

Description: This class is a common utility
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
 * SCC_File
 */
class SCC_File {

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Get file size of given file
	 *
	 * @param string $file File path.
	 * @return integer
	 */
	public static function get_file_size( $file ) {

		if ( file_exists( $file ) && is_file( $file ) ) {
			$filesize = filesize( $file );
			$s = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
			$e = floor( log( $filesize ) / log( 1024 ) );

			if ( 0 === $e || 1 === $e ) {
				$format = '%d ';
			} else {
				$format = '%.1f ';
			}

			$filesize = sprintf( $format . $s[ $e ], ( $filesize / pow( 1024, floor( $e ) ) ) );

			return $filesize;
		} else {
			return null;
		}
	}

}

?>
