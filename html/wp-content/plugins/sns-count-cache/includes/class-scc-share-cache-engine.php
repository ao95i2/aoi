<?php
/**
class-scc-share-cache-engine.php

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
 * SCC_Share_Cache_Engine
 */
abstract class SCC_Share_Cache_Engine extends SCC_Cache_Engine {

	/**
	 * Crawler instance
	 *
	 * @var SCC_Share_Crawler
	 */
	protected $crawler = null;

	/**
	 * Cache target
	 *
	 * @var array
	 */
	protected $target_sns = array();

	/**
	 * Migration mode from http to https
	 *
	 * @var boolean
	 */
	protected $scheme_migration_mode = false;

	/**
	 * Migration date from http to https
	 *
	 * @var string
	 */
	protected $scheme_migration_date = null;

	/**
	 * Excluded keys in scheme migration
	 *
	 * @var array
	 */
	protected $scheme_migration_exclude_keys = array();

	/**
	 * Load ratio for throttle
	 *
	 * @var float
	 */
	protected $load_ratio = 0.5;

	/**
	 * Get and cache data for a given post
	 *
	 * @param array $options Option.
	 * @return array
	 */
	public function cache( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$cache_key = $options['cache_key'];
		$target_url = $options['target_url'];
		$target_sns = $options['target_sns'];
		$cache_expiration = $options['cache_expiration'];
		$publish_date = $options['publish_date'];
		$target_title = $options['target_title'];

		SCC_Logger::log( '[' . __METHOD__ . '] current memory usage: ' . round( memory_get_usage( true ) / 1024 / 1024, 2 ) . ' MB' );
		SCC_Logger::log( '[' . __METHOD__ . '] target url: ' . $target_url );

		$target_option = array(
			'target_url' => $target_url,
			'target_title' => $target_title,
		);

		$response = $this->get_data( $target_sns, $target_option );
		$sns_count = $response['count'];
		$sns_info = $response['info'];

		SCC_Logger::log( $sns_count );
		SCC_Logger::log( '[' . __METHOD__ . '] scheme migration date: ' . $this->scheme_migration_date );
		SCC_Logger::log( '[' . __METHOD__ . '] publish date: ' . $publish_date );

		if ( $this->scheme_migration_mode && SCC_Format::is_https_url( $target_url ) ) {
			if ( ! isset( $this->scheme_migration_date ) ) {
				$target_normal_url = SCC_Format::get_http_url( $target_url );
				$target_sns_migrated = $target_sns;

				foreach ( $this->scheme_migration_exclude_keys as $sns ) {
					unset( $target_sns_migrated[ $sns ] );
				}

				SCC_Logger::log( '[' . __METHOD__ . '] target url: ' . $target_normal_url );

				$target_option = array(
					'target_url' => $target_normal_url,
					'target_title' => $target_title,
				);

				$migrated_response = $this->get_data( $target_sns_migrated, $target_option );

				$migrated_sns_count = $migrated_response['count'];
				$migrated_sns_info = $migrated_response['info'];

				SCC_Logger::log( $migrated_sns_count );

				foreach ( $target_sns_migrated as $sns => $active ) {
					if ( $active && isset( $migrated_sns_count[ $sns ] ) && is_numeric( $migrated_sns_count[ $sns ] ) && 0 < $migrated_sns_count[ $sns ] ) {
						$sns_count[ $sns ] = $sns_count[ $sns ] + $migrated_sns_count[ $sns ];
					}
				}
			} else {
				if ( isset( $publish_date ) ) {
					if ( strtotime( $publish_date ) <= strtotime( $this->scheme_migration_date ) ) {
						$target_normal_url = SCC_Format::get_http_url( $target_url );
						$target_sns_migrated = $target_sns;

						foreach ( $this->scheme_migration_exclude_keys as $sns ) {
							unset( $target_sns_migrated[ $sns ] );
						}

						SCC_Logger::log( '[' . __METHOD__ . '] target url: ' . $target_normal_url );

						$target_option = array(
							'target_url' => $target_normal_url,
							'target_title' => $target_title,
						);

						$migrated_response = $this->get_data( $target_sns_migrated, $target_option );

						$migrated_sns_count = $migrated_response['count'];
						$migrated_sns_info = $migrated_response['info'];

						SCC_Logger::log( $migrated_sns_count );

						foreach ( $target_sns_migrated as $sns => $active ) {
							if ( $active && isset( $migrated_sns_count[ $sns ] ) && is_numeric( $migrated_sns_count[ $sns ] ) && 0 < $migrated_sns_count[ $sns ] ){
								$sns_count[ $sns ] = $sns_count[ $sns ] + $migrated_sns_count[ $sns ];
							}
						}
					}
				} else {
					$target_normal_url = SCC_Format::get_http_url( $target_url );
					$target_sns_migrated = $target_sns;

					foreach ( $this->scheme_migration_exclude_keys as $sns ) {
						unset( $target_sns_migrated[ $sns ] );
					}

					SCC_Logger::log( '[' . __METHOD__ . '] target url: ' . $target_url );

					$target_option = array(
						'target_url' => $target_url,
						'target_title' => $target_title,
					);

					$migrated_response = $this->get_data( $target_sns_migrated, $target_option );

					$migrated_sns_count = $migrated_response['count'];
					$migrated_sns_info = $migrated_response['info'];

					SCC_Logger::log( $migrated_sns_count );

					foreach ( $target_sns_migrated as $sns => $active ) {
						if ( $active && isset( $migrated_sns_count[ $sns ] ) && is_numeric( $migrated_sns_count[ $sns ] ) && 0 < $migrated_sns_count[ $sns ] ) {
							$sns_count[ $sns ] = $sns_count[ $sns ] + $migrated_sns_count[ $sns ];
						}
					}
				}
			}

			$target_sns_param = $target_sns;

			unset( $target_sns_param[ SNS_Count_Cache::REF_CRAWL_DATE ] );
			unset( $target_sns_param[ SNS_Count_Cache::REF_SHARE_TOTAL ] );

			$query_parameters = array(
				'url' => $target_url,
			);

			foreach ( $target_sns_param as $sns => $active ) {
				if ( $active ) {
					$this->crawler->set_crawl_strategy_parameters( $sns, $query_parameters );
				}
			}

			$sns_info = array_merge( (array) $sns_info, (array) $migrated_sns_info );
		} // End if().

		SCC_Logger::log( '[' . __METHOD__ . '] fault tolerance mode: ' . $this->fault_tolerance_mode );

		if ( SNS_Count_Cache::OPT_COMMON_FAULT_TOLERANCE_ON === $this->fault_tolerance_mode ) {

			$target_sns_tmp = $target_sns;

			unset( $target_sns_tmp[ SNS_Count_Cache::REF_CRAWL_DATE ] );
			unset( $target_sns_tmp[ SNS_Count_Cache::REF_SHARE_TOTAL ] );

			SCC_Logger::log( '[' . __METHOD__ . '] retrieved SNS count: ' );
			SCC_Logger::log( $sns_count );

			$target_sns_tolerance = array();

			foreach ( $target_sns_tmp as $sns => $active ) {
				if ( $active ) {
					if ( ! SCC_Cache::has_value( $sns_count[ $sns ] ) || 0 > $sns_count[ $sns ] ) {
						$target_sns_tolerance[ $sns ] = true;
						SCC_Logger::log( '[' . __METHOD__ . '] check count existence[ ' . $sns . ']: true' );
					} else {
						SCC_Logger::log( '[' . __METHOD__ . '] check count existence[ ' . $sns . ']: false' );
					}
				}
			}

			SCC_Logger::log( '[' . __METHOD__ . '] checked result of count existence: ' );
			SCC_Logger::log( $target_sns_tolerance );

			if ( ! empty( $target_sns_tolerance ) ) {
				$current_sns_count = $this->get_cache( $options );

				SCC_Logger::log( '[' . __METHOD__ . '] current SNS count: ' );
				SCC_Logger::log( $current_sns_count );

				foreach ( $target_sns_tolerance as $sns => $active ) {
					if ( $active ) {
						if ( SCC_Cache::has_value( $current_sns_count[ $sns ] ) && 0 <= $current_sns_count[ $sns ] ) {
							$sns_count[ $sns ] = $current_sns_count[ $sns ];
							SCC_Logger::log( '[' . __METHOD__ . '] replaced by current count: ' . $sns );
						}
					}
				}

				$total = 0;

				foreach ( $target_sns_tmp as $sns => $active ) {
					if ( $active ) {
						if ( isset( $sns_count[ $sns ] ) && 0 <= $sns_count[ $sns ] ) {
							$total = $total + $sns_count[ $sns ];
						}
					}
				}

				$sns_count[ SNS_Count_Cache::REF_SHARE_TOTAL ] = $total;
			}
		} // End if().

		SCC_Logger::log( $sns_count );

		if ( ! empty( $sns_count ) ) {
			$result = set_transient( $cache_key, $sns_count, $cache_expiration );
		}

		if ( ! empty( $sns_info ) ) {
			update_option( SNS_Count_Cache::DB_SHARE_APP_STATUS, $sns_info );
		} else {
			delete_option( SNS_Count_Cache::DB_SHARE_APP_STATUS );
		}

		/*
		if ( $data ) {
			$throttle = new SCC_Sleep_Throttle( $this->load_ratio );

			$throttle->reset();
			$throttle->start();

			$result = set_transient( $cache_key, $data, $cache_expiration );
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

						$result = set_transient( $cache_key, $data, $cache_expiration );

						$throttle->stop();
					} else {
						SCC_Logger::log( '[' . __METHOD__ . '] set_transient result (' . $cache_key . '): retry failed' );
						break;
					}
				}
			}
		} // End if().
		*/

		SCC_Logger::log( '[' . __METHOD__ . '] current memory usage: ' . SCC_Memory::get_current_usage() . ' MB' );
		SCC_Logger::log( '[' . __METHOD__ . '] max memory usage: ' . SCC_Memory::get_peak_usage() . ' MB' );

		return $sns_count;
	}

	/**
	 * Get data
	 *
	 * @param array $target_sns Target SNS.
	 * @param array $target_option Target option.
	 * @return array
	 */
	private function get_data( $target_sns, $target_option ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$target_sns_base = $target_sns;

		$target_url = $target_option['target_url'];
		$target_title = $target_option['target_title'];

		unset( $target_sns_base[ SNS_Count_Cache::REF_CRAWL_DATE ] );
		unset( $target_sns_base[ SNS_Count_Cache::REF_SHARE_TOTAL ] );

		$parameters = array(
			'url' => $target_url,
			'title' => $target_title,
		);

		foreach ( $target_sns_base as $sns => $active ) {
			if ( $active ) {
				$this->crawler->set_crawl_strategy_parameters( $sns, $parameters );
			}
		}

		return $this->crawler->get_data( $target_sns, null );
	}

}

?>
