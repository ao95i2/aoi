<?php
/**
class-scc-follow-cache-engine.php

Description: This class is a data cache engine whitch get and cache data using wp-cron at regular intervals
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
 * SCC_Follow_Cache_Engine
 */
abstract class SCC_Follow_Cache_Engine extends SCC_Cache_Engine {


	/**
	 * Crawler instance
	 *
	 * @var SCC_Follow_Crawler
	 */
	protected $crawler = null;

	/**
	 * Cache target
	 *
	 * @var array
	 */
	protected $target_sns = array();

	/**
	 * Migration between http and https
	 *
	 * @var boolean
	 */
	protected $scheme_migration_mode = false;

	/**
	 * Excluded keys in scheme migration
	 *
	 * @var array
	 */
	protected $scheme_migration_exclude_keys = array();

	/**
	 * Load ratio for throttle
	 *
	 * @var integer
	 */
	protected $load_ratio = 0.5;

	/**
	 * Get and cache data of follower
	 *
	 * @param array $options Option.
	 * @return array
	 */
	public function cache( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$cache_key = $options['cache_key'];
		$target_sns = $options['target_sns'];
		$cache_expiration = $options['cache_expiration'];

		$response = $this->crawler->get_data( $target_sns, null );
		$sns_count = $response['count'];

		SCC_Logger::log( $sns_count );

		$target_sns_base = $target_sns;

		unset( $target_sns_base[ SNS_Count_Cache::REF_CRAWL_DATE ] );
		unset( $target_sns_base[ SNS_Count_Cache::REF_SHARE_TOTAL ] );

		$target_sns_url = array();

		$parameters = array();

		$parameters_origin = array();

		$secure_url_flag = false;

		foreach ( $target_sns_base as $sns => $active ) {
			if ( $active ) {
				$parameters[ $sns ] = $this->crawler->get_crawl_strategy_parameters( $sns );
				SCC_Logger::log( $parameters[ $sns ] );
				if ( isset( $parameters[ $sns ]['url'] ) ) {
					$target_sns_url[ $sns ] = true;
					SCC_Logger::log( $parameters[ $sns ] );
					if ( SCC_Format::is_https_url( $parameters[ $sns ]['url'] ) ) {
						$secure_url_flag = true;
					}
				} elseif ( isset( $parameters[ $sns ]['urls'] ) ) {
					$target_sns_url[ $sns ] = true;
					SCC_Logger::log( $parameters[ $sns ] );
					if ( SCC_Format::is_https_url( $parameters[ $sns ]['urls'] ) ) {
						$secure_url_flag = true;
					}
				}
			}
		}

		foreach ( $target_sns_url as $sns => $active ) {
			if ( $active ) {
				$parameters_origin[ $sns ] = $this->crawler->get_crawl_strategy_parameters( $sns );
			}
		}

		if ( $this->scheme_migration_mode && $secure_url_flag ) {

			foreach ( $target_sns_url as $sns => $active ) {
				if ( $active ) {
					if ( isset( $parameters[ $sns ]['url'] ) ) {
						$parameters[ $sns ]['url'] = SCC_Format::get_http_url( $parameters[ $sns ]['url'] );
					} elseif ( isset( $parameters[ $sns ]['urls'] ) ) {
						$parameters[ $sns ]['urls'] = SCC_Format::get_http_url( $parameters[ $sns ]['urls'] );
					}
				}
			}

			$target_sns_migrated = $target_sns_url;

			foreach ( $this->scheme_migration_exclude_keys as $sns ) {
				unset( $target_sns_migrated[ $sns ] );
			}

			foreach ( $target_sns_url as $sns => $active ) {
				if ( $active ) {
					$this->crawler->set_crawl_strategy_parameters( $sns, $parameters[ $sns ] );
				}
			}

			$migrated_sns_count = $this->crawler->get_data( $target_sns_migrated, null );

			SCC_Logger::log( $migrated_sns_count );

			foreach ( $target_sns_migrated as $sns => $active ) {
				if ( $active && isset( $migrated_sns_count[ $sns ] ) && is_numeric( $migrated_sns_count[ $sns ] ) && $migrated_sns_count[ $sns ] > 0 ) {
					$sns_count[ $sns ] = $sns_count[ $sns ] + $migrated_sns_count[ $sns ];
				}
			}

			foreach ( $target_sns_url as $sns => $active ) {
				if ( $active ) {
					$this->crawler->set_crawl_strategy_parameters( $sns, $parameters_origin[ $sns ] );
				}
			}
		}

		SCC_Logger::log( $sns_count );

		if ( $sns_count ) {
			$result = set_transient( $cache_key, $sns_count, $cache_expiration );
		}

		/*
		if ( $sns_count ) {
			$throttle = new SCC_Sleep_Throttle( $this->load_ratio );

			$throttle->reset();
			$throttle->start();

			$result = set_transient( $cache_key, $sns_count, $cache_expiration );

			$throttle->stop();

			$retry_count = 0;

			while ( true ) {
				SCC_Logger::log( '[' . __METHOD__ . '] set_transient result (' . $cache_key . '): ' . $result );

				if ( $result ) {
					break;
				} else {
					if ( $retry_count < $this->retry_limit ) {
						SCC_Logger::log( '[' . __METHOD__ . '] sleep before set_transient retry (' . $cache_key . '): ' . $throttle->get_sleep_time() . ' sec.' );

						$throttle->sleep();

						++$retry_count;

						SCC_Logger::log( '[' . __METHOD__ . '] count of set_transient retry (' . $cache_key . '): ' . $retry_count );

						$throttle->reset();
						$throttle->start();

						$result = set_transient( $cache_key, $sns_count, $cache_expiration );

						$throttle->stop();
					} else {
						SCC_Logger::log( '[' . __METHOD__ . '] set_transient result (' . $cache_key . '): retry failed' );
						break;
					}
				}
			}
		} // End if().
		*/

		return $sns_count;
	}

}

?>
