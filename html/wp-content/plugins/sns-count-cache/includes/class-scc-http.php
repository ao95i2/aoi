<?php
/**
class-scc-http.php

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
 * SCC_HTTP
 */
class SCC_HTTP {

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Request based on given parameters,
	 *
	 * @param array $urls URLs.
	 * @param array $methods HTTP methos.
	 * @param array $headers Headers.
	 * @param array $bodies Bodies.
	 * @param integer $timeout Timeout.
	 * @param boolean $sslverify SSL verification.
	 * @param boolean $curl cURL flag.
	 * @return array
	 */
	public static function multi_remote_request( $urls, $methods, $headers, $bodies, $timeout = 0, $sslverify = true, $curl = false ) {
		global $wp_version;

		$responses = array();

		if ( empty( $urls ) ) {
			return $responses;
		}

		if ( $curl ) {
			SCC_Logger::log( '[' . __METHOD__ . '] cURL: On' );

			$mh = curl_multi_init();
			$ch = array();

			$handle_to_sns = array();

			foreach ( $urls as $sns => $url ) {
				$ch[ $sns ] = curl_init();

				curl_setopt( $ch[ $sns ], CURLOPT_URL, $url );
				curl_setopt( $ch[ $sns ], CURLOPT_USERAGENT, 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) );
				curl_setopt( $ch[ $sns ], CURLOPT_FOLLOWLOCATION, true );
				curl_setopt( $ch[ $sns ], CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch[ $sns ], CURLOPT_ENCODING, 'gzip' );
				curl_setopt( $ch[ $sns ], CURLOPT_HEADER, true );

				if ( ! empty( $headers[ $sns ] ) ) {
					$http_headers = array();

					foreach ( $headers[ $sns ] as $key => $value ) {
						$http_headers[] = $key . ': ' . $value;
					}

					curl_setopt( $ch[ $sns ], CURLOPT_HTTPHEADER, $http_headers );
				}

				if ( $sslverify ) {
					curl_setopt( $ch[ $sns ], CURLOPT_SSL_VERIFYPEER, true );
					curl_setopt( $ch[ $sns ], CURLOPT_SSL_VERIFYHOST, 2 );
				} else {
					curl_setopt( $ch[ $sns ], CURLOPT_SSL_VERIFYPEER, false );
					curl_setopt( $ch[ $sns ], CURLOPT_SSL_VERIFYHOST, 0 );
				}

				if ( $timeout > 0 ) {
					curl_setopt( $ch[ $sns ], CURLOPT_CONNECTTIMEOUT, $timeout );
					curl_setopt( $ch[ $sns ], CURLOPT_TIMEOUT, $timeout );
				}

				if ( isset( $methods[ $sns ] ) && 'POST' === $methods[ $sns ] ) {
					curl_setopt( $ch[ $sns ], CURLOPT_POST, true );
					if ( isset( $bodies[ $sns ] ) ) {
						curl_setopt( $ch[ $sns ], CURLOPT_POSTFIELDS, wp_json_encode( $bodies[ $sns ] ) );
					}
				}

				$handle_to_sns[ (string) $ch[ $sns ] ] = $sns;

				curl_multi_add_handle( $mh, $ch[ $sns ] );
			} // End foreach().

			do {
				curl_multi_exec( $mh, $active );
				curl_multi_select( $mh );
			} while ( $active > 0 );

			foreach ( $urls as $sns => $url ) {
				$error = curl_error( $ch[ $sns ] );
				$response = curl_multi_getcontent( $ch[ $sns ] );

				$info = curl_getinfo( $ch[ $sns ] );
				$header_size = $info['header_size'];

				$header = substr( $response, 0, $header_size );
				$body = substr( $response, $header_size );

				$headers = array();
				$data = preg_split( '/[\r\n]+/', $header );
				// $data = explode( '\r\n', $header );
				array_shift( $data );
				array_pop( $data );

				foreach ( $data as $part ) {
					$middle = explode( ':', $part, 2 );
					$headers[ trim( strtolower( $middle[0] ) ) ] = trim( $middle[1] );
				}

				// SCC_Logger::log( $header );
				// SCC_Logger::log( $headers );

				if ( ! empty( $error ) || false === $response ) {
					$responses[ $sns ]['body'] = '';
					$responses[ $sns ]['error'] = $error;
					$responses[ $sns ]['header'] = $headers;
				} else {
					$responses[ $sns ]['body'] = $body;
					$responses[ $sns ]['error'] = $error;
					$responses[ $sns ]['header'] = $headers;
				}

				curl_multi_remove_handle( $mh, $ch[ $sns ] );
				curl_close( $ch[ $sns ] );
			} // End foreach().

			curl_multi_close( $mh );

			/*

			do {
				$stat = curl_multi_exec( $mh, $running );
			} while ( CURLM_CALL_MULTI_PERFORM === $stat );

			if ( ! $running || CURLM_OK !== $stat ) {
				throw new RuntimeException( 'リクエストが開始出来なかった。マルチリクエスト内のどれか、URLの設定がおかしいのでは？' );
			}

			do switch ( curl_multi_select( $mh, $timeout ) ) {

				case -1:
					usleep( 10 );
					do {
						$stat = curl_multi_exec( $mh, $running );
					} while ( CURLM_CALL_MULTI_PERFORM === $stat );
					continue 2;

				case 0:
					continue 2;

				default:
					do {
						$stat = curl_multi_exec( $mh, $running );
					} while ( CURLM_CALL_MULTI_PERFORM === $stat );

					do if ( $raised = curl_multi_info_read( $mh, $remains ) ) {

						$sns = (string) $handle_to_sns[ (string) $raised['handle'] ];

						$info = curl_getinfo( $raised['handle'] );
						//echo "$info[url]: $info[http_code]\n";

						$header_size = $info['header_size'];

						$error = curl_error( $raised['handle'] );
						$response = curl_multi_getcontent( $raised['handle'] );

						$header = substr( $response, 0, $header_size );
						$body = substr( $response, $header_size );

						$headers = array();
						$data = preg_split( '/[\r\n]+/', $header );
						//$data = explode( '\r\n', $header );
						array_shift( $data );
						array_pop( $data );

						foreach ( $data as $part ) {
							$middle = explode( ':', $part, 2 );
							$headers[ trim( strtolower( $middle[0] ) ) ] = trim( $middle[1] );
						}

						//SCC_Logger::log( $header );
						//SCC_Logger::log( $headers );

						if ( ! empty( $error ) || false === $response ) {
							$responses[ $sns ]['body'] = '';
							$responses[ $sns ]['error'] = $error;
							$responses[ $sns ]['header'] = $headers;
						} else {
							$responses[ $sns ]['body'] = $body;
							$responses[ $sns ]['error'] = $error;
							$responses[ $sns ]['header'] = $headers;
						}

						curl_multi_remove_handle( $mh, $raised['handle'] );
						curl_close( $raised['handle'] );
					} while ( $remains );
			} while ( $running );

			curl_multi_close( $mh );
			*/

			return $responses;

		} else {

			SCC_Logger::log( '[' . __METHOD__ . '] cURL: Off' );

			foreach ( $urls as $sns => $url ) {

				$options = array(
					'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
				);

				if ( $sslverify ) {
					$options['sslverify'] = true;
				} else {
					$options['sslverify'] = false;
				}

				if ( $timeout > 0 ) {
					$options['timeout'] = $timeout;
				}

				if ( ! empty( $headers[ $sns ] ) ) {
					$options['headers'] = $headers[ $sns ];
				}

				if ( isset( $methods[ $sns ] ) ) {
					if ( 'GET' === $methods[ $sns ] ) {
						$response = wp_remote_get( $url, $options );
					} elseif ( 'POST' === $methods[ $sns ] ) {
						if ( isset( $bodies[ $sns ] ) ) {
							$options['body'] = wp_json_encode( $bodies[ $sns ] );
						}
						$response = wp_remote_post( $url, $options );
					} else {
						$response = wp_remote_get( $url, $options );
					}
				}

				// SCC_Logger::log( $response );

				if ( ! is_wp_error( $response ) ) {
					if ( 200 === $response['response']['code'] ) {
						$responses[ $sns ]['body'] = $response['body'];
						$responses[ $sns ]['error'] = '';
						$responses[ $sns ]['header'] = wp_remote_retrieve_headers( $response );
					} else {
						$responses[ $sns ]['body'] = '';
						$responses[ $sns ]['error'] = 'Response Code: ' . $response['response']['code'] . ' Message: ' . $response['response']['message'];
						$responses[ $sns ]['header'] = wp_remote_retrieve_headers( $response );
					}
				} else {
					$responses[ $sns ]['body'] = '';
					$responses[ $sns ]['error'] = $response->get_error_message();
					$responses[ $sns ]['header'] = wp_remote_retrieve_headers( $response );
				}
			} // End foreach().

			return $responses;
		} // End if().
	}

}

?>
