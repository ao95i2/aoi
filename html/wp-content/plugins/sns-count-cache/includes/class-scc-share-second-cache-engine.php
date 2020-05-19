<?php
/**
class-scc-share-second-cache-engine.php

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
 * SCC_Share_Second_Cache_Engine
 */
class SCC_Share_Second_Cache_Engine extends SCC_Cache_Engine {

	/**
	 * Prefix of cache ID
	 */
	const DEF_TRANSIENT_PREFIX = 'scc_share_count_';

	/**
	 * Cron name to schedule cache processing
	 */
	const DEF_PRIME_CRON = 'scc_share_2ndcache_prime';

	/**
	 * Cron name to execute cache processing
	 */
	const DEF_EXECUTE_CRON = 'scc_share_2ndcache_exec';

	/**
	 * Schedule name for cache processing
	 */
	const DEF_EVENT_SCHEDULE = 'share_second_cache_event';

	/**
	 * Schedule description for cache processing
	 */
	const DEF_EVENT_DESCRIPTION = '[SCC] Share Second Cache Interval';

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
	 * Cache target
	 *
	 * @var array
	 */
	private $target_sns = array();

	/**
	 * Cache post types
	 *
	 * @var array
	 */
	private $post_types = array( 'post', 'page' );

	/**
	 * Crawl date key
	 *
	 * @var array
	 */
	private $crawl_date_key = null;

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

		if ( isset( $options['delegate'] ) ) {
			$this->delegate = $options['delegate'];
		}

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

		if ( isset( $options['prime_cron'] ) ) {
			$this->prime_cron = $options['prime_cron'];
		}

		if ( isset( $options['execute_cron'] ) ) {
			$this->execute_cron = $options['execute_cron'];
		}

		if ( isset( $options['event_schedule'] ) ) {
			$this->event_schedule = $options['event_schedule'];
		}

		if ( isset( $options['event_description'] ) ) {
			$this->event_description = $options['event_description'];
		}

		if ( isset( $options['post_types'] ) ) {
			$this->post_types = $options['post_types'];
		}

		if ( isset( $options['crawl_date_key'] ) ) {
			$this->crawl_date_key = $options['crawl_date_key'];
		}

