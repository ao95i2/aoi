<?php
/**
class-scc-share-twitter-crawl-strategy.php

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
 * SCC_Share_Twitter_Crawl_Strategy
 *
 */
class SCC_Share_Twitter_Crawl_Strategy extends SCC_Crawl_Strategy {

	/**
	 * SNS base url (widgetoon.js & count.jsoon)
	 */
	const DEF_BASE_URL_JSOON = 'http://jsoon.digitiminimi.com/twitter/count.json';

	/**
	 * SNS base url (OpenShareCount)
	 */
	const DEF_BASE_URL_OPENSHARECOUNT = 'http://opensharecount.com/count.json';

	/**
	 * SNS base url (TwitCount)
	 */
	const DEF_BASE_URL_TWITCOUNT = 'http://counts.twitcount.com/counts.php';

	/**
	 * SNS base url (NewShareCounts)
	 */
	const DEF_BASE_URL_NEWSHARECOUNTS = 'http://public.newsharecounts.com/count.json';

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

		if ( isset( $options['twitter_api'] ) ) {
			$this->twitter_api = $options['twitter_api'];
		}

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

		$base_url = self::DEF_BASE_URL_JSOON;

		if ( isset( $this->twitter_api ) && $this->twitter_api ) {
			if ( SNS_Count_Cache::OPT_SHARE_TWITTER_API_JSOON === $this->twitter_api ) {
				$base_url = self::DEF_BASE_URL_JSOON;
			} elseif ( SNS_Count_Cache::OPT_SHARE_TWITTER_API_OPENSHARECOUNT === $this->twitter_api ) {
				$base_url = self::DEF_BASE_URL_OPENSHARECOUNT;
			} elseif ( SNS_Count_Cache::OPT_SHARE_TWITTER_API_TWITCOUNT === $this->twitter_api ) {
				$base_url = self::DEF_BASE_URL_TWITCOUNT;
			}
		}

		$url = $base_url . '?' . http_build_query( $this->query_parameters , '' , '&' );

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

			if ( isset( $json['count'] ) && is_numeric( $json['count'] ) ) {
				$count = (int) $json['count'];
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
		if ( isset( $this->twitter_api ) && $this->twitter_api ) {
			if ( SNS_Count_Cache::OPT_SHARE_TWITTER_API_JSOON === $this->twitter_api ) {
				if ( 'url' === $key ) {
					if ( home_url( '/', 'http' ) === $value || home_url( '/', 'https' ) === $value ) {
						$this->query_parameters[ $key ] = '"' . $value . '"';
					} else {
						$this->query_parameters[ $key ] = $value;
					}
				}
			} elseif ( SNS_Count_Cache::OPT_SHARE_TWITTER_API_OPENSHARECOUNT === $this->twitter_api ) {
				if ( 'url' === $key ) {
					$this->query_parameters[ $key ] = $value;
				}
			} elseif ( SNS_Count_Cache::OPT_SHARE_TWITTER_API_TWITCOUNT === $this->twitter_api ) {
				if ( 'url' === $key ) {
					$this->query_parameters[ $key ] = $value;
				}
			}
		} else {
			if ( 'url' === $key ) {
				$this->query_parameters[ $key ] = $value;
			}
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
