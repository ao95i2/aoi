<?php
/**
class-scc-oauth.php

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
 * SCC_Oauth
 */
class SCC_Oauth {

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Get bearer token for Twitter
	 *
	 * @param string $cosumer_key Consumer key.
	 * @param string $consumer_secret Consumer secret.
	 * @param boolean $sslverify SSL verification.
	 * @param integer $timeout Timeout.
	 * @return string
	 */
	public static function get_twitter_bearer_token( $cosumer_key, $consumer_secret, $sslverify = true, $timeout = 10 ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		global $wp_version;

		$bearer_token = '';
		$url = 'https://api.twitter.com/oauth2/token';

		$credential = base64_encode( $cosumer_key . ':' . $consumer_secret );

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

		$headers = array(
			'Authorization' => 'Basic ' . $credential,
			'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
		);

		if ( ! empty( $headers ) ) {
			$options['headers'] = $headers;
		}

		$body = array(
			'grant_type' => 'client_credentials',
		);

		if ( ! empty( $body ) ) {
			$options['body'] = $body;
		}

		$response = wp_remote_post( $url, $options );

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === $response['response']['code'] ) {
				$json = $response['body'];
				$content = json_decode( $json, true );
				if ( ! empty( $content['token_type'] ) && 'bearer' === $content['token_type'] ) {
					if ( ! empty( $content['access_token'] ) ) {
						$bearer_token = $content['access_token'];
					} else {
						return new WP_Error( 'no_bearer_token', '[ERROR] No bearer token' );
					}
				} else {
					return new WP_Error( 'no_bearer_token', '[ERROR] No bearer token' );
				}
			} else {
				return new WP_Error( 'invalid_response_code', '[ERROR] Invalid response code: ' . $response['response']['code'] . ' ' . $response['response']['message'] );
			}
		} else {
			return $response;
		}

		return $bearer_token;
	}

	/**
	 * Get acess token for Instagram
	 *
	 * @param string $client_id Client ID.
	 * @param string $client_secret Client Secret.
	 * @param string $redirect_uri Redirect URI.
	 * @param string $code Code.
	 * @param boolean $sslverify SSL verification.
	 * @param integer $timeout Timeout.
	 * @return string
	 */
	public static function get_instagram_access_token( $client_id, $client_secret, $redirect_uri, $code, $sslverify = true, $timeout = 10 ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		global $wp_version;

		$access_token = '';
		$url = 'https://api.instagram.com/oauth/access_token';

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

		$body = array(
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $redirect_uri,
			'code' => $code,
		);

		if ( ! empty( $body ) ) {
			$options['body'] = $body;
		}

		$response = wp_remote_post( $url, $options );

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === $response['response']['code'] ) {
				$json = $response['body'];
				$content = json_decode( $json, true );
				if ( ! empty( $content['access_token'] ) ) {
					$access_token = $content['access_token'];
				} else {
					return new WP_Error( 'no_access_token', '[ERROR] No access token' );
				}
			} else {
				return new WP_Error( 'invalid_response_code', '[ERROR] Invalid response code: ' . $response['response']['code'] . ' ' . $response['response']['message'] );
			}
		} else {
			return $response;
		}

		return $access_token;
	}

	/**
	 * Get acess token for Facebook
	 *
	 * @param string $app_id App ID,
	 * @param string $app_secret App secret.
	 * @param string $redirect_uri Redirect URI.
	 * @param string $code Code.
	 * @param string $page_id Page ID.
	 * @param boolean $sslverify SSL verification.
	 * @param integer $timeout Timeout.
	 * @return string
	 */
	public static function get_facebook_access_token( $app_id, $app_secret, $redirect_uri, $code, $page_id, $sslverify = true, $timeout = 10 ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		global $wp_version;

		$original_access_token = '';
		$extended_access_token = '';
		$access_token = '';

		$url = 'https://graph.facebook.com/oauth/access_token';

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

		$query_parameters = array(
			'client_id' => $app_id,
			'client_secret' => $app_secret,
			'redirect_uri' => $redirect_uri,
			'code' => $code,
		);

		$request_url = $url . '?' . http_build_query( $query_parameters , '' , '&' );

		$response = wp_remote_get( $request_url, $options );

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === $response['response']['code'] ) {

				$data = json_decode( $response['body'], true );

				if ( ! empty( $data['access_token'] ) ) {
					$original_access_token = $data['access_token'];
				} else {
					return new WP_Error( 'no_original_access_token', '[ERROR] No original access token' );
				}
			} else {
				return new WP_Error( 'invalid_response_code', '[ERROR] Invalid response code: ' . $response['response']['code'] . ' ' . $response['response']['message'] );
			}
		} else {
			return new WP_Error( 'request_error', '[ERROR] Request error' );
		}

		// SCC_Logger::log( $response );
		// SCC_Logger::log( '[' . __METHOD__ . '] Original access token: ' . $original_access_token );

		if ( ! empty( $original_access_token ) ) {
			$url = 'https://graph.facebook.com/oauth/access_token';

			$query_parameters = array(
				'client_id' => $app_id,
				'client_secret' => $app_secret,
				'grant_type' => 'fb_exchange_token',
				'fb_exchange_token' => $original_access_token,
			);

			$request_url = $url . '?' . http_build_query( $query_parameters, '' , '&' );

			$response = wp_remote_get( $request_url, $options );

			$data = array();

			if ( ! is_wp_error( $response ) ) {
				if ( 200 === $response['response']['code'] ) {

					$data = json_decode( $response['body'], true );

					if ( ! empty( $data['access_token'] ) ) {
						$extended_access_token = $data['access_token'];
					} else {
						return new WP_Error( 'no_extended_access_token', '[ERROR] No extended access token' );
					}
				} else {
					return new WP_Error( 'invalid_response_code', '[ERROR] Invalid response code: ' . $response['response']['code'] . ' ' . $response['response']['message'] );
				}
			} else {
				return new WP_Error( 'request_error', '[ERROR] Request error' );
			}
		} // End if().

		// SCC_Logger::log( $response );
		// SCC_Logger::log( '[' . __METHOD__ . '] Extended access token: ' . $original_access_token );

		if ( ! empty( $extended_access_token ) ) {
			$url = 'https://graph.facebook.com/me/accounts';

			$query_parameters = array(
				'access_token' => $extended_access_token,
			);

			$request_url = $url . '?' . http_build_query( $query_parameters, '' , '&' );

			$response = wp_remote_get( $request_url, $options );

			// SCC_Logger::log( $response );

			if ( ! is_wp_error( $response ) ) {
				if ( 200 === $response['response']['code'] ) {
					$content = json_decode( $response['body'], true );

					$i = 0;

					while ( true ) {
						if ( ! isset( $content['data'][ $i ] ) ) {
							break;
						}

						if ( isset( $content['data'][ $i ]['id'] ) && $content['data'][ $i ]['id'] === $page_id ) {
							if ( ! empty( $content['data'][ $i ]['access_token'] ) ) {
								$access_token = $content['data'][ $i ]['access_token'];
							}
							break;
						}

						++$i;
					}

					if ( ! $access_token ) {
						return new WP_Error( 'no_target_access_token', '[ERROR] No access token' );
					} else {
						return $access_token;
					}
				} else {
					return new WP_Error( 'invalid_response_code', '[ERROR] Invalid response code: ' . $response['response']['code'] . ' ' . $response['response']['message'] );
				}
			} else {
				return new WP_Error( 'request_error', '[ERROR] Request error' );
			}
		} // End if().

	}

}

?>
