<?php
/**
class-scc-share-restore-cache-engine.php

Description: This class is a data cache engine whitch restore cache data from second cache.
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
 * SCC_Share_Restore_Cache_Engine
 */
class SCC_Share_Restore_Cache_Engine extends SCC_Cache_Engine {

	/**
	 * Prefix of cache ID
	 */
	const DEF_TRANSIENT_PREFIX = 'scc_share_count_';

	/**
	 * Cron name to schedule cache processing
	 */
	const DEF_PRIME_CRON = 'scc_share_restorecache_prime';

	/**
	 * Cron name to execute cache processing
	 */
	const DEF_EXECUTE_CRON = 'scc_share_restorecache_exec';

	/**
	 * Schedule name for cache processing
	 */
	const DEF_EVENT_SCHEDULE = 'share_restore_cache_event';

	/**
	 * Schedule description for cache processing
	 */
	const DEF_EVENT_DESCRIPTION = '[SCC] Share Restore Cache Interval';

	/**
	 * Interval cheking and caching target data
	 *
	 * @var integer
	 */
	private $check_interval = 600;

	/**
	 * Number of posts to check at a time
	 *
	 * @var integer
	 */
	private $posts_per_check = 20;

	/**
	 * Latency suffix
	 *
	 * @var integer
	 */
	private $check_latency = 10;

	/**
	 * Cache post types
	 *
	 * @var array
	 */
	private $post_types = array( 'post', 'page' );

	/**
	 * Initialization
	 *
	 * @param array $options Option.
	 * @return void
	 */
	public function initialize( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$this->cache_prefix = self::DEF_TRANSIENT_PREFIX;
		$this->prime_cron = self::DEF_PRIME_CRON;
		$this->execute_cron = self::DEF_EXECUTE_CRON;
		$this->event_schedule = self::DEF_EVENT_SCHEDULE;
		$this->event_description = self::DEF_EVENT_DESCRIPTION;

		if ( isset( $options['target_sns'] ) ) {
			$this->target_sns = $options['target_sns'];
		}

		if ( isset( $options['check_interval'] ) ) {
			$this->check_interval = $options['check_interval'];
		}

		if ( isset( $options['posts_per_check'] ) ) {
			$this->posts_per_check = $options['posts_per_check'];
		}

		if ( isset( $options['cache_prefix'] ) ) {
			$this->cache_prefix = $options['cache_prefix'];
		}

		if ( isset( $options['execute_cron'] ) ) {
			$this->execute_cron = $options['execute_cron'];
		}

		if ( isset( $options['check_latency'] ) ) {
			$this->check_latency = $options['check_latency'];
		}

		if ( isset( $options['post_types'] ) ) {
			$this->post_types = $options['post_types'];
		}

		add_action( $this->execute_cron, array( $this, 'execute_cache' ), 10, 1 );
	}

