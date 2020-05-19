<?php
/**
class-scc-format.php

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
 * SCC_Format
 */
class SCC_Format {

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Convert url based on http into url based on https
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function get_secure_url( $url ) {
		$url = str_replace( 'http://', 'https://', $url );
		return $url;
	}

	/**
	 * Convert url based on https into url based on http
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function get_http_url( $url ) {
		$url = str_replace( 'https://', 'http://', $url );
		return $url;
	}

	/**
	 * Check if a given URL is based on https or not.
	 *
	 * @param string $url URL.
	 * @return boolean
	 */
	public static function is_https_url( $url ) {
		if ( preg_match( '/^(https)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $url ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if a given URL is based on http or not.
	 *
	 * @param string $url URL.
	 * @return boolean
	 */
	public static function is_http_url( $url ) {
		if ( preg_match( '/^(http)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $url ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if a given URL is valid URL or not
	 *
	 * @param string $url URL.
	 * @return boolean
	 */
	public static function is_url( $url ) {
		if ( false !== filter_var( $url, FILTER_VALIDATE_URL ) && preg_match( '@^https?+://@i', $url ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if a given ID is vaild or not.
	 *
	 * @param mixed $id ID.
	 * @return boolean
	 */
	public static function is_content_id( $id ) {
		if ( is_numeric( $id ) || 'home' === $id ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sanitize
	 *
	 * @param string $str Input string.
	 * @return string
	 */
	public static function sanitize( $str ) {
		$filtered = wp_check_invalid_utf8( $str );

		if ( strpos( $filtered, '<' ) !== false ) {
			$filtered = wp_pre_kses_less_than( $filtered );
			// This will strip extra whitespace for us.
			$filtered = wp_strip_all_tags( $filtered, true );
		} else {
			$filtered = trim( preg_replace( '/[\r\n\t ]+/', ' ', $filtered ) );
		}

		$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );

		return $filtered;
	}

	/**
	 * Sanitize key
	 *
	 * @param string $cache_key Cache key.
	 * @return string
	 */
	public static function sanitize_cache_key( $cache_key ) {
		$cache_key = strtolower( $cache_key );
		$cache_key = preg_replace( '/[^a-z0-9_\-\+]/', '', $cache_key );
		return $cache_key;
	}

}

?>
