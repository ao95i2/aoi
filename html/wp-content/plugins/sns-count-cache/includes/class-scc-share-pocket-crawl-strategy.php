<?php
/**
class-scc-share-pocket-crawl-strategy.php

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
 * SCC_Share_Pocket_Crawl_Strateg
 */
class SCC_Share_Pocket_Crawl_Strategy extends SCC_Crawl_Strategy {

	/**
	 * SNS base URL
	 *
	 * @var string
	 */
	const DEF_BASE_URL = 'https://widgets.getpocket.com/api/saves';

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

			$json = json_decode( $content['body'], true );

			if ( isset( $json['saves'] ) && is_numeric( $json['saves'] ) ) {
				$count = (int) $json['saves'];
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
			$this->query_parameters['url'] = $value;
		}
	}

	/**
	 * Check if required paramters are included or not.
	 *
	 * @return boolean
	 */
	public function check_configuration() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		if ( ! empty( $this->query_parameters['url'] ) ) {
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