	/**
	 * Register base schedule for this engine
	 *
	 * @return void
	 */
	public function register_schedule() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Unregister base schedule for this engine
	 *
	 * @return void
	 */
	public function unregister_schedule() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		SCC_WP_Cron::clear_scheduled_hook( $this->execute_cron );
	}

	/**
	 * Schedule cache restoring
	 *
	 * @param string $post_id Post ID.
	 * @return void
	 */
	public function prime_cache( $post_id ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$next_exec_time = (int) current_time( 'timestamp', 1 ) + $this->check_latency;

		SCC_Logger::log( '[' . __METHOD__ . '] check_latency: ' . $this->check_latency );
		SCC_Logger::log( '[' . __METHOD__ . '] next_exec_time: ' . $next_exec_time );

		if ( 'home' === $post_id ) {
			wp_schedule_single_event( $next_exec_time, $this->execute_cron, array( $post_id ) );
		} elseif ( is_numeric( $post_id ) ) {
			wp_schedule_single_event( $next_exec_time, $this->execute_cron, array( (int) $post_id ) );
		}
	}

	/**
	 * Restore cache
	 *
	 * @param string $post_id Post ID,
	 * @return array
	 */
	public function execute_cache( $post_id ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$cache_expiration = $this->get_cache_expiration();

		SCC_Logger::log( '[' . __METHOD__ . '] cache_expiration: ' . $cache_expiration );

		$transient_id = $this->get_cache_key( $post_id );

		$options = array(
			'cache_key' => $transient_id,
			'post_id' => $post_id,
			'target_sns' => $this->target_sns,
			'cache_expiration' => $cache_expiration,
		);

		$this->cache( $options );
	}

	/**
	 * Restore cache
	 *
	 * @param array $options Option.
	 * @return void
	 */
	public function cache( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$transient_id = $options['cache_key'];
		$target_sns = $options['target_sns'];
		$post_id = $options['post_id'];
		$cache_expiration = $options['cache_expiration'];

		$sns_counts = array();

		if ( 'home' !== $post_id ) {
			foreach ( $target_sns as $sns => $active ) {
				if ( $active ) {
					$meta_key = $this->get_cache_key( $sns );

					$sns_count = get_post_meta( $post_id, $meta_key, true );

					if ( isset( $sns_count ) && '' !== $sns_count ) {
						if ( is_numeric( $sns_count ) ) {
							$sns_counts[ $sns ] = (int) $sns_count;
						} else {
							$sns_counts[ $sns ] = $sns_count;
						}
					} else {

					}
				}
			}
		} else {
			$option_key = $this->get_cache_key( 'home' );

			$sns_counts = get_option( $option_key );

			if ( false !== $sns_counts ) {
			} else {
				foreach ( $this->share_base_cache_target as $sns => $active ) {
					if ( $active ) {
						$sns_counts[ $sns ] = (int) -1;
					}
				}
			}
		}

		$result = set_transient( $transient_id, $sns_counts, $cache_expiration );
	}

	/**
	 * Get cache expiration based on current number of total post and page
	 *
	 * @return integer
	 */
	protected function get_cache_expiration() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$posts_total = $this->get_posts_total();

		SCC_Logger::log( '[' . __METHOD__ . '] posts_total: ' . $posts_total );

		return ceil( $posts_total / $this->posts_per_check ) * $this->check_interval * 3;
	}

	/**
	 * Get total count of current published post and page
	 *
	 * @return integer
	 */
	private function get_posts_total() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$query_args = array(
			'post_type' => $this->post_types,
			'post_status' => 'publish',
			'nopaging' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			);

		$posts_query = new WP_Query( $query_args );

		return $posts_query->found_posts;
	}

	/**
	 * Initialize cache
	 *
	 * @return void
	 */
	public function initialize_cache() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Clear cache
	 *
	 * @return void
	 */
	public function clear_cache() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );
	}

	/**
	 * Get cache
	 *
	 * @param array $options Option.
	 * @return array
	 */
	public function get_cache( $options = array() ) {
		$target_sns = $options['target_sns'];
		$post_id = $options['post_id'];

		$sns_counts = array();

		if ( 'home' !== $post_id ) {
			foreach ( $target_sns as $sns => $active ) {
				if ( $active ) {
					$meta_key = $this->get_cache_key( $sns );

					$sns_count = get_post_meta( $post_id, $meta_key, true );

					if ( isset( $sns_count ) && '' !== $sns_count ) {
						if ( is_numeric( $sns_count ) ) {
							$sns_counts[ $sns ] = (int) $sns_count;
						} else {
							$sns_counts[ $sns ] = $sns_count;
						}
					} else {

					}
				}
			}
		} else {
			$option_key = $this->get_cache_key( 'home' );

			$sns_counts = get_option( $option_key );

			if ( false !== $sns_counts ) {
			} else {
				foreach ( $this->share_base_cache_target as $sns => $active ) {
					if ( $active ) {
						$sns_counts[ $sns ] = (int) -1;
					}
				}
			}
		}

		return $sns_counts;
	}

}

?>
