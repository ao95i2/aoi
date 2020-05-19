<?php
/**
class-scc-follow-crawler.php

Description: This class is a data crawler whitch get share count using given API and cURL
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

class SCC_Follow_Crawler extends SCC_Crawler {

	/**
	 * Initialization
	 *
	 * @param array $options Option.
	 * @return void
	 */
	public function initialize( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		//$this->throttle = new Sleep_Throttle( 0.9 );

		if ( isset( $options['target_sns'] ) ) {
			$this->target_sns = $options['target_sns'];
		}

		if ( isset( $options['crawl_method'] ) ) {
			$this->crawl_method = $options['crawl_method'];
		}

		if ( isset( $options['timeout'] ) ) {
			$this->timeout = $options['timeout'];
		}

		if ( isset( $options['ssl_verification'] ) ) {
			$this->ssl_verification = $options['ssl_verification'];
		}

		if ( isset( $options['crawl_retry'] ) ) {
			$this->crawl_retry = $options['crawl_retry'];
		}

		if ( isset( $options['retry_limit'] ) ) {
			$this->retry_limit = $options['retry_limit'];
		}

		$target_sns = $this->target_sns;

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				$this->crawl_strategies[ $sns ] = SCC_Follow_Crawl_Strategy_Factory::create_crawl_strategy( $sns );
			}
		}

	}

	/**
	 * Set option
	 *
	 * @param array $options Option.
	 * @return void
	 */
	public function set_option( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		if ( isset( $options['target_sns'] ) ) {
			$this->target_sns = $options['target_sns'];
		}

		if ( isset( $options['crawl_method'] ) ) {
			$this->crawl_method = $options['crawl_method'];
		}

		if ( isset( $options['timeout'] ) ) {
			$this->timeout = $options['timeout'];
		}

		if ( isset( $options['ssl_verification'] ) ) {
			$this->ssl_verification = $options['ssl_verification'];
		}

		if ( isset( $options['crawl_retry'] ) ) {
			$this->crawl_retry = $options['crawl_retry'];
		}

		if ( isset( $options['retry_limit'] ) ) {
			$this->retry_limit = $options['retry_limit'];
		}

	}

	/**
	 * Check configuration
	 *
	 * @param array $target_sns Target SNS.
	 * @return array
	 */
	private function check_configurations( $target_sns ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		SCC_Logger::log( $target_sns );

		$checked_target_sns = array();

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				$checked_target_sns[ $sns ] = $this->crawl_strategies[ $sns ]->check_configuration();
			}
		}

		$checked_target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] = true;

		return $checked_target_sns;
	}

	/**
	 * Get data
	 *
	 * @param array $target_sns Target SNS.
	 * @param array $options Option.
	 * @return array
	 */
	public function get_data( $target_sns, $options ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		SCC_Logger::log( $target_sns );

		$valid_target_sns = $this->check_configurations( $target_sns );
		SCC_Logger::log( $valid_target_sns );

		$request_http_methods = $this->get_http_methods( $valid_target_sns );
		SCC_Logger::log( $request_http_methods );

		$request_urls = $this->build_request_urls( $valid_target_sns );
		SCC_Logger::log( $request_urls );

		$request_headers = $this->build_request_headers( $valid_target_sns );
		SCC_Logger::log( $request_headers );

		$request_bodies = $this->build_request_bodies( $valid_target_sns );
		SCC_Logger::log( $request_bodies );

		$data = array();

		$throttle = new SCC_Sleep_Throttle( 0.9 );

		$throttle->reset();
		$throttle->start();

		if ( SNS_Count_Cache::OPT_COMMON_CRAWLER_METHOD_CURL === $this->crawl_method ) {
			$data = SCC_HTTP::multi_remote_request( $request_urls, $request_http_methods, $request_headers, $request_bodies, $this->timeout, $this->ssl_verification, true );
		} else {
			$data = SCC_HTTP::multi_remote_request( $request_urls, $request_http_methods, $request_headers, $request_bodies, $this->timeout, $this->ssl_verification, false );
		}

		$throttle->stop();

		if ( 0 < $this->retry_limit ) {

			$retry_count = 0;

			while ( true ) {
				$target_sns_retry = array();

				$tmp_count = $this->extract_response_bodies( $valid_target_sns, $data );

				foreach ( $valid_target_sns as $sns => $active ) {
					if ( $active ) {
						if ( -1 === $tmp_count[ $sns ] ) {
							$target_sns_retry[ $sns ] = true;
						}
					}
				}

				$target_sns_retry = $this->check_rate_limiting( $target_sns_retry, $data );

				if ( empty( $target_sns_retry ) ) {
					break;
				} else {
					SCC_Logger::log( '[' . __METHOD__ . '] crawl failure' );
					SCC_Logger::log( $target_sns_retry );

					if ( $retry_count < $this->retry_limit ) {

						SCC_Logger::log( '[' . __METHOD__ . '] sleep before crawl retry: ' . $throttle->get_sleep_time() . ' sec.' );

						$throttle->sleep();

						++$retry_count;

						SCC_Logger::log( '[' . __METHOD__ . '] count of crawl retry: ' . $retry_count );

						$request_http_methods_retry = $this->get_http_methods( $target_sns_retry );
						$request_urls_retry = $this->build_request_urls( $target_sns_retry );
						$request_headers_retry = $this->build_request_headers( $target_sns_retry );
						$request_bodies_retry = $this->build_request_bodies( $target_sns_retry );

						$data_retry = array();

						$throttle->reset();
						$throttle->start();

						if ( SNS_Count_Cache::OPT_COMMON_CRAWLER_METHOD_CURL === $this->crawl_method ) {
							$data_retry = SCC_HTTP::multi_remote_request( $request_urls_retry, $request_http_methods_retry, $request_headers_retry, $request_bodies_retry, $this->timeout, $this->ssl_verification, true );
						} else {
							$data_retry = SCC_HTTP::multi_remote_request( $request_urls_retry, $request_http_methods_retry, $request_headers_retry, $request_bodies_retry, $this->timeout, $this->ssl_verification, true );
						}

						$throttle->stop();

						$data = array_merge( (array) $data, (array) $data_retry );
					} else {
						SCC_Logger::log( '[' . __METHOD__ . '] crawl retry ended' );
						break;
					}
				} // End if().
			} // End while().
		} // End if().

		$response = array();

		$response['count'] = $this->extract_response_bodies( $target_sns, $data );
		$response['info'] = $this->extract_response_headers( $target_sns, $data );

		return $response;
	}

	/**
	 * Get HTTP method
	 *
	 * @param array $target_sns Target SNS.
	 * @return array
	 */
	private function get_http_methods( $target_sns ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$http_methods = array();

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		SCC_Logger::log( $target_sns );

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				$http_methods[ $sns ] = $this->crawl_strategies[ $sns ]->get_http_method();
			}
		}

		return $http_methods;
	}

	/**
	 * Build request URLs
	 *
	 * @param array $target_sns Target SNS.
	 * @return array
	 */
	private function build_request_urls( $target_sns ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$request_urls = array();

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		SCC_Logger::log( $target_sns );

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				$request_urls[ $sns ] = $this->crawl_strategies[ $sns ]->build_request_url();
			}
		}

		return $request_urls;
	}

	/**
	 * Build request bodies
	 *
	 * @param array $target_sns Target SNS.
	 * @return array
	 */
	private function build_request_bodies( $target_sns ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$request_contents = array();

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		SCC_Logger::log( $target_sns );

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				$request_contents[ $sns ] = $this->crawl_strategies[ $sns ]->build_request_body();
			}
		}

		return $request_contents;
	}

	/**
	 * Build request headers
	 *
	 * @param array $target_sns Target SNS.
	 * @return array
	 */
	private function build_request_headers( $target_sns ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$request_headers = array();

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		SCC_Logger::log( $target_sns );

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				$request_headers[ $sns ] = $this->crawl_strategies[ $sns ]->build_request_header();
			}
		}

		return $request_headers;
	}

	/**
	 * Extract response bodies
	 *
	 * @param array $target_sns Target SNS.
	 * @param array $contents Contents.
	 * @return void
	 */
	private function extract_response_bodies( $target_sns, $contents ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$sns_counts = array();

		$extract_date = date_i18n( 'Y/m/d H:i:s' );

		SCC_Logger::log( $contents );

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				if ( isset( $contents[ $sns ] ) ) {
					$sns_counts[ $sns ] = $this->crawl_strategies[ $sns ]->extract_response_body( $contents[ $sns ] );
				} else {
					$sns_counts[ $sns ] = (int) -1;
				}
			}
		}

		if ( isset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] ) && $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] ) {
			$sns_counts[ SNS_Count_Cache::REF_CRAWL_DATE ] = $extract_date;
		} else {
			$sns_counts[ SNS_Count_Cache::REF_CRAWL_DATE ] = '';
		}

		return $sns_counts;
	}

	/**
	 * Extract response headers
	 *
	 * @param array $target_sns Target SNS.
	 * @param array $contents Contents.
	 * @return array
	 */
	private function extract_response_headers( $target_sns, $contents ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$sns_headers = array();

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		SCC_Logger::log( $contents );

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				if ( isset( $contents[ $sns ] ) ) {
					$sns_headers[ $sns ] = $this->crawl_strategies[ $sns ]->extract_response_header( $contents[ $sns ] );
				} else {
					$sns_headers[ $sns ] = null;
				}
			}
		}

		return $sns_headers;
	}

	/**
	 * Check rate limiting
	 *
	 * @param array $target_sns Target SNS.
	 * @param array $contents Contents.
	 * @return array
	 */
	private function check_rate_limiting( $target_sns, $contents ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		unset( $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] );

		$checked_target_sns = array();

		foreach ( $target_sns as $sns => $active ) {
			if ( $active ) {
				$checked_target_sns[ $sns ] = $this->crawl_strategies[ $sns ]->check_rate_limiting( $contents[ $sns ] );
			}
		}

		// $target_sns[ SNS_Count_Cache::REF_CRAWL_DATE ] = true;
		// $target_sns[ SNS_Count_Cache::REF_SHARE_TOTAL ] = true;

		return $checked_target_sns;
	}

}

?>