		add_filter( 'cron_schedules', array( $this, 'schedule_check_interval' ) );
		add_action( $this->prime_cron, array( $this, 'prime_cache' ) );
		add_action( $this->execute_cron, array( $this, 'execute_cache' ), 10, 0 );
	}

	/**
	 * Register event schedule for this engine
	 *
	 * @param array $schedules Schedule.
	 * @return array
	 */
	public function schedule_check_interval( $schedules ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$schedules[ $this->event_schedule ] = array(
			'interval' => $this->check_interval,
			'display' => $this->event_description,
			);

		return $schedules;
	}

	/**
	 * Schedule data retrieval and cache processing
	 *
	 * @return void
	 */
	public function prime_cache() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$next_exec_time = (int) current_time( 'timestamp', 1 ) + $this->check_interval;

		SCC_Logger::log( '[' . __METHOD__ . '] check_interval: ' . $this->check_interval );
		SCC_Logger::log( '[' . __METHOD__ . '] next_exec_time: ' . $next_exec_time );

		wp_schedule_single_event( $next_exec_time, $this->execute_cron );
	}

	/**
	 * Get and cache data of each published post and page
	 *
	 * @param integer $posts_offset Post offset.
	 * @return void
	 */
	public function execute_cache() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$query_args = array(
			'post_type' => $this->post_types,
			'post_status' => 'publish',
			'nopaging' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			);

		$posts_query = new WP_Query( $query_args );

		if ( $posts_query->have_posts() ) {
			while ( $posts_query->have_posts() ) {
				$posts_query->the_post();

				$post_id = get_the_ID();

				$transient_id = $this->get_cache_key( $post_id );

				$url = get_permalink( $post_id );

				$options = array(
					'cache_key' => $transient_id,
					'post_id' => $post_id,
					'target_sns' => $this->target_sns,
					);

				$this->cache( $options );
			}
		}
		wp_reset_postdata();

	}

	/**
	 * Get and cache data for a given post
	 *
	 * @param integer $post_id Post ID.
	 * @param boolean $second_sync Second sync flag.
	 * @return array
	 */
	public function cache( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$transient_id = $options['cache_key'];
		$target_sns = $options['target_sns'];
		$post_id = $options['post_id'];

		$sns_counts = array();

		if ( 'home' !== $post_id ) {

			$sns_counts = get_transient( $transient_id );

			if ( false !== $sns_counts ) {
				foreach ( $target_sns as $sns => $active ) {
					if ( $active ) {
						$meta_key = $this->get_cache_key( $sns );

						if ( $sns !== $this->crawl_date_key ) {

							if ( isset( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
								update_post_meta( $post_id, $meta_key, (int) $sns_counts[ $sns ] );
							} else {
								update_post_meta( $post_id, $meta_key, (int) -1 );
							}
						} else {
							if ( isset( $sns_counts[ $sns ] ) && '' !== $sns_counts[ $sns ] ) {
								update_post_meta( $post_id, $meta_key, $sns_counts[ $sns ] );
							} else {
								update_post_meta( $post_id, $meta_key, '' );
							}
						}
					}
				}
			}
		} else {
			$sns_counts = get_transient( $transient_id );

			if ( false !== $sns_counts ) {

				$option_key = $this->get_cache_key( 'home' );

				foreach ( $target_sns as $sns => $active ) {
					if ( $active ) {
						if ( $sns !== $this->crawl_date_key ) {
							if ( ! isset( $sns_counts[ $sns ] ) || 0 > $sns_counts[ $sns ] ) {
								$sns_counts[ $sns ] = (int) -1;
							}
						} else {
							if ( ! isset( $sns_counts[ $sns ] ) || '' === $sns_counts[ $sns ] ) {
								$sns_counts[ $sns ] = '';
							}
						}
					}
				}

				update_option( $option_key, $sns_counts );
			}
		}

		$this->delegate_order( SCC_Order::ORDER_DO_ANALYSIS, $options );

	}

	/**
	 * Get cache expiration based on current number of total post and page
	 *
	 * @return integer
	 */
	protected function get_cache_expiration() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		return 0;
	}

	/**
	 * Initialize cache
	 *
	 * @return void
	 */
	public function initialize_cache() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$option_key = $this->get_cache_key( 'home' );

		$sns_counts = array();

		foreach ( $this->target_sns as $sns => $active ) {
			if ( $active ) {
				$sns_counts[ $sns ] = (int) -1;
			}
		}

		update_option( $option_key, $sns_counts );

		$query_args = array(
			'post_type' => $this->post_types,
			'post_status' => 'publish',
			'nopaging' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			);

		$posts_query = new WP_Query( $query_args );

		if ( $posts_query->have_posts() ) {
			while ( $posts_query->have_posts() ) {
				$posts_query->the_post();

				$post_id = get_the_ID();

				foreach ( $this->target_sns as $sns => $active ) {
					$meta_key = $this->get_cache_key( $sns );

					if ( $active ) {
						update_post_meta( $post_id, $meta_key, (int) -1 );
					}
				}
			}
		}
		wp_reset_postdata();
	}

	/**
	 * Get cache
	 *
	 * @param array $options Option.
	 * @return array
	 */
	public function get_cache( $options = array() ) {
		$post_id = $options['post_id'];
		$sns_counts = array();

		if ( 'home' !== $post_id ) {
			foreach ( $this->target_sns as $sns => $active ) {
				if ( $active ) {
					$meta_key = $this->get_cache_key( $sns );
					$sns_counts[ $sns ] = get_post_meta( $post_id, $meta_key, true );
				}
			}
		} else {
			$option_key = $this->get_cache_key( 'home' );
			$sns_counts = get_option( $option_key );
		}
		return $sns_counts;
	}

	/**
	 * Clear cache
	 *
	 * @return void
	 */
	public function clear_cache() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$option_key = $this->get_cache_key( 'home' );
		delete_option( $option_key );

		foreach ( $this->target_sns as $sns => $active ) {
			if ( $active ) {
				$meta_key = $this->get_cache_key( $sns );
				delete_post_meta_by_key( $meta_key );
			}
		}

		// Processing for Google+ count removal
		$meta_key = $this->get_cache_key( SNS_Count_Cache::REF_SHARE_GPLUS );
		delete_post_meta_by_key( $meta_key );
	}

    /**
	 * Clear cache
	 *
	 * @param integer $post_id Post ID.
	 * @return void
	 */
	public function clear_cache_by_post_id( $post_id ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		foreach ( $this->target_sns as $sns => $active ) {
			if ( $active ) {
				$meta_key = $this->get_cache_key( $sns );
				delete_post_meta( $post_id, $meta_key );
			}
		}

		// Processing for Google+ count removal
		$meta_key = $this->get_cache_key( SNS_Count_Cache::REF_SHARE_GPLUS );
		delete_post_meta_by_key( $meta_key );

	}

}

?>
