<?php
/**
class-scc-crypt.php

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
 * SCC_Crypt
 */
class SCC_Crypt {

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 *
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Encrypt data
	 *
	 * @param data $raw_data Raw data.
	 * @param string $password Password.
	 * @return base64 Encrypted data
	 */
	public static function encrypt( $raw_data, $password ) {
		$salt = openssl_random_pseudo_bytes( 16 );

		$salted = '';
		$dx = '';
		// Salt the key(32) and iv(16) = 48

		$salted_length = strlen( $salted );
		while ( $salted_length < 48 ) {
			$dx = hash( 'sha256', $dx . $password . $salt, true );
			$salted .= $dx;
			$salted_length = strlen( $salted );
		}

		$key = substr( $salted, 0, 32 );
		$iv  = substr( $salted, 32, 16 );

		$encrypted_data = openssl_encrypt( $raw_data, 'AES-256-CBC', $key, true, $iv );
		return base64_encode( $salt . $encrypted_data );
	}

	/**
	 * Decrypt data
	 *
	 * @param data $encrypted_data Encrypted data.
	 * @param string $password Password.
	 * @return decrypted Data.
	 */
	public static function decrypt( $encrypted_data, $password ) {

		$data = base64_decode( $encrypted_data );
		$salt = substr( $data, 0, 16 );
		$ct = substr( $data, 16 );

		$rounds = 3;
		$data00 = $password . $salt;
		$hash = array();
		$hash[0] = hash( 'sha256', $data00, true );
		$result = $hash[0];

		for ( $i = 1; $i < $rounds; ++$i ) {
			$hash[ $i ] = hash( 'sha256', $hash[ $i - 1 ] . $data00, true );
			$result .= $hash[ $i ];
		}

		$key = substr( $result, 0, 32 );
		$iv  = substr( $result, 32,16 );

		return openssl_decrypt( $ct, 'AES-256-CBC', $key, true, $iv );
	}

}

?>
