<?php
/**
class-scc-share-facebook-crawl-strategy.php

Description: This class is abstract class of a data crawler
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
 * SCC_Share_Facebook_Crawl_Strategy
 */
class SCC_Share_Facebook_Crawl_Strategy extends SCC_Crawl_Strategy {

	/**
	 * SNS base URL
	 *
	 * @var string
	 */
	const DEF_BASE_URL = 'https://graph.facebook.com/v3.2/';

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$this->http_method = 'GET';
		$this->query_parameters['fields'] = 'og_object{engagement},engagement';
	}

	/**
	 * Initialization
	 *
	 * @param array $options Option.
	 * @return void
	 */
	public function initialize( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$this->set_parameters( $options );
	}

	/**
	 * Build request header
	 *
	 * @return array
	 */
	public function build_request_header() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		return null;
	}

	/**
	 * Build request body
	 *
	 * @return array
	 */
	public function build_request_body() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
		return null;
	}

	/**
	 * Build request URL
	 *
	 * @return string
	 */
	public function build_request_url() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$url = self::DEF_BASE_URL . '?' . http_build_query( $this->query_parameters , '' , '&' );

		return $url;
	}

	/**
	 * Extract response body
	 *
	 * @param array $content Content.
	 * @return array
	 */
	public function extract_response_body( $content ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$count = -1;

		if ( isset( $content['body'] ) && empty( $content['error'] ) ) {
			// SCC_Logger::log( '[' . __METHOD__ . '] X-App-Usage: ' . $content['header'][ strtolower( 'X-App-Usage' ) ] );

			$json = json_decode( $content['body'], true );

			if ( isset( $json['engagement'] ) ) {
				if ( isset( $json['engagement']['reaction_count'] ) ) {
					$reaction_count =(int) $json['engagement']['reaction_count'];
				} else {
					$reaction_count = 0;
				}
				if ( isset( $json['engagement']['comment_count'] ) ) {
					$comment_count =(int) $json['engagement']['comment_count'];
				} else {
					$comment_count = 0;
				}
				if ( isset( $json['engagement']['share_count'] ) ) {
					$share_count =(int) $json['engagement']['share_count'];
				} else {
					$share_count = 0;
				}
				if ( isset( $json['engagement']['comment_plugin_count'] ) ) {
					$comment_plugin_count =(int) $json['engagement']['comment_plugin_count'];
				} else {
					$comment_plugin_count = 0;
				}

				$count = $reaction_count + $comment_count + $share_count + $comment_plugin_count;

			} else {
				$count = -1;
			}
		} else {
			$count = -1;

			if ( isset( $content['error'] ) ) {
				SCC_Logger::log( $content['error'] );
			}
		}

		return $count;
	}

	/**
	 * Extract response header
	 *
	 * @param array $content Content.
	 * @return array
	 */
	public function extract_response_header( $content ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		if ( isset( $content['header'] ) ) {

			$headers = array();

			if ( isset( $content['header'][ strtolower( 'X-App-Usage' ) ] ) ) {
				SCC_Logger::log( '[' . __METHOD__ . '] X-App-Usage: ' . $content['header'][ strtolower( 'X-App-Usage' ) ] );

				$headers['x-app-usage'] = json_decode( $content['header'][ strtolower( 'X-App-Usage' ) ], true );

				return $headers;
			}
		}

		return null;
	}

	/**
	 * Set parameter
	 *
	 * @param string $key Key.
	 * @param string $value Value.
	 * @return void
	 */
	public function set_parameter( $key, $value ) {
		if ( 'url' === $key ) {
			$this->query_parameters['id'] = $value;
		} elseif ( 'access_token' === $key ) {
			$this->query_parameters[ $key ] = $value;
		} elseif ( 'app_id' === $key ) {
			$this->parameters[ $key ] = $value;
		} elseif ( 'app_secret' === $key ) {
			$this->parameters[ $key ] = $value;
		}
	}

	/**
	 * Check if required paramters are included or not.
	 *
	 * @return boolean
	 */
	public function check_configuration() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		if ( ! empty( $this->parameters['app_id'] ) && ! empty( $this->parameters['app_secret'] ) && ! empty( $this->query_parameters['access_token'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if there is rate limiting or not.
	 *
	 * @param string $content Content.
	 * @return boolean
	 */
	public function check_rate_limiting( $content ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		if ( isset( $content['header'] ) ) {

			$headers = array();

			if ( isset( $content['header'][ strtolower( 'X-App-Usage' ) ] ) ) {

				$headers['x-app-usage'] = json_decode( $content['header'][ strtolower( 'X-App-Usage' ) ], true );

				if ( ! empty( $headers[ strtolower( 'X-App-Usage' ) ]['call_count'] ) ) {
					$current_call_count = (int) $headers[ strtolower( 'X-App-Usage' ) ]['call_count'];

					SCC_Logger::log( '[' . __METHOD__ . '] current_call_count: ' . $current_call_count );

					$threshold = 100;

					if ( $current_call_count + 20 > $threshold ) {
						return false;
					} else {
						return true;
					}
				} else {
					return true;
				}
			} else {
				return true;
			}
		} else {
			return true;
		}
	}


}
