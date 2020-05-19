<?php
/**
class-scc-follow-twitter-crawl-strategy.php

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
 * SCC_Follow_Twitter_Crawl_Strategy
 */
class SCC_Follow_Twitter_Crawl_Strategy extends SCC_Crawl_Strategy {

	/**
	 * SNS base URL
	 *
	 * @var string
	 */
	const DEF_BASE_URL = 'https://api.twitter.com/1.1/users/show.json';

	/**
	 * Class constarctor
	 * Hook onto all of the actions and filters needed by the plugin.
	 */
	protected function __construct() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$this->http_method = 'GET';
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

		$headers = array();

		/*
		// vesion using user auth
		$oauth_parameters = array(
			'oauth_consumer_key' => $this->parameters['api_key'],
			'oauth_token' => $this->parameters['access_token'],
			'oauth_nonce' => microtime(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_version' => '1.0'
			);

		$signature_key = rawurlencode( $this->parameters['api_secret_key'] ) . '&' . rawurlencode( $this->parameters['access_token_secret'] );

		$oauth_parameters = array_merge( $oauth_parameters, $this->query_parameters );

		ksort( $oauth_parameters );

		$signature_parameters = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ) , http_build_query( $oauth_parameters , '' , '&' ) );

		$signature_data = rawurlencode( $this->method ) . '&' . rawurlencode( self::DEF_BASE_URL ) . '&' . rawurlencode( $signature_parameters );

		$signature = base64_encode( hash_hmac( 'sha1' , $signature_data , $signature_key , true ) );

		$oauth_parameters['oauth_signature'] = $signature;

		$header_parameters = http_build_query( $oauth_parameters, '', ',' );

		$headers['Authorization'] = 'OAuth ' . $header_parameters;
		*/

		// version using application-only auth
		$headers['Authorization'] = 'Bearer ' . $this->parameters['bearer_token'];

		return $headers;
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

		$count = (int) -1;

		if ( isset( $content['body'] ) && empty( $content['error'] ) ) {
			$json = json_decode( $content['body'], true );

			if ( isset( $json['followers_count'] ) && is_numeric( $json['followers_count'] ) ) {
				$count = (int) $json['followers_count'];
			} else {
				$count = (int) -1;
			}
		} else {
			$count = (int) -1;

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
		if ( 'screen_name' === $key ) {
			$this->query_parameters[ $key ] = $value;
		} elseif ( 'api_key' === $key ) {
			$this->parameters[ $key ] = $value;
		} elseif ( 'api_secret_key' === $key ) {
			$this->parameters[ $key ] = $value;
		} elseif ( 'bearer_token' === $key ) {
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

		/*
		// vesion using user auth
		if ( isset( $this->query_parameters['screen_name'] ) && $this->query_parameters['screen_name'] &&
			isset( $this->parameters['api_key'] ) && $this->parameters['api_key'] &&
			isset( $this->parameters['api_secret_key'] ) && $this->parameters['api_secret_key'] &&
			isset( $this->parameters['access_token'] ) && $this->parameters['access_token'] &&
			isset( $this->parameters['access_token_secret'] ) && $this->parameters['access_token_secret']
		) {
			return true;
		} else {
			return false;
		}
		*/

		// version using application-only auth
		if ( ! empty( $this->query_parameters['screen_name'] ) && ! empty( $this->parameters['api_key'] ) &&
			! empty( $this->parameters['api_secret_key'] ) && ! empty( $this->parameters['bearer_token'] ) ) {
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

		return true;
	}

}
