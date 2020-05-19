<?php
/**
class-scc-share-app-status-engine.php

Description:
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
 * SCC_Share_App_Status_Engine
 */
class SCC_Share_App_Status_Engine extends SCC_Engine {

	/**
	 * Cron name to schedule cache processing
	 */
	const DEF_PRIME_CRON = 'scc_share_appstatus_prime';

	/**
	 * Cron name to execute cache processing
	 */
	const DEF_EXECUTE_CRON = 'scc_share_appstatus_exec';

	/**
	 * Schedule name for cache processing
	 */
	const DEF_EVENT_SCHEDULE = 'share_app_status_event';

	/**
	 * Schedule description for cache processing
	 */
 	const DEF_EVENT_DESCRIPTION = '[SCC] Share App Status Interval';

	/**
	 * Interval cheking and caching target data
	 *
	 * @var integer
	 */
	private $check_interval = 600;

	/**
	 * Initialization
	 *
	 * @param array $options Option.
	 * @return void
	 */
	public function initialize( $options = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$this->prime_cron = self::DEF_PRIME_CRON;
		$this->execute_cron = self::DEF_EXECUTE_CRON;
		$this->event_schedule = self::DEF_EVENT_SCHEDULE;
		$this->event_description = self::DEF_EVENT_DESCRIPTION;

		$this->load_ratio = 0.5;

		if ( isset( $options['delegate'] ) ) {
			$this->delegate = $options['delegate'];
		}

		if ( isset( $options['crawler'] ) ) {
			$this->crawler = $options['crawler'];
		}

		if ( isset( $options['target_sns'] ) ) {
			$this->target_sns = $options['target_sns'];
		}

		if ( isset( $options['check_interval'] ) ) {
			$this->check_interval = $options['check_interval'];
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

		add_filter( 'cron_schedules', array( $this, 'schedule_check_interval' ) );
		add_action( $this->prime_cron, array( $this, 'prime_check' ) );
		add_action( $this->execute_cron, array( $this, 'execute_check' ), 10, 0 );
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
	 * Schedule app status ckeck
	 *
	 * @return void
	 */
	public function prime_check() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$next_exec_time = (int) current_time( 'timestamp', 1 ) + $this->check_interval;

		SCC_Logger::log( '[' . __METHOD__ . '] check_interval: ' . $this->check_interval );
		SCC_Logger::log( '[' . __METHOD__ . '] next_exec_time: ' . $next_exec_time );

		wp_schedule_single_event( $next_exec_time, $this->execute_cron );

	}

	/**
	 * Check app status
	 *
	 * @return void
	 */
	public function execute_check() {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		SCC_Logger::log( '[' . __METHOD__ . '] check_interval: ' . $this->check_interval );

		$url = home_url( '/' );
		$title = get_bloginfo( 'name' );

		$target_option = array(
			'target_url' => $url,
			'target_title' => $title,
		);

		$response = $this->get_data( $this->target_sns, $target_option );

		$app_info = $response['info'];

		if ( ! empty( $app_info ) ) {
			update_option( SNS_Count_Cache::DB_SHARE_APP_STATUS, $app_info );
		} else {
			delete_option( SNS_Count_Cache::DB_SHARE_APP_STATUS );
		}

	}

	/**
	 * Get data
	 *
	 * @param array $target_sns Target SNS.
	 * @param array $target_option Target Option.
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

		$options = array(
			'app_status_check' => false,
		);

		$this->crawler->set_option( $options );

		foreach ( $target_sns_base as $sns => $active ) {
			if ( $active ) {
				$this->crawler->set_crawl_strategy_parameters( $sns, $parameters );
			}
		}

		return $this->crawler->get_data( $target_sns, null );
	}

	/**
	 * Check nessesary of throttle processing
	 *
	 * @param array $options Option.
	 * @return boolean
	 */
	public function need_throttle( $options ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		$expected_call_count = 0;

		if ( isset( $options['expected_call_count'] ) && is_numeric( $options['expected_call_count'] ) ) {
			$expected_call_count = (int) $options['expected_call_count'];
		}

		$app_info = get_option( SNS_Count_Cache::DB_SHARE_APP_STATUS );

		$need_throttle = array();

		if ( false !== $app_info ) {
			if ( ! empty( $app_info[ SNS_Count_Cache::REF_SHARE_FACEBOOK ][ strtolower( 'X-App-Usage' ) ]['call_count'] ) ) {
				$current_call_count = (int) $app_info[ SNS_Count_Cache::REF_SHARE_FACEBOOK ][ strtolower( 'X-App-Usage' ) ]['call_count'];

				SCC_Logger::log( '[' . __METHOD__ . '] current_call_count: ' . $current_call_count );
				SCC_Logger::log( '[' . __METHOD__ . '] expected_call_count: ' . $expected_call_count );

				$threshold = 100;

				if ( $current_call_count + $expected_call_count > $threshold ) {
					$need_throttle[ SNS_Count_Cache::REF_SHARE_FACEBOOK ] = true;
				} else {
					$need_throttle[ SNS_Count_Cache::REF_SHARE_FACEBOOK ] = false;
				}
			} else {
				$need_throttle[ SNS_Count_Cache::REF_SHARE_FACEBOOK ] = false;
			}
		} else {
			$need_throttle[ SNS_Count_Cache::REF_SHARE_FACEBOOK ] = false;
		}

		return $need_throttle;
	}

	/**
	 * Update aplication status
	 *
	 * @param array $app_info Aplication information.
	 * @return void
	 */
	public function update_app_status( $app_info = array() ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		if ( ! empty( $app_info ) ) {
			update_option( SNS_Count_Cache::DB_SHARE_APP_STATUS, $app_info );
		}
	}

}

?>
