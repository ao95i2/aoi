<?php
/**
admin-setting.php

Description: Option page implementation
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

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly
}

$tmp_instagram_access_token = '';
$tmp_facebook_access_token = '';
$tmp_twitter_bearer_token = '';

if ( ! empty( $_POST['_wpnonce'] ) && check_admin_referer( __FILE__, '_wpnonce' ) ) {
	if ( current_user_can( self::OPT_COMMON_CAPABILITY ) ) {
		if ( isset( $_POST['update_all_options'] ) && __( 'Update All Options', self::DOMAIN ) === wp_unslash( $_POST['update_all_options'] ) ) {

			$wp_error = new WP_Error();

			$settings = get_option( self::DB_SETTINGS );

			$share_base_cache_target = array();
			$follow_base_cache_target = array();

			if ( ! empty( $_POST['share_base_custom_post_types'] ) ) {
				$settings[ self::DB_SHARE_CUSTOM_POST_TYPES ] = explode( ',', sanitize_text_field( wp_unslash( $_POST['share_base_custom_post_types'] ) ) );
			} else {
				$settings[ self::DB_SHARE_CUSTOM_POST_TYPES ] = array();
			}

			if ( ! empty( $_POST['share_base_check_interval'] ) ) {
				$tmp_share_base_check_interval = sanitize_text_field( wp_unslash( $_POST['share_base_check_interval'] ) );
				if ( is_numeric( $tmp_share_base_check_interval ) ) {
					$settings[ self::DB_SHARE_BASE_CHECK_INTERVAL ] = $tmp_share_base_check_interval;
				}
			}

			if ( ! empty( $_POST['share_base_posts_per_check'] ) ) {
				$tmp_share_base_posts_per_check = sanitize_text_field( wp_unslash( $_POST['share_base_posts_per_check'] ) );
				if ( is_numeric( $tmp_share_base_posts_per_check ) ) {
					$settings[ self::DB_SHARE_BASE_POSTS_PER_CHECK ] = $tmp_share_base_posts_per_check;
				}
			}

			if ( ! empty( $_POST['dynamic_cache_mode'] ) ) {
				$settings[ self::DB_COMMON_DYNAMIC_CACHE_MODE ] = sanitize_text_field( wp_unslash( $_POST['dynamic_cache_mode'] ) );
			}

			if ( ! empty( $_POST['fault_tolerance_mode'] ) ) {
				$settings[ self::DB_COMMON_FAULT_TOLERANCE_MODE ] = sanitize_text_field( wp_unslash( $_POST['fault_tolerance_mode'] ) );
			}

			if ( ! empty( $_POST['crawl_throttling_mode'] ) ) {
				$settings[ self::DB_COMMON_CRAWL_THROTTLING_MODE ] = sanitize_text_field( wp_unslash( $_POST['crawl_throttling_mode'] ) );
			}

			if ( ! empty( $_POST['share_variation_analysis_mode'] ) ) {
				$settings[ self::DB_SHARE_VARIATION_ANALYSIS_MODE ] = sanitize_text_field( wp_unslash( $_POST['share_variation_analysis_mode'] ) );
			}

			if ( ! empty( $_POST['follow_variation_analysis_mode'] ) ) {
				$settings[ self::DB_FOLLOW_VARIATION_ANALYSIS_MODE ] = sanitize_text_field( wp_unslash( $_POST['follow_variation_analysis_mode'] ) );
			}

			if ( ! empty( $_POST['share_rush_new_content_term'] ) ) {
				$tmp_share_rush_new_content_term = sanitize_text_field( wp_unslash( $_POST['share_rush_new_content_term'] ) );
				if ( is_numeric( $tmp_share_rush_new_content_term ) ) {
					$settings[ self::DB_SHARE_RUSH_NEW_CONTENT_TERM ] = $tmp_share_rush_new_content_term;
				}
			}

			if ( ! empty( $_POST['share_rush_check_interval'] ) ) {
				$tmp_share_rush_check_interval = sanitize_text_field( wp_unslash( $_POST['share_rush_check_interval'] ) );
				if ( is_numeric( $tmp_share_rush_check_interval ) ) {
					$settings[ self::DB_SHARE_RUSH_CHECK_INTERVAL ] = $tmp_share_rush_check_interval;
				}
			}

			if ( ! empty( $_POST['share_rush_posts_per_check'] ) ) {
				$tmp_share_rush_posts_per_check = sanitize_text_field( wp_unslash( $_POST['share_rush_posts_per_check'] ) );
				if ( is_numeric( $tmp_share_rush_posts_per_check ) ) {
					$settings[ self::DB_SHARE_RUSH_POSTS_PER_CHECK ] = $tmp_share_rush_posts_per_check;
				}
			}

			if ( ! empty( $_POST['data_export_mode'] ) ) {
				$settings[ self::DB_COMMON_DATA_EXPORT_MODE ] = sanitize_text_field( wp_unslash( $_POST['data_export_mode'] ) );
			}

			if ( ! empty( $_POST['data_export_interval'] ) ) {
				$tmp_data_export_interval  = sanitize_text_field( wp_unslash( $_POST['data_export_interval'] ) );
				if ( is_numeric( $tmp_data_export_interval ) ) {
					$settings[ self::DB_COMMON_DATA_EXPORT_INTERVAL ] = $tmp_data_export_interval;
				}
			}

			if ( ! empty( $_POST['common_data_crawler_retry_limit'] ) ) {
				$tmp_common_data_crawler_retry_limit  = sanitize_text_field( wp_unslash( $_POST['common_data_crawler_retry_limit'] ) );
				if ( is_numeric( $tmp_common_data_crawler_retry_limit ) ) {
					$settings[ self::DB_COMMON_CRAWLER_RETRY_LIMIT ] = $tmp_common_data_crawler_retry_limit;
				}
			}

			if ( ! empty( $_POST['share_base_cache_target_twitter'] ) ) {
				$share_base_cache_target[ self::REF_SHARE_TWITTER ] = true;
			} else {
				$share_base_cache_target[ self::REF_SHARE_TWITTER ] = false;
			}

			if ( ! empty( $_POST['share_alternative_twitter_api'] ) ) {
				$settings[ self::DB_SHARE_BASE_TWITTER_API ] = sanitize_text_field( wp_unslash( $_POST['share_alternative_twitter_api'] ) );
			}

			if ( ! empty( $_POST['share_base_cache_target_facebook'] ) ) {
				$share_base_cache_target[ self::REF_SHARE_FACEBOOK ] = true;
			} else {
				$share_base_cache_target[ self::REF_SHARE_FACEBOOK ] = false;
			}

			if ( ! empty( $_POST['share_base_cache_target_gplus'] ) ) {
				$share_base_cache_target[ self::REF_SHARE_GPLUS ] = true;
			} else {
				$share_base_cache_target[ self::REF_SHARE_GPLUS ] = false;
			}

			if ( ! empty( $_POST['share_base_cache_target_pocket'] ) ) {
				$share_base_cache_target[ self::REF_SHARE_POCKET ] = true;
			} else {
				$share_base_cache_target[ self::REF_SHARE_POCKET ] = false;
			}

			if ( ! empty( $_POST['share_base_cache_target_hatebu'] ) ) {
				$share_base_cache_target[ self::REF_SHARE_HATEBU ] = true;
			} else {
				$share_base_cache_target[ self::REF_SHARE_HATEBU ] = false;
			}

			if ( ! empty( $_POST['share_base_cache_target_pinterest'] ) ) {
				$share_base_cache_target[ self::REF_SHARE_PINTEREST ] = true;
			} else {
				$share_base_cache_target[ self::REF_SHARE_PINTEREST ] = false;
			}

			if ( ! empty( $_POST['share_base_cache_target_linkedin'] ) ) {
				$share_base_cache_target[ self::REF_SHARE_LINKEDIN ] = true;
			} else {
				$share_base_cache_target[ self::REF_SHARE_LINKEDIN ] = false;
			}

			if ( ! empty( $share_base_cache_target ) ) {
				$settings[ self::DB_SHARE_CACHE_TARGET ] = $share_base_cache_target;
			}

			if ( ! empty( $_POST['follow_base_check_interval'] ) ) {
				$tmp_follow_base_check_interval = sanitize_text_field( wp_unslash( $_POST['follow_base_check_interval'] ) );
				if ( is_numeric( $tmp_follow_base_check_interval ) ) {
					if ( $tmp_follow_base_check_interval >= self::OPT_FOLLOW_BASE_CHECK_INTERVAL_MIN ) {
						$settings[ self::DB_FOLLOW_CHECK_INTERVAL ] = $tmp_follow_base_check_interval;
					} else {
						$settings[ self::DB_FOLLOW_CHECK_INTERVAL ] = self::OPT_FOLLOW_BASE_CHECK_INTERVAL_MIN;
					}
				}
			}

			if ( ! empty( $_POST['follow_base_cache_target_feedly'] ) ) {
				$follow_base_cache_target[ self::REF_FOLLOW_FEEDLY ] = true;
			} else {
				$follow_base_cache_target[ self::REF_FOLLOW_FEEDLY ] = false;
			}

			if ( ! empty( $_POST['follow_base_cache_target_twitter'] ) ) {
				$follow_base_cache_target[ self::REF_FOLLOW_TWITTER ] = true;
			} else {
				$follow_base_cache_target[ self::REF_FOLLOW_TWITTER ] = false;
			}

			if ( ! empty( $_POST['follow_base_cache_target_facebook'] ) ) {
				$follow_base_cache_target[ self::REF_FOLLOW_FACEBOOK ] = true;
			} else {
				$follow_base_cache_target[ self::REF_FOLLOW_FACEBOOK ] = false;
			}

			if ( ! empty( $_POST['follow_base_cache_target_push7'] ) ) {
				$follow_base_cache_target[ self::REF_FOLLOW_PUSH7 ] = true;
			} else {
				$follow_base_cache_target[ self::REF_FOLLOW_PUSH7 ] = false;
			}

			if ( ! empty( $_POST['follow_base_cache_target_instagram'] ) ) {
				$follow_base_cache_target[ self::REF_FOLLOW_INSTAGRAM ] = true;
			} else {
				$follow_base_cache_target[ self::REF_FOLLOW_INSTAGRAM ] = false;
			}

			if ( ! empty( $_POST['follow_base_cache_target_onesignal'] ) ) {
				$follow_base_cache_target[ self::REF_FOLLOW_ONESIGNAL ] = true;
			} else {
				$follow_base_cache_target[ self::REF_FOLLOW_ONESIGNAL ] = false;
			}

			if ( ! empty( $follow_base_cache_target ) ) {
				$settings[ self::DB_FOLLOW_CACHE_TARGET ] = $follow_base_cache_target;
			}

			$follow_twitter_preparation_flag = true;

			if ( ! empty( $_POST['share_facebook_app_id'] ) ) {
				$settings[ self::DB_SHARE_FACEBOOK_APP_ID ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['share_facebook_app_id'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['share_facebook_app_secret'] ) ) {
				$settings[ self::DB_SHARE_FACEBOOK_APP_SECRET ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['share_facebook_app_secret'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_twitter_screen_name'] ) ) {
				$settings[ self::DB_FOLLOW_TWITTER_SCREEN_NAME ] = sanitize_text_field( wp_unslash( $_POST['follow_twitter_screen_name'] ) );
			}

			if ( ! empty( $_POST['follow_twitter_api_key'] ) ) {
				$settings[ self::DB_FOLLOW_TWITTER_API_KEY ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_twitter_api_key'] ) ), AUTH_KEY );
			} else {
				$follow_twitter_preparation_flag = false;
			}

			if ( ! empty( $_POST['follow_twitter_api_secret_key'] ) ) {
				$settings[ self::DB_FOLLOW_TWITTER_API_SECRET_KEY ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_twitter_api_secret_key'] ) ), AUTH_KEY );
			} else {
				$follow_twitter_preparation_flag = false;
			}

			if ( ! empty( $_POST['follow_twitter_bearer_token'] ) ) {
				$settings[ self::DB_FOLLOW_TWITTER_BEARER_TOKEN ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_twitter_bearer_token'] ) ), AUTH_KEY );
			}

			/*
			if ( isset( $_POST['follow_twitter_access_token'] ) && $_POST['follow_twitter_access_token'] ) {
				$settings[self::DB_FOLLOW_TWITTER_ACCESS_TOKEN] = SCC_Crypt::encrypt( $_POST['follow_twitter_access_token'], AUTH_KEY );
			}

			if ( isset( $_POST['follow_twitter_access_token_secret'] ) && $_POST['follow_twitter_access_token_secret'] ) {
				$settings[self::DB_FOLLOW_TWITTER_ACCESS_TOKEN_SECRET] = SCC_Crypt::encrypt( $_POST['follow_twitter_access_token_secret'], AUTH_KEY );
			}
			*/

			if ( ! empty( $_POST['follow_facebook_page_id'] ) ) {
				$settings[ self::DB_FOLLOW_FACEBOOK_PAGE_ID ] = sanitize_text_field( wp_unslash( $_POST['follow_facebook_page_id'] ) );
			}

			if ( ! empty( $_POST['follow_facebook_app_id'] ) ) {
				$settings[ self::DB_FOLLOW_FACEBOOK_APP_ID ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_facebook_app_id'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_facebook_app_secret'] ) ) {
				$settings[ self::DB_FOLLOW_FACEBOOK_APP_SECRET ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_facebook_app_secret'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_facebook_access_token'] ) ) {
				$settings[ self::DB_FOLLOW_FACEBOOK_ACCESS_TOKEN ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_facebook_access_token'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_push7_app_number'] ) ) {
				$settings[ self::DB_FOLLOW_PUSH7_APP_NUMBER ] = SCC_Crypt::encrypt( sanitize_text_field( wp_unslash( $_POST['follow_push7_app_number'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_instagram_access_token'] ) ) {
				$settings[ self::DB_FOLLOW_INSTAGRAM_ACCESS_TOKEN ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_instagram_access_token'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_instagram_client_id'] ) ) {
				$settings[ self::DB_FOLLOW_INSTAGRAM_CLIENT_ID ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_instagram_client_id'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_instagram_client_secret'] ) ) {
				$settings[ self::DB_FOLLOW_INSTAGRAM_CLIENT_SECRET ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_instagram_client_secret'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_onesignal_app_id'] ) ) {
				$settings[ self::DB_FOLLOW_ONESIGNAL_APP_ID ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_onesignal_app_id'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_onesignal_rest_api_key'] ) ) {
				$settings[ self::DB_FOLLOW_ONESIGNAL_REST_API_KEY ] = SCC_Crypt::encrypt( SCC_Format::sanitize( wp_unslash( $_POST['follow_onesignal_rest_api_key'] ) ), AUTH_KEY );
			}

			if ( ! empty( $_POST['follow_feed_type'] ) ) {
				switch ( $_POST['follow_feed_type'] ) {
					case 'default':
						$settings[ self::DB_FOLLOW_FEED_TYPE ] = self::OPT_FEED_TYPE_DEFAULT;
						break;
					case 'rss2':
						$settings[ self::DB_FOLLOW_FEED_TYPE ] = self::OPT_FEED_TYPE_RSS2;
						break;
					case 'rss':
						$settings[ self::DB_FOLLOW_FEED_TYPE ] = self::OPT_FEED_TYPE_RSS;
						break;
					case 'rdf':
						$settings[ self::DB_FOLLOW_FEED_TYPE ] = self::OPT_FEED_TYPE_RDF;
						break;
					case 'atom':
						$settings[ self::DB_FOLLOW_FEED_TYPE ] = self::OPT_FEED_TYPE_ATOM;
						break;
					default:
						$settings[ self::DB_FOLLOW_FEED_TYPE ] = self::OPT_FEED_TYPE_DEFAULT;
				}
			} else {
				$settings[ self::DB_FOLLOW_FEED_TYPE ] = self::OPT_FEED_TYPE_DEFAULT;
			}

			if ( ! empty( $_POST['scheme_migration_mode'] ) ) {
				$settings[ self::DB_COMMON_SCHEME_MIGRATION_MODE ] = self::OPT_COMMON_SCHEME_MIGRATION_MODE_ON;
			} else {
				$settings[ self::DB_COMMON_SCHEME_MIGRATION_MODE ] = self::OPT_COMMON_SCHEME_MIGRATION_MODE_OFF;
			}

			if ( ! empty( $_POST['scheme_migration_date'] ) ) {
				$tmp_scheme_migration_date = sanitize_text_field( wp_unslash( $_POST['scheme_migration_date'] ) );
				if ( strptime( $tmp_scheme_migration_date, '%Y/%m/%d' ) ) {
					$settings[ self::DB_COMMON_SCHEME_MIGRATION_DATE ] = $tmp_scheme_migration_date;
				}
			}

			if ( ! empty( $_POST['crawler_ssl_verification'] ) ) {
				$settings[ self::DB_COMMON_CRAWLER_SSL_VERIFICATION ] = self::OPT_COMMON_CRAWLER_SSL_VERIFY_ON;
			} else {
				$settings[ self::DB_COMMON_CRAWLER_SSL_VERIFICATION ] = self::OPT_COMMON_CRAWLER_SSL_VERIFY_OFF;
			}

			if ( isset( $_POST['a_cronbtype'] ) && 'mon' === wp_unslash( $_POST['a_cronbtype'] ) ) {
				if ( isset( $_POST['a_moncronminutes'] ) && isset( $_POST['a_moncronhours'] ) && isset( $_POST['a_moncronmday'] ) ) {
					$settings[ self::DB_SHARE_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['a_moncronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['a_moncronhours'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['a_moncronmday'] ) ) . ' * *';
				}
			}

			if ( isset( $_POST['a_cronbtype'] ) && 'week' === wp_unslash( $_POST['a_cronbtype'] ) ) {
				if ( isset( $_POST['a_weekcronminutes'] ) && isset( $_POST['a_weekcronhours'] ) && isset( $_POST['a_weekcronwday'] ) ) {
					$settings[ self::DB_SHARE_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['a_weekcronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['a_weekcronhours'] ) ) . ' * * ' . sanitize_text_field( wp_unslash( $_POST['a_weekcronwday'] ) );
				}
			}

			if ( isset( $_POST['a_cronbtype'] ) && 'day' === wp_unslash( $_POST['a_cronbtype'] ) ) {
				if ( isset( $_POST['a_daycronminutes'] ) && isset( $_POST['a_daycronhours'] ) ) {
					$settings[ self::DB_SHARE_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['a_daycronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['a_daycronhours'] ) ) . ' * * *';
				}
			}

			if ( isset( $_POST['a_cronbtype'] ) && 'hour' === wp_unslash( $_POST['a_cronbtype'] ) ) {
				if ( isset( $_POST['a_hourcronminutes'] ) ) {
					$settings[ self::DB_SHARE_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['a_hourcronminutes'] ) ) . ' * * * *';
				}
			}

			if ( isset( $_POST['b_cronbtype'] ) && 'mon' === wp_unslash( $_POST['b_cronbtype'] ) ) {
				if ( isset( $_POST['b_moncronminutes'] ) && isset( $_POST['b_moncronhours'] ) && isset( $_POST['b_moncronmday'] ) ) {
					$settings[ self::DB_FOLLOW_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['b_moncronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['b_moncronhours'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['b_moncronmday'] ) ) . ' * *';
				}
			}

			if ( isset( $_POST['b_cronbtype'] ) && 'week' === wp_unslash( $_POST['b_cronbtype'] ) ) {
				if ( isset( $_POST['b_weekcronminutes'] ) && isset( $_POST['b_weekcronhours'] ) && isset( $_POST['b_weekcronwday'] ) ) {
					$settings[ self::DB_FOLLOW_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['b_weekcronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['b_weekcronhours'] ) ) . ' * * ' . sanitize_text_field( wp_unslash( $_POST['b_weekcronwday'] ) );
				}
			}

			if ( isset( $_POST['b_cronbtype'] ) && 'day' === wp_unslash( $_POST['b_cronbtype'] ) ) {
				if ( isset( $_POST['b_daycronminutes'] ) && isset( $_POST['b_daycronhours'] ) ) {
					$settings[ self::DB_FOLLOW_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['b_daycronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['b_daycronhours'] ) ) . ' * * *';
				}
			}

			if ( isset( $_POST['b_cronbtype'] ) && 'hour' === wp_unslash( $_POST['b_cronbtype'] ) ) {
				if ( isset( $_POST['b_hourcronminutes'] ) ) {
					$settings[ self::DB_FOLLOW_VARIATION_ANALYSIS_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['b_hourcronminutes'] ) ) . ' * * * *';
				}
			}

			if ( isset( $_POST['e_cronbtype'] ) && 'mon' === wp_unslash( $_POST['e_cronbtype'] ) ) {
				if ( isset( $_POST['e_moncronminutes'] ) && isset( $_POST['e_moncronhours'] ) && isset( $_POST['e_moncronmday'] ) ) {
					$settings[ self::DB_COMMON_DATA_EXPORT_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['e_moncronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['e_moncronhours'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['e_moncronmday'] ) ) . ' * *';
				}
			}

			if ( isset( $_POST['e_cronbtype'] ) && 'week' === wp_unslash( $_POST['e_cronbtype'] ) ) {
				if ( isset( $_POST['e_weekcronminutes'] ) && isset( $_POST['e_weekcronhours'] ) && isset( $_POST['e_weekcronwday'] ) ) {
					$settings[ self::DB_COMMON_DATA_EXPORT_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['e_weekcronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['e_weekcronhours'] ) ) . ' * * ' .  sanitize_text_field( wp_unslash( $_POST['e_weekcronwday'] ) );
				}
			}

			if ( isset( $_POST['e_cronbtype'] ) && 'day' === wp_unslash( $_POST['e_cronbtype'] ) ) {
				if ( isset( $_POST['e_daycronminutes'] ) && isset( $_POST['e_daycronhours'] ) ) {
					$settings[ self::DB_COMMON_DATA_EXPORT_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['e_daycronminutes'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['e_daycronhours'] ) ) . ' * * *';
				}
			}

			if ( isset( $_POST['e_cronbtype'] ) && 'hour' === wp_unslash( $_POST['e_cronbtype'] ) ) {
				if ( isset( $_POST['e_hourcronminutes'] ) ) {
					$settings[ self::DB_COMMON_DATA_EXPORT_SCHEDULE ] = sanitize_text_field( wp_unslash( $_POST['e_hourcronminutes'] ) ) . ' * * * *';
				}
			}

			update_option( self::DB_SETTINGS, $settings );

			$this->reactivate_plugin();

			set_transient( self::OPT_COMMON_ERROR_MESSAGE, $wp_error->get_error_messages(), 10 );
		} // End if().

		if ( isset( $_POST['reset_data'] ) && __( 'Reset', self::DOMAIN ) === wp_unslash( $_POST['reset_data'] ) ) {
			SCC_Logger::log( '[' . __METHOD__ . '] reset' );

			$this->export_engines[ self::REF_COMMON_EXPORT ]->reset_export();
		}

		if ( isset( $_POST['export_data'] ) && __( 'Export', self::DOMAIN ) === wp_unslash( $_POST['export_data'] ) ) {
			SCC_Logger::log( '[' . __METHOD__ . '] export' );

			set_time_limit( $this->extended_max_execution_time );

			$this->export_engines[ self::REF_COMMON_EXPORT ]->execute_export( null );

			set_time_limit( $this->original_max_execution_time );
		}

		if ( isset( $_POST['update_share_comparison_base'] ) && __( 'Update Basis of Comparison', self::DOMAIN ) === wp_unslash( $_POST['update_share_comparison_base'] ) ) {
			SCC_Logger::log( '[' . __METHOD__ . '] base' );

			set_time_limit( $this->extended_max_execution_time );

			$this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->execute_base( null );

			set_time_limit( $this->original_max_execution_time );
		}

		if ( isset( $_POST['update_follow_comparison_base'] ) && __( 'Update Basis of Comparison', self::DOMAIN ) === wp_unslash( $_POST['update_follow_comparison_base'] ) ) {
			SCC_Logger::log( '[' . __METHOD__ . '] base' );

			set_time_limit( $this->extended_max_execution_time );

			$this->analytical_engines[ self::REF_FOLLOW_ANALYSIS ]->execute_base( null );

			set_time_limit( $this->original_max_execution_time );
		}

		if ( isset( $_POST['clear_share_base_cache'] ) && __( 'Clear Cache', self::DOMAIN ) === wp_unslash( $_POST['clear_share_base_cache'] ) ) {
			SCC_Logger::log( '[' . __METHOD__ . '] clear cache' );

			set_time_limit( $this->extended_max_execution_time );

			$this->cache_engines[ self::REF_SHARE_BASE ]->clear_cache();
			//$this->cache_engines[self::REF_SHARE_2ND]->clear_cache();
			$this->cache_engines[ self::REF_SHARE_2ND ]->initialize_cache();
			$this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->clear_base();

			set_time_limit( $this->original_max_execution_time );
		}

		if ( isset( $_POST['clear_follow_base_cache'] ) && __( 'Clear Cache', self::DOMAIN ) === wp_unslash( $_POST['clear_follow_base_cache'] ) ) {
			SCC_Logger::log( '[' . __METHOD__ . '] clear cache' );

			set_time_limit( $this->extended_max_execution_time );

			$this->cache_engines[ self::REF_FOLLOW_BASE ]->clear_cache();
			//$this->cache_engines[ self::REF_FOLLOW_2ND ]->clear_cache();
			$this->cache_engines[ self::REF_FOLLOW_2ND ]->initialize_cache();

			set_time_limit( $this->original_max_execution_time );
		}

		if ( isset( $_POST['direct_follow_base_cache'] ) && __( 'Cache', self::DOMAIN ) === wp_unslash( $_POST['direct_follow_base_cache'] ) ) {
			SCC_Logger::log( '[' . __METHOD__ . '] cache' );

			set_time_limit( $this->extended_max_execution_time );

			$this->cache_engines[ self::REF_FOLLOW_BASE ]->direct_cache( true );

			set_time_limit( $this->original_max_execution_time );
		}

		if ( isset( $_POST['get_tiwtter_bearer_token'] ) && __( 'Get Bearer Token', self::DOMAIN ) === wp_unslash( $_POST['get_tiwtter_bearer_token'] ) ) {
			$tmp_twitter_bearer_token = SCC_Oauth::get_twitter_bearer_token( $this->follow_twitter_api_key, $this->follow_twitter_api_secret_key );
		}
	} // End if().

} elseif ( ! empty( $_GET['_wpnonce'] ) && ! empty( $_GET['action'] ) && ! empty( $_GET['code'] ) ) {
	if ( 'instagram-auth' === $_GET['action'] ) {
		if ( check_admin_referer( 'instagram-auth', '_wpnonce' ) ) {
			if ( current_user_can( self::OPT_COMMON_CAPABILITY ) ) {
				$redirect_uri = plugins_url() . '/sns-count-cache/?action=instagram-auth';
				$code = sanitize_text_field( wp_unslash( $_GET['code'] ) );
				$tmp_instagram_access_token = SCC_Oauth::get_instagram_access_token( $this->follow_instagram_client_id, $this->follow_instagram_client_secret, $redirect_uri, $code );
			}
		} // End if().
	} elseif ( 'facebook-auth' === $_GET['action'] ) {
		if ( check_admin_referer( 'facebook-auth', '_wpnonce' ) ) {
			if ( current_user_can( self::OPT_COMMON_CAPABILITY ) ) {
				$redirect_uri = plugins_url() . '/sns-count-cache/?action=facebook-auth';
				$code = sanitize_text_field( wp_unslash( $_GET['code'] ) );
				$tmp_facebook_access_token = SCC_Oauth::get_facebook_access_token( $this->follow_facebook_app_id, $this->follow_facebook_app_secret, $redirect_uri, $code, $this->follow_facebook_page_id );
			} else {
				SCC_Logger::log( 'Invalid capability' );
			}
		} else {
			SCC_Logger::log( 'Invalid nonce' );
		}
	} // End if().
} // End if().
?>
<div class="wrap">
	<h2><a href="admin.php?page=scc-setting"><?php esc_html_e( 'SNS Count Cache', self::DOMAIN ); ?></a></h2>
	<?php
	$messages = get_transient( self::OPT_COMMON_ERROR_MESSAGE );

	if ( $messages ) {
	?>
	<div class="error">
		<ul>
		<?php
		foreach ( $messages as $message ) {
		?>
			<li><?php echo esc_html( $message ); ?></li>
		<?php
		}
		?>
		</ul>
	</div>
	<?php
		delete_transient( self::OPT_COMMON_ERROR_MESSAGE );
	}
	?>
	<?php
		$status = SCC_WP_Cron::test_cron_spawn();

		if ( is_wp_error( $status ) ) {
			if ( 'scc_cron_info' === $status->get_error_code() ) {
				echo '<div class="notice notice-info"><p>';
				echo esc_html( $status->get_error_message() );
				echo '</p></div>';
			} else {
				echo '<div class="notice notice-error"><p>';
				esc_html_e( 'There was a problem spawning a call to the WP-Cron system on your site. This means WP-Cron jobs on your site may not work. The problem was: ', self::DOMAIN );
				echo '<br><strong>' . esc_html( $status->get_error_message() ) . '</strong>';
				echo '</p></div>';
			}
		}

		if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_FACEBOOK ] ) ) {
			$configuration_check = $this->crawlers[ self::REF_SHARE ]->check_crawl_strategy_configurations( self::REF_SHARE_FACEBOOK );

			if ( ! $configuration_check ) {
				echo '<div class="notice notice-warning"><p>';
				esc_html_e( 'Configuratin is not enough to get share count. Please set required parameters at ', self::DOMAIN );
				echo '<a href="#share-base-cache-facebook">' . esc_html( __( 'Share Base Cache - Facebook', self::DOMAIN ) ) . '</a>';
				echo '.</p></div>';
			}
		}

		if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_INSTAGRAM ] ) ) {
			$configuration_check = $this->crawlers[ self::REF_FOLLOW ]->check_crawl_strategy_configurations( self::REF_FOLLOW_INSTAGRAM );

			if ( ! $configuration_check ) {
				echo '<div class="notice notice-warning"><p>';
				esc_html_e( 'Configuratin is not enough to get follower count. Please set required parameters at ', self::DOMAIN );
				echo '<a href="#follow-base-cache-instagram">' . esc_html( __( 'Follow Base Cache - Instagram', self::DOMAIN ) ) . '</a>';
				echo '.</p></div>';
			}
		}

		if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FACEBOOK ] ) ) {
			$configuration_check = $this->crawlers[ self::REF_FOLLOW ]->check_crawl_strategy_configurations( self::REF_FOLLOW_FACEBOOK );

			if ( ! $configuration_check ) {
				echo '<div class="notice notice-warning"><p>';
				esc_html_e( 'Configuratin is not enough to get follower count. Please set required parameters at ', self::DOMAIN );
				echo '<a href="#follow-base-cache-facebook">' . esc_html( __( 'Follow Base Cache - Facebook', self::DOMAIN ) ) . '</a>';
				echo '.</p></div>';
			}
		}

		if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_PUSH7 ] ) ) {
			$configuration_check = $this->crawlers[ self::REF_FOLLOW ]->check_crawl_strategy_configurations( self::REF_FOLLOW_PUSH7 );

			if ( ! $configuration_check ) {
				echo '<div class="notice notice-warning"><p>';
				esc_html_e( 'Configuratin is not enough to get follower count. Please set required parameters at ', self::DOMAIN );
				echo '<a href="#follow-base-cache-push7">' . esc_html( __( 'Follow Base Cache - Push7', self::DOMAIN ) ) . '</a>';
				echo '.</p></div>';
			}
		}

		if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_TWITTER ] ) ) {
			$configuration_check = $this->crawlers[ self::REF_FOLLOW ]->check_crawl_strategy_configurations( self::REF_FOLLOW_TWITTER );

			if ( ! $configuration_check ) {
				echo '<div class="notice notice-warning"><p>';
				esc_html_e( 'Configuratin is not enough to get follower count. Please set required parameters at ', self::DOMAIN );
				echo '<a href="#follow-base-cache-twitter">' . esc_html( __( 'Follow Base Cache - Twitter', self::DOMAIN ) ) . '</a>';
				echo '.</p></div>';
			}
		}

		if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_ONESIGNAL ] ) ) {
			$configuration_check = $this->crawlers[ self::REF_FOLLOW ]->check_crawl_strategy_configurations( self::REF_FOLLOW_ONESIGNAL );

			if ( ! $configuration_check ) {
				echo '<div class="notice notice-warning"><p>';
				esc_html_e( 'Configuratin is not enough to get follower count. Please set required parameters at ', self::DOMAIN );
				echo '<a href="#follow-base-cache-onesignal">' . esc_html( __( 'Follow Base Cache - OneSignal', self::DOMAIN ) ) . '</a>';
				echo '.</p></div>';
			}
		}

	?>

	<div class="sns-cnt-cache">
		<h3 class="nav-tab-wrapper">
			<a class="nav-tab" href="admin.php?page=scc-dashboard"><?php esc_html_e( 'Dashboard', self::DOMAIN ); ?></a>
			<a class="nav-tab" href="admin.php?page=scc-cache-status"><?php esc_html_e( 'Cache Status', self::DOMAIN ); ?></a>
			<a class="nav-tab" href="admin.php?page=scc-share-count"><?php esc_html_e( 'Share Count', self::DOMAIN ); ?></a>
		<?php if ( self::OPT_SHARE_VARIATION_ANALYSIS_NONE !== $this->share_variation_analysis_mode ) { ?>
			<a class="nav-tab" href="admin.php?page=scc-hot-content"><?php esc_html_e( 'Hot Content', self::DOMAIN ); ?></a>
		<?php } ?>
			<a class="nav-tab nav-tab-active" href="admin.php?page=scc-setting"><?php esc_html_e( 'Setting', self::DOMAIN ); ?></a>
			<a class="nav-tab" href="admin.php?page=scc-help"><?php esc_html_e( 'Help', self::DOMAIN ); ?></a>
		</h3>
		<p id="options-menu">
			<a href="#current-parameter"><?php esc_html_e( 'Current Setting', self::DOMAIN ); ?></a> | <a href="#share-base-cache"><?php esc_html_e( 'Share Base Cache', self::DOMAIN ); ?></a> | <a href="#share-rush-cache"><?php esc_html_e( 'Share Rush Cache', self::DOMAIN ); ?></a> | <a href="#share-variation-analysis"><?php esc_html_e( 'Share Variation Analysis', self::DOMAIN ); ?></a> | <a href="#follow-base-cache"><?php esc_html_e( 'Follow Base Cache', self::DOMAIN ); ?></a> | <a href="#follow-variation-analysis"><?php esc_html_e( 'Follow Variation Analysis', self::DOMAIN ); ?></a> | <a href="#common-dynamic-cache"><?php esc_html_e( 'Dynamic Cache', self::DOMAIN ); ?></a> | <a href="#common-fault-tolerance"><?php esc_html_e( 'Fault Tolerance', self::DOMAIN ); ?></a> | <a href="#common-data-crawler"><?php esc_html_e( 'Data Crawler', self::DOMAIN ); ?></a> | <a href="#common-data-export"><?php esc_html_e( 'Data Export', self::DOMAIN ); ?></a> | <a href="#common-exported-file"><?php esc_html_e( 'Exported File', self::DOMAIN ); ?></a>
		</p>
		<div class="metabox-holder">
			<div id="current-parameter" class="postbox">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span><?php esc_html_e( 'Current Setting', self::DOMAIN ); ?></span></h3>
				<div class="inside">
					<table class="tfloat view-table">
						<thead>
							<tr class="thead">
								<th class="dummy"></th>
								<th><?php esc_html_e( 'Capability', self::DOMAIN ); ?></th>
								<th><?php esc_html_e( 'Parameter', self::DOMAIN ); ?></th>
								<th><?php esc_html_e( 'Value', self::DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Target SNS', self::DOMAIN ); ?></td>
								<td>
									<?php
									$target_sns = array();

									if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_FACEBOOK ] ) ) {
										$target_sns[] = __( 'Facebook', self::DOMAIN );
									}
									if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_GPLUS ] ) ) {
										$target_sns[] = __( 'Google+', self::DOMAIN );
									}
									if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_HATEBU ] ) ) {
										$target_sns[] = __( 'Hatena Bookmark', self::DOMAIN );
									}
									if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_LINKEDIN ] ) ) {
										$target_sns[] = __( 'Linkedin', self::DOMAIN );
									}
									if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_PINTEREST ] ) ) {
										$target_sns[] = __( 'Pinterest', self::DOMAIN );
									}
									if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_POCKET ] ) ) {
										$target_sns[] = __( 'Pocket', self::DOMAIN );
									}
									if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_TWITTER ] ) ) {
										$target_sns[] = __( 'Twitter', self::DOMAIN );
									}
									echo esc_html( implode( ', ', $target_sns ) );
									?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Custom post types', self::DOMAIN ); ?></td>
								<td>
									<?php
									if ( ! empty( $this->share_base_custom_post_types ) ) {
										echo esc_html( implode( ',', $this->share_base_custom_post_types ) );
									} else {
										esc_html_e( 'N/A', self::DOMAIN );
									}
									?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Interval cheking share count (sec)', self::DOMAIN ); ?></td>
								<td><?php echo esc_html( $this->share_base_check_interval ) . ' ' . esc_html( __( 'seconds', self::DOMAIN ) ); ?></td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Number of contents to check at a time', self::DOMAIN ) ?></td>
								<td><?php echo esc_html( $this->share_base_posts_per_check ) . ' ' . esc_html( __( 'contents', self::DOMAIN ) ); ?></td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Scheme migration mode from http to https', self::DOMAIN ); ?></td>
								<td>
									<?php
									if ( $this->scheme_migration_mode ) {
										esc_html_e( 'On', self::DOMAIN );
									} else {
										esc_html_e( 'Off', self::DOMAIN );
									}
									?>
								</td>
							</tr>
						<?php if ( $this->scheme_migration_mode ) { ?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Scheme migration date from http to https', self::DOMAIN ); ?></td>
								<td>
								<?php
								if ( isset( $this->scheme_migration_date ) ) {
									echo esc_html( $this->scheme_migration_date );
								} else {
									esc_html_e( 'N/A', self::DOMAIN );
								}
								?>
								</td>
							</tr>
						<?php } ?>
						<?php if ( isset( $this->share_base_cache_target[ self::REF_SHARE_TWITTER ] ) && $this->share_base_cache_target[ self::REF_SHARE_TWITTER ] ) { ?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Base Cache - Twitter', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Alternative Twitter API', self::DOMAIN ); ?></td>
								<td>
								<?php
								if ( self::OPT_SHARE_TWITTER_API_JSOON === $this->share_base_twitter_api ) {
									echo '<a href="' . esc_url( 'https://jsoon.digitiminimi.com/' ) . '" target="_blank">' . esc_html( 'widgetoon.js & count.jsoon' ) . '</a>';
								} elseif ( self::OPT_SHARE_TWITTER_API_OPENSHARECOUNT === $this->share_base_twitter_api ) {
									echo '<a href="' . esc_url( 'https://opensharecount.com/' ) . '" target="_blank">' . esc_html( 'OpenShareCount' ) . '</a>';
								} elseif ( self::OPT_SHARE_TWITTER_API_TWITCOUNT === $this->share_base_twitter_api ) {
									echo '<a href="' . esc_url( 'http://twitcount.com/' ) . '" target="_blank">' . esc_html( 'TwitCount' ) . '</a>';
								}
								?>
								</td>
							</tr>
						<?php } ?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Rush Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Term considering posted content as new content', self::DOMAIN ); ?></td>
								<td>
								<?php
								if ( 1 == $this->share_rush_new_content_term ) {
									echo esc_html( $this->share_rush_new_content_term ) . ' ' . esc_html( __( 'day', self::DOMAIN ) );
								} elseif ( 1 < $this->share_rush_new_content_term ) {
									echo esc_html( $this->share_rush_new_content_term ) . ' ' . esc_html( __( 'days', self::DOMAIN ) );
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Rush Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Interval cheking share count (sec)', self::DOMAIN ); ?></td>
								<td>
									<?php
										echo esc_html( $this->share_rush_check_interval ) . ' ' . esc_html( __( 'seconds', self::DOMAIN ) );
									?>
								</td>
								<tr>
									<td class="dummy"></td>
									<td><?php esc_html_e( 'Share Rush Cache', self::DOMAIN ); ?></td>
									<td><?php esc_html_e( 'Number of contents to check at a time', self::DOMAIN ) ?></td>
									<td><?php echo esc_html( $this->share_rush_posts_per_check ) . ' ' . esc_html( __( 'contents', self::DOMAIN ) ); ?></td>
								</tr>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Variation Analysis', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Method to update basis of comparison', self::DOMAIN ); ?></td><td>
								<?php
								switch ( $this->share_variation_analysis_mode ) {
									case self::OPT_SHARE_VARIATION_ANALYSIS_NONE:
										esc_html_e( 'Disabled (None)', self::DOMAIN );
										break;
									case self::OPT_SHARE_VARIATION_ANALYSIS_MANUAL:
										esc_html_e( 'Enabled (Manual)', self::DOMAIN );
										break;
									case self::OPT_SHARE_VARIATION_ANALYSIS_SCHEDULER:
										esc_html_e( 'Enabled (Scheduler)', self::DOMAIN );
										break;
									default:
										esc_html_e( 'Disabled (None)', self::DOMAIN );
								}
								?>
								</td>
							</tr>
						<?php
						if ( self::OPT_SHARE_VARIATION_ANALYSIS_SCHEDULER === $this->share_variation_analysis_mode ) {
						?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Share Variation Analysis', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Schedule', self::DOMAIN ); ?></td>
								<td><?php echo esc_html( $this->share_variation_analysis_schedule ); ?></td>
							</tr>
						<?php
						}
						?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Target SNS', self::DOMAIN ); ?></td>
								<td>
									<?php
										$target_sns = array();
										if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FACEBOOK ] ) ) {
											$target_sns[] = 'Facebook';
										}
										if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FEEDLY ] ) ) {
											$target_sns[] = 'Feedly';
										}
										if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_INSTAGRAM ] ) ) {
											$target_sns[] = 'Instagram';
										}
										if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_ONESIGNAL ] ) ) {
											$target_sns[] = 'OneSignal';
										}
										if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_PUSH7 ] ) ) {
											$target_sns[] = 'Push7';
										}
										if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_TWITTER ] ) ) {
											$target_sns[] = 'Twitter';
										}
										echo esc_html( implode( ', ', $target_sns ) );
									?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Base Cache', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Interval cheking follower count (sec)', self::DOMAIN ); ?></td>
								<td><?php echo esc_html( $this->follow_base_check_interval ) . ' ' . esc_html( __( 'seconds', self::DOMAIN ) ); ?></td>
							</tr>
						<?php if ( $this->follow_base_cache_target[ self::REF_FOLLOW_FEEDLY ] ) { ?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Base Cache - Feedly', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Target feed type', self::DOMAIN ); ?></td>
								<td>
									<?php
										switch ( $this->follow_feed_type ) {
											case self::OPT_FEED_TYPE_DEFAULT:
												esc_html_e( 'Default', self::DOMAIN );
												break;
											case self::OPT_FEED_TYPE_RSS:
												esc_html_e( 'RSS', self::DOMAIN );
												break;
											case self::OPT_FEED_TYPE_RSS2:
												esc_html_e( 'RSS2', self::DOMAIN );
												break;
											case self::OPT_FEED_TYPE_RDF:
												esc_html_e( 'RDF', self::DOMAIN );
												break;
											case self::OPT_FEED_TYPE_ATOM:
												esc_html_e( 'ATOM', self::DOMAIN );
												break;
											default:
												esc_html_e( 'Default', self::DOMAIN );
										}
									?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Base Cache - Feedly', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Target feed', self::DOMAIN ); ?></td>
								<td><a href="<?php echo esc_url( get_feed_link( $this->follow_feed_type ) ); ?>" target="_blank"><?php echo esc_html( get_feed_link( $this->follow_feed_type ) ); ?></a></td>
							</tr>
						<?php } ?>
						<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_TWITTER ] ) ) { ?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Base Cache - Twitter', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Target screen name', self::DOMAIN ); ?></td>
								<td>
									<?php
										if ( ! empty( $this->follow_twitter_screen_name ) ) {
											echo '@' . esc_html( $this->follow_twitter_screen_name );
										} else {
											esc_html_e( 'N/A', self::DOMAIN );
										}
									?>
								</td>
							</tr>
						<?php } ?>
						<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FACEBOOK ] ) ) { ?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Base Cache - Facebook', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Page ID', self::DOMAIN ); ?></td>
								<td>
									<?php
										if ( isset( $this->follow_facebook_page_id ) ) {
											echo esc_html( $this->follow_facebook_page_id );
										} else {
											esc_html_e( 'N/A', self::DOMAIN );
										}
									?>
								</td>
							</tr>
						<?php } ?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Variation Analysis', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Method to update basis of comparison', self::DOMAIN ); ?></td><td>
								<?php
								switch ( $this->follow_variation_analysis_mode ) {
									case self::OPT_FOLLOW_VARIATION_ANALYSIS_NONE:
										esc_html_e( 'Disabled (None)', self::DOMAIN );
										break;
									case self::OPT_FOLLOW_VARIATION_ANALYSIS_MANUAL:
										esc_html_e( 'Enabled (Manual)', self::DOMAIN );
										break;
									case self::OPT_FOLLOW_VARIATION_ANALYSIS_SCHEDULER:
										esc_html_e( 'Enabled (Scheduler)', self::DOMAIN );
										break;
									default:
										esc_html_e( 'Disabled (None)', self::DOMAIN );
										break;
								}
								?>
								</td>
							</tr>
						<?php
							if ( self::OPT_FOLLOW_VARIATION_ANALYSIS_SCHEDULER === $this->follow_variation_analysis_mode ) {
						?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Follow Variation Analysis', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Schedule', self::DOMAIN ); ?></td>
								<td><?php echo esc_html( $this->follow_variation_analysis_schedule ); ?></td>
							</tr>
						<?php
							}
						?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Dynamic Cache', self::DOMAIN); ?></td>
								<td><?php esc_html_e( 'Dynamic caching based on user access', self::DOMAIN ); ?></td><td>
								<?php
								switch ( $this->dynamic_cache_mode ) {
									case self::OPT_COMMON_ACCESS_BASED_CACHE_OFF:
										esc_html_e( 'Disabled', self::DOMAIN );
										break;
									case self::OPT_COMMON_ACCESS_BASED_CACHE_ON:
										esc_html_e( 'Enabled', self::DOMAIN );
										break;
									default:
										esc_html_e( 'Disabled', self::DOMAIN );
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Fault Tolerance', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Fault tolerance of count retrieval', self::DOMAIN ); ?></td><td>
								<?php
								switch ( $this->fault_tolerance_mode ) {
									case self::OPT_COMMON_FAULT_TOLERANCE_OFF:
										esc_html_e( 'Disabled', self::DOMAIN );
										break;
									case self::OPT_COMMON_FAULT_TOLERANCE_ON:
										esc_html_e( 'Enabled', self::DOMAIN );
										break;
									default:
										esc_html_e( 'Disabled', self::DOMAIN );
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Data Crawler', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Crawl method', self::DOMAIN ); ?></td>
								<td>
								<?php
								switch ( $this->crawler_method ) {
									case self::OPT_COMMON_CRAWLER_METHOD_NORMAL:
										esc_html_e( 'Normal (Sequential Retrieval)', self::DOMAIN );
										break;
									case self::OPT_COMMON_CRAWLER_METHOD_CURL:
										esc_html_e( 'Extended (Parallel Retrieval)', self::DOMAIN );
										break;
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Data Crawler', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Crawl retry limit', self::DOMAIN ); ?></td>
								<td>
								<?php
								switch ( $this->crawler_retry_limit ) {
									case -1:
										esc_html_e( 'no retry', self::DOMAIN );
										break;
									case 1:
										esc_html_e( '1 time', self::DOMAIN );
										break;
									case 2:
										esc_html_e( '2 times', self::DOMAIN );
										break;
									case 3:
										esc_html_e( '3 times', self::DOMAIN );
										break;
									case 4:
										esc_html_e( '2 times', self::DOMAIN );
										break;
									case 5:
										esc_html_e( '5 times', self::DOMAIN );
										break;
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Data Crawler', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'SSL verification', self::DOMAIN ); ?></td>
								<td>
								<?php
								if ( $this->crawler_ssl_verification ) {
									esc_html_e( 'On', self::DOMAIN );
								} else {
									esc_html_e( 'Off', self::DOMAIN );
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Data Crawler', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Crawl throttling mode', self::DOMAIN ); ?></td>
								<td>
								<?php
								switch ( $this->crawl_throttling_mode ) {
									case self::OPT_COMMON_CRAWL_THROTTLING_MODE_OFF:
										esc_html_e( 'Disabled', self::DOMAIN );
										break;
									case self::OPT_COMMON_CRAWL_THROTTLING_MODE_ON:
										esc_html_e( 'Enabled', self::DOMAIN );
										break;
									default:
										esc_html_e( 'Enabled', self::DOMAIN );
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Data Export', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Method of data export', self::DOMAIN ); ?></td><td>
								<?php
								switch ( $this->data_export_mode ) {
									case self::OPT_COMMON_DATA_EXPORT_MANUAL:
										esc_html_e( 'Manual', self::DOMAIN );
										break;
									case self::OPT_COMMON_DATA_EXPORT_SCHEDULER:
										esc_html_e( 'Scheduler', self::DOMAIN );
										break;
								}
								?>
								</td>
							</tr>
							<?php
							if ( self::OPT_COMMON_DATA_EXPORT_SCHEDULER === $this->data_export_mode ) {
							?>
							<tr>
								<td class="dummy"></td>
								<td><?php esc_html_e( 'Data Export', self::DOMAIN ); ?></td>
								<td><?php esc_html_e( 'Interval exporting share count to a csv file', self::DOMAIN ); ?></td>
								<td><?php echo esc_html( $this->data_export_interval / 3600 ) . ' ' . esc_html( __( 'hours', self::DOMAIN ) ); ?></td>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="metabox-holder">
			<form action="admin.php?page=scc-setting" method="post">
				<?php wp_nonce_field( __FILE__, '_wpnonce' ); ?>
				<div id="share-base-cache" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e('Share Base Cache', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Target SNS', self::DOMAIN ); ?></label></th>
								<td>
									<div class="sns-check">
										<input type="checkbox" value="1" id="share_base_cache_target_facebook" name="share_base_cache_target_facebook"<?php if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_FACEBOOK ] ) ) echo ' checked="checked"'; ?> />
										<label for="share_base_cache_target_facebook"><?php esc_html_e( 'Facebook', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="share_base_cache_target_hatebu" name="share_base_cache_target_hatebu"<?php if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_HATEBU ] ) ) echo ' checked="checked"'; ?> />
										<label for="share_base_cache_target_hatebu"><?php esc_html_e( 'Hatena Bookmark', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="share_base_cache_target_linkedin" name="share_base_cache_target_linkedin"<?php if (  ! empty( $this->share_base_cache_target[ self::REF_SHARE_LINKEDIN ] ) ) echo ' checked="checked"'; ?> />
										<label for="share_base_cache_target_linkedin"><?php esc_html_e( 'Linkedin', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="share_base_cache_target_pinterest" name="share_base_cache_target_pinterest"<?php if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_PINTEREST ] ) ) echo ' checked="checked"'; ?> />
										<label for="share_base_cache_target_pinterest"><?php esc_html_e( 'Pinterest', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="share_base_cache_target_pocket" name="share_base_cache_target_pocket"<?php if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_POCKET ] ) ) echo ' checked="checked"'; ?> />
										<label for="share_base_cache_target_pocket"><?php esc_html_e( 'Pocket', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="share_base_cache_target_twitter" name="share_base_cache_target_twitter"<?php if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_TWITTER ] ) ) echo ' checked="checked"'; ?> />
										<label for="share_base_cache_target_twitter"><?php esc_html_e( 'Twitter', self::DOMAIN ); ?></label>
									</div>
								</td>
							</tr>
							<tr>
								<th><label for="share_base_custom_post_types"><?php esc_html_e( 'Custom post types', self::DOMAIN ); ?></label></th>
								<td>
									<input type="text" class="text" id="share_base_custom_post_types" name="share_base_custom_post_types" size="60" value="<?php echo esc_attr( implode( ',', $this->share_base_custom_post_types ) );  ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'e.g. aaa, bbb, ccc (comma-delimited)', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="share_base_check_interval"><?php esc_html_e( 'Interval cheking share count (sec)', self::DOMAIN ); ?></label></th>
								<td>
									<input type="number" class="number" id="share_base_check_interval" name="share_base_check_interval" value="<?php echo esc_attr( $this->share_base_check_interval ); ?>" step="1" kl_vkbd_parsed="true" />
									<span class="description"><?php esc_html_e( 'Default: 900', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="share_base_posts_per_check"><?php esc_html_e( 'Number of contents to check at a time', self::DOMAIN ); ?></label></th>
								<td>
									<input type="number" class="number" id="share_base_posts_per_check" name="share_base_posts_per_check" value="<?php echo esc_attr( $this->share_base_posts_per_check ); ?>" step="1" kl_vkbd_parsed="true" />
									<span class="description"><?php esc_html_e( 'Default: 15', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="scheme_migration_mode"><?php esc_html_e( 'Scheme migration mode from http to https', self::DOMAIN ); ?></label></th>
								<td>
									<select id="scheme_migration_mode" name="scheme_migration_mode">
										<option value="0"<?php if ( self::OPT_COMMON_SCHEME_MIGRATION_MODE_OFF === $this->scheme_migration_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Off', self::DOMAIN ); ?></option>
										<option value="1"<?php if ( self::OPT_COMMON_SCHEME_MIGRATION_MODE_ON === $this->scheme_migration_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'On', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Off', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php if ( $this->scheme_migration_mode ) { ?>
							<tr>
								<th><label for="scheme-migration-date"><?php esc_html_e( 'Scheme migration date from http to https', self::DOMAIN ); ?></label></th>
								<td>
									<input id="scheme-migration-date" type="text" class="text" name="scheme_migration_date" size="20" value="<?php echo esc_attr( $this->scheme_migration_date ); ?>" />
									<span class="description"><?php esc_html_e( 'Default: N/A', self::DOMAIN ); ?></span>
									<script>
										jQuery(document).ready(function() {
											jQuery('#scheme-migration-date').datepicker({
												dateFormat : 'yy/mm/dd'
											});
										});
									</script>
								</td>
							</tr>
						<?php } ?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
							<input type="submit" class="button button-secondary" name="clear_share_base_cache" value="<?php esc_html_e( 'Clear Cache', self::DOMAIN ); ?>">
						</div>
					</div>
				</div>
			<?php if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_FACEBOOK ] ) ) { ?>
				<div id="share-base-cache-facebook" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Share Base Cache - Facebook', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Developer page', self::DOMAIN ); ?></label></th>
								<td>
									<a href="https://developers.facebook.com/apps/" target="_blank">https://developers.facebook.com/apps/</a>
									<br>
									<span class="description"><?php esc_html_e( 'Register a application in Facebook developer page to get the following App ID and App Secret.', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="share_facebook_app_id"><?php esc_html_e( 'App ID', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="share_facebook_app_id" class="text" name="share_facebook_app_id" size="60" value="<?php echo esc_attr( $this->share_facebook_app_id ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'App ID for Facebook API', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="share_facebook_app_secret"><?php esc_html_e( 'App Secret', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="share_facebook_app_secret" class="text" name="share_facebook_app_secret" size="60" value="<?php echo esc_attr( $this->share_facebook_app_secret ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'App Secret for Facebook API', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<?php if ( ! empty( $this->share_facebook_app_id ) && ! empty( $this->share_facebook_app_secret ) ) { ?>
								<tr>
									<th><label for="tmp_share_facebook_access_token"><?php esc_html_e( 'Access Token', self::DOMAIN ); ?></label></th>
									<td>
										<input type="password" id="tmp_share_facebook_access_token" class="text" name="tmp_share_facebook_access_token" size="60" value="<?php echo esc_attr( $this->share_facebook_access_token ); ?>" readonly />
										<br>
										<span class="description"><?php esc_html_e( 'Access Token for Facebook API', self::DOMAIN ); ?></span>
									</td>
								</tr>
							<?php } ?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
			<?php } // End if(). ?>
			<?php if ( ! empty( $this->share_base_cache_target[ self::REF_SHARE_TWITTER ] ) ) { ?>
				<div id="share-base-cache-twitter" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Share Base Cache - Twitter', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="share_alternative_twitter_api"><?php esc_html_e( 'Alternative Twitter API', self::DOMAIN ); ?></label></th>
								<td>
									<select id="share_alternative_twitter_api" name="share_alternative_twitter_api">
										<option value="1"<?php if ( self::OPT_SHARE_TWITTER_API_JSOON === $this->share_base_twitter_api ) echo ' selected="selected"'; ?>><?php echo esc_html( 'widgetoon.js & count.jsoon' ); ?></option>
										<option value="2"<?php if ( self::OPT_SHARE_TWITTER_API_OPENSHARECOUNT === $this->share_base_twitter_api ) echo ' selected="selected"'; ?>><?php echo esc_html( 'OpenShareCount' ); ?></option>
										<option value="3"<?php if ( self::OPT_SHARE_TWITTER_API_TWITCOUNT === $this->share_base_twitter_api ) echo ' selected="selected"'; ?>><?php echo esc_html( 'TwitCount' ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: widgetoon.js & count.jsoon' ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Registration destination', self::DOMAIN ); ?></label></th>
								<td>
									<?php
									if ( self::OPT_SHARE_TWITTER_API_JSOON === $this->share_base_twitter_api ) {
										echo '<a href="' . esc_url( 'https://jsoon.digitiminimi.com/' ) . '" target="_blank">' . esc_html( 'https://jsoon.digitiminimi.com/' ) . '</a>';
									} elseif ( self::OPT_SHARE_TWITTER_API_OPENSHARECOUNT === $this->share_base_twitter_api ) {
										echo '<a href="' . esc_url( 'https://opensharecount.com/' ) . '" target="_blank">' . esc_html( 'https://opensharecount.com/' ) . '</a>';
									} elseif ( self::OPT_SHARE_TWITTER_API_TWITCOUNT === $this->share_base_twitter_api ) {
										echo '<a href="' . esc_url( 'http://twitcount.com/' ) . '" target="_blank">' . esc_html( 'http://twitcount.com/' ) . '</a>';
									}
									?>
									<br />
									<span class="description"><?php esc_html_e( 'You need to register information such as your domain with the above site in order to start counting.', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
			<?php } // End if(). ?>
				<div id="share-rush-cache" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Share Rush Cache', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="share_rush_new_content_term"><?php esc_html_e( 'Term considering posted content as new content', self::DOMAIN ); ?></label></th>
								<td>
									<select id="share_rush_new_content_term" name="share_rush_new_content_term">
										<option value="1"<?php if ( 1 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '1 day', self::DOMAIN ); ?></option>
										<option value="2"<?php if ( 2 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '2 days', self::DOMAIN ); ?></option>
										<option value="3"<?php if ( 3 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '3 days', self::DOMAIN ); ?></option>
										<option value="4"<?php if ( 4 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '4 days', self::DOMAIN ); ?></option>
										<option value="5"<?php if ( 5 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '5 days', self::DOMAIN ); ?></option>
										<option value="6"<?php if ( 6 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '6 days', self::DOMAIN ); ?></option>
										<option value="7"<?php if ( 7 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '7 days', self::DOMAIN ); ?></option>
										<option value="8"<?php if ( 8 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '8 days', self::DOMAIN ); ?></option>
										<option value="9"<?php if ( 9 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '9 days', self::DOMAIN ); ?></option>
										<option value="10"<?php if ( 10 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '10 days', self::DOMAIN ); ?></option>
										<option value="11"<?php if ( 11 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '11 days', self::DOMAIN ); ?></option>
										<option value="12"<?php if ( 12 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '12 days', self::DOMAIN ); ?></option>
										<option value="13"<?php if ( 13 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '13 days', self::DOMAIN ); ?></option>
										<option value="14"<?php if ( 14 === $this->share_rush_new_content_term ) echo ' selected="selected"'; ?>><?php esc_html_e( '14 days', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: 3 days', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="share_rush_check_interval"><?php esc_html_e( 'Interval cheking share count (sec)', self::DOMAIN ); ?></label></th>
								<td>
									<input type="number" id="share_rush_check_interval" class="number" name="share_rush_check_interval" value="<?php echo esc_attr( $this->share_rush_check_interval ); ?>" step="1" kl_vkbd_parsed="true" />
									<span class="description"><?php esc_html_e( 'Default: 900', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="share_rush_posts_per_check"><?php esc_html_e( 'Number of contents to check at a time', self::DOMAIN ); ?></label></th>
								<td>
									<input type="number" id="share_rush_posts_per_check" class="number" name="share_rush_posts_per_check" value="<?php echo esc_attr( $this->share_rush_posts_per_check ); ?>" step="1" kl_vkbd_parsed="true" />
									<span class="description"><?php esc_html_e( 'Default: 10', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
				<div id="share-variation-analysis" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Share Variation Analysis', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="share_variation_analysis_mode"><?php esc_html_e( 'Method to update basis of comparison', self::DOMAIN ); ?></label></th>
								<td>
									<select id="share_variation_analysis_mode" name="share_variation_analysis_mode">
										<option value="1"<?php if ( self::OPT_SHARE_VARIATION_ANALYSIS_NONE === $this->share_variation_analysis_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Disabled (None)', self::DOMAIN ); ?></option>
										<option value="2"<?php if ( self::OPT_SHARE_VARIATION_ANALYSIS_MANUAL === $this->share_variation_analysis_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Enabled (Manual)', self::DOMAIN ); ?></option>
										<option value="3"<?php if ( self::OPT_SHARE_VARIATION_ANALYSIS_SCHEDULER === $this->share_variation_analysis_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Enabled (Scheduler)', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Disabled (None)', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php
							if ( self::OPT_SHARE_VARIATION_ANALYSIS_SCHEDULER === $this->share_variation_analysis_mode ) {
								list( $cronstr['minutes'], $cronstr['hours'], $cronstr['mday'], $cronstr['mon'], $cronstr['wday'] ) = explode( ' ', $this->share_variation_analysis_schedule, 5 );
								if ( strstr( $cronstr['minutes'], '*/' ) ) {
									$minutes = explode( '/', $cronstr['minutes'] );
								} else {
									$minutes = explode( ',', $cronstr['minutes'] );
								}
								if ( strstr( $cronstr['hours'], '*/' ) ) {
									$hours = explode( '/', $cronstr['hours'] );
								} else {
									$hours = explode( ',', $cronstr['hours'] );
								}
								if ( strstr( $cronstr['mday'], '*/' ) ) {
									$mday = explode( '/', $cronstr['mday'] );
								} else {
									$mday = explode( ',', $cronstr['mday'] );
								}
								if ( strstr( $cronstr['mon'], '*/' ) ) {
									$mon = explode( '/', $cronstr['mon'] );
								} else {
									$mon = explode( ',', $cronstr['mon'] );
								}
								if ( strstr( $cronstr['wday'], '*/' ) ) {
									$wday = explode( '/', $cronstr['wday'] );
								} else {
									$wday = explode( ',', $cronstr['wday'] );
								}
						?>
							<tr class="a_wpcron">
								<th scope="row"><?php esc_html_e( 'Scheduler', self::DOMAIN ); ?></th>
								<td>
									<table class="wpcron">
										<tr>
											<th>
												<?php esc_html_e( 'Type', self::DOMAIN ); ?>
											</th>
											<th>
											</th>
											<th>
												<?php esc_html_e( 'Hour', self::DOMAIN ); ?>
											</th>
											<th>
												<?php esc_html_e( 'Minute', self::DOMAIN ); ?>
											</th>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-mon">
													<?php echo '<input class="radio" type="radio"' . checked( true, is_numeric( $mday[0] ), false ) . ' name="a_cronbtype" value="mon" /> ' . esc_html( __( 'monthly', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
												<select name="a_moncronmday">
													<?php
														for ( $i = 1; $i <= 31; $i ++ ) {
															$on_day = '';

															switch ( $i ) {
																case 1:
																	$on_day = __( 'on 1.', self::DOMAIN );
																	break;
																case 2:
																	$on_day = __( 'on 2.', self::DOMAIN );
																	break;
																case 3:
																	$on_day = __( 'on 3.', self::DOMAIN );
																	break;
																case 4:
																	$on_day = __( 'on 4.', self::DOMAIN );
																	break;
																case 5:
																	$on_day = __( 'on 5.', self::DOMAIN );
																	break;
																case 6:
																	$on_day = __( 'on 6.', self::DOMAIN );
																	break;
																case 7:
																	$on_day = __( 'on 7.', self::DOMAIN );
																	break;
																case 8:
																	$on_day = __( 'on 8.', self::DOMAIN );
																	break;
																case 9:
																	$on_day = __( 'on 9.', self::DOMAIN );
																	break;
																case 10:
																	$on_day = __( 'on 10.', self::DOMAIN );
																	break;
																case 11:
																	$on_day = __( 'on 11.', self::DOMAIN );
																	break;
																case 12:
																	$on_day = __( 'on 12.', self::DOMAIN );
																	break;
																case 13:
																	$on_day = __( 'on 13.', self::DOMAIN );
																	break;
																case 14:
																	$on_day = __( 'on 14.', self::DOMAIN );
																	break;
																case 15:
																	$on_day = __( 'on 15.', self::DOMAIN );
																	break;
																case 16:
																	$on_day = __( 'on 16.', self::DOMAIN );
																	break;
																case 17:
																	$on_day = __( 'on 17.', self::DOMAIN );
																	break;
																case 18:
																	$on_day = __( 'on 18.', self::DOMAIN );
																	break;
																case 19:
																	$on_day = __( 'on 19.', self::DOMAIN );
																	break;
																case 20:
																	$on_day = __( 'on 20.', self::DOMAIN );
																	break;
																case 21:
																	$on_day = __( 'on 21.', self::DOMAIN );
																	break;
																case 22:
																	$on_day = __( 'on 22.', self::DOMAIN );
																	break;
																case 23:
																	$on_day = __( 'on 23.', self::DOMAIN );
																	break;
																case 24:
																	$on_day = __( 'on 24.', self::DOMAIN );
																	break;
																case 25:
																	$on_day = __( 'on 25.', self::DOMAIN );
																	break;
																case 26:
																	$on_day = __( 'on 26.', self::DOMAIN );
																	break;
																case 27:
																	$on_day = __( 'on 27.', self::DOMAIN );
																	break;
																case 28:
																	$on_day = __( 'on 28.', self::DOMAIN );
																	break;
																case 29:
																	$on_day = __( 'on 29.', self::DOMAIN );
																	break;
																case 30:
																	$on_day = __( 'on 30.', self::DOMAIN );
																	break;
																case 31:
																	$on_day = __( 'on 31.', self::DOMAIN );
																	break;
															}

															echo '<option ' . selected( in_array( "$i", $mday, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $on_day ) . '</option>';
														} // End if().
													?>
												</select>
											</td>
											<td>
												<select name="a_moncronhours">
													<?php for ( $i = 0; $i < 24; $i ++ ) {
														echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
											<td>
												<select name="a_moncronminutes">
													<?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
														echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-week">
													<?php echo '<input class="radio" type="radio"' . checked( true, is_numeric( $wday[0] ), false ) . ' name="a_cronbtype" value="week" /> ' . esc_html( __( 'weekly', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
												<select name="a_weekcronwday">
													<?php
														echo '<option ' . selected( in_array( '0', $wday, true ), true, false ) . '  value="0" />' . esc_html( __( 'Sunday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '1', $wday, true ), true, false ) . '  value="1" />' . esc_html( __( 'Monday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '2', $wday, true ), true, false ) . '  value="2" />' . esc_html( __( 'Tuesday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '3', $wday, true ), true, false ) . '  value="3" />' . esc_html( __( 'Wednesday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '4', $wday, true ), true, false ) . '  value="4" />' . esc_html( __( 'Thursday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '5', $wday, true ), true, false ) . '  value="5" />' . esc_html( __( 'Friday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '6', $wday, true ), true, false ) . '  value="6" />' . esc_html( __( 'Saturday', self::DOMAIN ) ) . '</option>';
													?>
												</select>
											</td>
											<td>
												<select name="a_weekcronhours">
													<?php for ( $i = 0; $i < 24; $i ++ ) {
														echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
											<td>
												<select name="a_weekcronminutes">
													<?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
														echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-day">
													<?php echo '<input class="radio" type="radio"' . checked( "**", $mday[0] . $wday[0], false ) . ' name="a_cronbtype" value="day" /> ' . esc_html( __( 'daily', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
											</td>
											<td>
												<select name="a_daycronhours">
													<?php
														for ( $i = 0; $i < 24; $i ++ ) {
															echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
											<td>
												<select name="a_daycronminutes">
													<?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
														echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						<?php
							}
						?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
							<input type="submit" class="button button-secondary" name="update_share_comparison_base" value="<?php esc_html_e( 'Update Basis of Comparison', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
				<div id="follow-base-cache" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Base Cache', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Target SNS', self::DOMAIN ); ?></label></th>
								<td>
									<div class="sns-check">
										<input type="checkbox" value="1" id="follow_base_cache_target_facebook" name="follow_base_cache_target_facebook"<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FACEBOOK ] ) ) echo ' checked="checked"'; ?> />
										<label for="follow_base_cache_target_facebook"><?php esc_html_e( 'Facebook', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="follow_base_cache_target_feedly" name="follow_base_cache_target_feedly"<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FEEDLY ] ) ) echo ' checked="checked"'; ?> />
										<label for="follow_base_cache_target_feedly"><?php esc_html_e( 'Feedly', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="follow_base_cache_target_instagram" name="follow_base_cache_target_instagram"<?php if ( ! empty( $this->follow_base_cache_target[self::REF_FOLLOW_INSTAGRAM] ) ) echo ' checked="checked"'; ?> />
										<label for="follow_base_cache_target_instagram"><?php esc_html_e( 'Instagram', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="follow_base_cache_target_onesignal" name="follow_base_cache_target_onesignal"<?php if ( ! empty( $this->follow_base_cache_target[self::REF_FOLLOW_ONESIGNAL] ) ) echo ' checked="checked"'; ?> />
										<label for="follow_base_cache_target_onesignal"><?php esc_html_e( 'OneSignal', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="follow_base_cache_target_push7" name="follow_base_cache_target_push7"<?php if ( ! empty( $this->follow_base_cache_target[self::REF_FOLLOW_PUSH7] ) ) echo ' checked="checked"'; ?> />
										<label for="follow_base_cache_target_push7"><?php esc_html_e( 'Push7', self::DOMAIN ); ?></label>
									</div>
									<div class="sns-check">
										<input type="checkbox" value="1" id="follow_base_cache_target_twitter" name="follow_base_cache_target_twitter"<?php if ( ! empty( $this->follow_base_cache_target[self::REF_FOLLOW_TWITTER] ) ) echo ' checked="checked"'; ?> />
										<label for="follow_base_cache_target_twitter"><?php esc_html_e( 'Twitter', self::DOMAIN ); ?></label>
									</div>
								</td>
							</tr>
							<tr>
								<th><label for="follow_base_check_interval"><?php esc_html_e( 'Interval cheking follower count (sec)', self::DOMAIN ); ?></label></th>
								<td>
									<input type="number" class="number" id="follow_base_check_interval" name="follow_base_check_interval" value="<?php echo esc_attr( $this->follow_base_check_interval ); ?>" step="1" kl_vkbd_parsed="true" />
									<span class="description"><?php esc_html_e( 'Default: 86400 Minimum: 3600', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
							<input type="submit" class="button button-secondary" name="direct_follow_base_cache" value="<?php esc_html_e( 'Cache', self::DOMAIN ); ?>">
							<input type="submit" class="button button-secondary" name="clear_follow_base_cache" value="<?php esc_html_e( 'Clear Cache', self::DOMAIN ); ?>">
						</div>
					</div>
				</div>
			<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FACEBOOK ] ) ) { ?>
				<div id="follow-base-cache-facebook" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Base Cache - Facebook', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="follow_facebook_page_id"><?php esc_html_e( 'Page ID', self::DOMAIN ); ?></label></th>
								<td>
									<input type="text" class="text" id="follow_facebook_page_id" name="follow_facebook_page_id" size="30" value="<?php echo esc_attr( $this->follow_facebook_page_id ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'Facebook page ID that you want to get follower count', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Developer page', self::DOMAIN ); ?></label></th>
								<td>
									<a href="https://developers.facebook.com/apps/" target="_blank">https://developers.facebook.com/apps/</a>
									<br>
									<span class="description"><?php esc_html_e( 'Register a application in Facebook developer page to get the following App ID and App Secret.', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_facebook_app_id"><?php esc_html_e( 'App ID', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_facebook_app_id" class="text" name="follow_facebook_app_id" size="60" value="<?php echo esc_attr( $this->follow_facebook_app_id ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'App ID for Facebook API', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_facebook_app_secret"><?php esc_html_e( 'App Secret', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_facebook_app_secret" class="text" name="follow_facebook_app_secret" size="60" value="<?php echo esc_attr( $this->follow_facebook_app_secret ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'App Secret for Facebook API', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="tmp_follow_facebook_redirect_uri"><?php esc_html_e( 'Redirect URI', self::DOMAIN ); ?></label></th>
								<td>
									<input type="text" id="tmp_follow_facebook_redirect_uri" class="text" name="tmp_follow_facebook_redirect_uri" size="80" value="<?php echo esc_url( plugins_url() . '/sns-count-cache/?action=facebook-auth' ); ?>" onclick="this.focus();this.select()" title="<?php esc_html_e( 'To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', self::DOMAIN );  ?>" readonly />
									<br>
									<span class="description"><?php  esc_html_e( 'Copy and set this to the field of "Valid OAuth redirect URIs" in application management page of Facebook developer page.',  self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php if ( ! empty( $_GET['action'] ) && wp_unslash( $_GET['action'] ) === 'facebook-auth' ) { ?>
							<tr>
								<th><label for="tmp_follow_facebook_access_token"><?php esc_html_e( 'Access Token', self::DOMAIN ); ?></label></th>
								<td>
									<?php if ( ! is_wp_error( $tmp_facebook_access_token ) ) {  ?>
										<input type="text" id="tmp_follow_facebook_access_token" class="text" name="tmp_follow_facebook_access_token" size="80" value="<?php echo esc_attr( $tmp_facebook_access_token ); ?>" onclick="this.focus();this.select()" title="<?php esc_html_e( 'To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', self::DOMAIN );  ?>" readonly />
										<br>
										<span class="description"><?php  esc_html_e( 'Copy and pase this into the fields below.',  self::DOMAIN ); ?></span>
									<?php } else { ?>
										<span class="update-message notice-error"><p><?php echo esc_html( $tmp_facebook_access_token->get_error_message() ); ?></p></span>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
						<?php if ( ! empty( $this->follow_facebook_page_id ) && ! empty( $this->follow_facebook_app_id ) && ! empty( $this->follow_facebook_app_secret ) ) { ?>
							<tr>
								<th><label for="follow_facebook_access_token"><?php esc_html_e( 'Access Token', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_facebook_access_token" class="text" name="follow_facebook_access_token" size="80" value="<?php echo esc_attr( $this->follow_facebook_access_token ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'Access Token for Facebook API', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php } ?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						<?php if ( ! empty( $this->follow_facebook_page_id ) && ! empty( $this->follow_facebook_app_id ) && ! empty( $this->follow_facebook_app_secret ) ) { ?>
							<a href="https://www.facebook.com/dialog/oauth?client_id=<?php echo esc_attr( $this->follow_facebook_app_id ); ?>&scope=manage_pages,read_insights&state=<?php echo wp_create_nonce( 'facebook-auth' ); ?>&redirect_uri=<?php echo esc_url( plugins_url() ) . '/sns-count-cache/'; ?>?action=facebook-auth" class="button button-secondary"><?php esc_html_e( 'Get Access Token', self::DOMAIN ); ?></a>
						<?php } ?>
						</div>
					</div>
				</div>
			<?php } // End if(). ?>
			<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_FEEDLY ] ) ) { ?>
				<div id="follow-base-cache-feedly" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Base Cache - Feedly', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="follow_feed_type"><?php esc_html_e( 'Target feed type', self::DOMAIN ); ?></label></th>
								<td>
									<select id="follow_feed_type" name="follow_feed_type">
										<option value="default"<?php if ( self::OPT_FEED_TYPE_DEFAULT === $this->follow_feed_type ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Default', self::DOMAIN ) ?></option>
										<option value="rss"<?php if ( self::OPT_FEED_TYPE_RSS === $this->follow_feed_type ) echo ' selected="selected"'; ?>><?php esc_html_e( 'RSS', self::DOMAIN ); ?></option>
										<option value="rss2"<?php if ( self::OPT_FEED_TYPE_RSS2 === $this->follow_feed_type ) echo ' selected="selected"'; ?>><?php esc_html_e( 'RSS2', self::DOMAIN ); ?></option>
										<option value="rdf"<?php if ( self::OPT_FEED_TYPE_RDF === $this->follow_feed_type ) echo ' selected="selected"'; ?>><?php esc_html_e( 'RDF', self::DOMAIN ); ?></option>
										<option value="atom"<?php if ( self::OPT_FEED_TYPE_ATOM === $this->follow_feed_type ) echo ' selected="selected"'; ?>><?php esc_html_e( 'ATOM', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Default', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Target feed', self::DOMAIN ); ?></label></th>
								<td><a href="<?php echo esc_url( get_feed_link( $this->follow_feed_type ) ); ?>" target="_blank"><?php echo esc_html( get_feed_link( $this->follow_feed_type ) ); ?></a></td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_INSTAGRAM ] ) ) { ?>
				<div id="follow-base-cache-instagram" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Base Cache - Instagram', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Developer page', self::DOMAIN ); ?></label></th>
								<td>
									<a href="https://www.instagram.com/developer/clients/manage/" target="_blank">https://www.instagram.com/developer/clients/manage/</a>
									<br>
									<span class="description"><?php esc_html_e( 'Register a client in Instagram developer page to get the following Client ID and Client Secret.', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_instagram_client_id"><?php esc_html_e( 'Client ID', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_instagram_client_id" class="text" name="follow_instagram_client_id" size="60" value="<?php echo esc_attr( $this->follow_instagram_client_id ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'Client ID for Instagram API', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_instagram_client_secret"><?php esc_html_e( 'Client Secret', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_instagram_client_secret" class="text" name="follow_instagram_client_secret" size="60" value="<?php echo esc_attr( $this->follow_instagram_client_secret ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'Client Secret for Instagram API', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="tmp_follow_instagram_redirect_uri"><?php esc_html_e( 'Redirect URI', self::DOMAIN ); ?></label></th>
								<td>
									<input type="text" id="tmp_follow_instagram_redirect_uri" class="text" name="tmp_follow_instagram_redirect_uri" size="80" value="<?php echo esc_url( plugins_url() . '/sns-count-cache/?action=instagram-auth' ); ?>" onclick="this.focus();this.select()" title="<?php esc_html_e( 'To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', self::DOMAIN );  ?>" readonly />
									<br>
									<span class="description"><?php  esc_html_e( 'Copy and set this to the field of "Valid redirect URIs" in the client management page of Instagram developer page.',  self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php if ( ! empty( $_GET['action'] ) && wp_unslash( $_GET['action'] ) === 'instagram-auth' ) { ?>
							<tr>
								<th><label for="tmp_follow_instagram_access_token"><?php esc_html_e( 'Access Token', self::DOMAIN ); ?></label></th>
								<td>
									<?php if ( ! is_wp_error( $tmp_instagram_access_token ) ) {  ?>
										<input type="text" id="tmp_follow_instagram_access_token" class="text" name="tmp_follow_instagram_access_token" size="80" value="<?php echo esc_attr( $tmp_instagram_access_token ); ?>" onclick="this.focus();this.select()" title="<?php esc_html_e( 'To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', self::DOMAIN );  ?>" readonly />
										<br>
										<span class="description"><?php esc_html_e( 'Copy and pase this into the fields below.',  self::DOMAIN ); ?></span>
									<?php } else { ?>
										<span class="update-message notice-error"><p><?php echo esc_html( $tmp_instagram_access_token->get_error_message() ); ?></p></span>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
						<?php if ( ! empty( $this->follow_instagram_client_id ) && ! empty( $this->follow_instagram_client_secret ) ) { ?>
							<tr>
								<th><label for="follow_instagram_access_token"><?php esc_html_e( 'Access Token', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_instagram_access_token" class="text" name="follow_instagram_access_token" size="80" value="<?php echo esc_attr( $this->follow_instagram_access_token ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'Access Token for Instagram API', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php } ?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						<?php if ( ! empty( $this->follow_instagram_client_id ) && ! empty( $this->follow_instagram_client_secret ) ) { ?>
							<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo esc_attr( $this->follow_instagram_client_id ); ?>&response_type=code&state=<?php echo wp_create_nonce( 'instagram-auth' ); ?>&redirect_uri=<?php echo esc_url( plugins_url() ) . '/sns-count-cache/'; ?>?action=instagram-auth" class="button button-secondary"><?php esc_html_e( 'Get Access Token', self::DOMAIN ); ?></a>
						<?php } ?>
						</div>
					</div>
				</div>
			<?php } // End if(). ?>
			<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_ONESIGNAL ] ) ) { ?>
				<div id="follow-base-cache-onesignal" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Base Cache - OneSignal', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Home page', self::DOMAIN ); ?></label></th>
								<td>
									<a href="https://onesignal.com/" target="_blank">https://onesignal.com/</a>
									<br>
									<span class="description"><?php esc_html_e( 'Register a application in OneSignal page to get the following App ID and User Auth Key.', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_onesignal_app_id"><?php esc_html_e( 'OneSignal App ID', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" class="text" id="follow_onesignal_app_id" name="follow_onesignal_app_id" size="60" value="<?php echo esc_attr( $this->follow_onesignal_app_id ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'OneSignal app ID that you want to get follower count', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_onesignal_rest_api_key"><?php esc_html_e( 'REST API Key', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_onesignal_rest_api_key" class="text" name="follow_onesignal_rest_api_key" size="60" value="<?php echo esc_attr( $this->follow_onesignal_rest_api_key ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'REST API Key for OneSignal API', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
			<?php } // End if(). ?>
			<?php if ( ! empty( $this->follow_base_cache_target[self::REF_FOLLOW_PUSH7] ) ) { ?>
				<div id="follow-base-cache-push7" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Base Cache - Push7', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Push7 page', self::DOMAIN ); ?></label></th>
								<td>
									<a href="https://push7.jp/" target="_blank">https://push7.jp/</a>
									<br>
									<span class="description"><?php esc_html_e( 'Register a application in Push7 page to get the following App Number.', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_push7_app_number"><?php esc_html_e( 'App Number', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_push7_app_number" class="text" name="follow_push7_app_number" size="60" value="<?php echo esc_attr( $this->follow_push7_app_number ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'App Number for Push7 API', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ( ! empty( $this->follow_base_cache_target[ self::REF_FOLLOW_TWITTER ] ) ) { ?>
				<div id="follow-base-cache-twitter" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Base Cache - Twitter', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="follow_twitter_screen_name"><?php esc_html_e( 'Screen Name', self::DOMAIN ); ?></label></th>
								<td>
									<span class="at-mark">@</span>
									<input type="text" id="follow_twitter_screen_name" class="text" name="follow_twitter_screen_name" size="30" value="<?php echo esc_attr( $this->follow_twitter_screen_name ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'Twitter screen name that you want to get follower count', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Developer page', self::DOMAIN ); ?></label></th>
								<td>
									<a href="https://apps.twitter.com" target="_blank">https://apps.twitter.com</a>
									<br>
									<span class="description"><?php esc_html_e( 'Register a application in Twitter developer page to get the following Consumer Key and Consumer Secret.', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_twitter_api_key"><?php esc_html_e( 'API Key', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_twitter_api_key" class="text" name="follow_twitter_api_key" size="60" value="<?php echo esc_attr( $this->follow_twitter_api_key ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'API key for Twitter API', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="follow_twitter_api_secret_key"><?php esc_html_e( 'API Secret Key', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_twitter_api_secret_key" class="text" name="follow_twitter_api_secret_key" size="60" value="<?php echo esc_attr( $this->follow_twitter_api_secret_key ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'API Secret Key for Twitter API', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php if ( isset( $_POST['get_tiwtter_bearer_token'] ) && wp_unslash( $_POST['get_tiwtter_bearer_token'] ) === __( 'Get Bearer Token', self::DOMAIN ) ) { ?>
							<tr>
								<th><label for="tmp_follow_twitter_bearer_token"><?php esc_html_e( 'Bearer Token', self::DOMAIN ); ?></label></th>
								<td>
									<?php if ( ! is_wp_error( $tmp_twitter_bearer_token ) ) {  ?>
										<input type="text" id="tmp_follow_twitter_bearer_token" class="text" name="tmp_follow_twitter_bearer_token" size="80" value="<?php echo esc_attr( $tmp_twitter_bearer_token ); ?>" onclick="this.focus();this.select()" title="<?php esc_html_e( 'To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', self::DOMAIN );  ?>" readonly />
										<br>
										<span class="description"><?php  esc_html_e( 'Copy and pase this into the fields below.',  self::DOMAIN ); ?></span>
									<?php } else { ?>
										<span class="update-message notice-error"><p><?php echo esc_html( $tmp_twitter_bearer_token->get_error_message() ); ?></p></span>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
						<?php if ( ! empty( $this->follow_twitter_api_key ) && ! empty( $this->follow_twitter_api_secret_key ) ) { ?>
							<tr>
								<th><label for="follow_twitter_bearer_token"><?php esc_html_e( 'Bearer Token', self::DOMAIN ); ?></label></th>
								<td>
									<input type="password" id="follow_twitter_bearer_token" class="text" name="follow_twitter_bearer_token" size="80" value="<?php echo esc_attr( $this->follow_twitter_bearer_token ); ?>" />
									<br>
									<span class="description"><?php esc_html_e( 'Bearer Token for Twitter API', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php } ?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						<?php if ( ! empty( $this->follow_twitter_api_key ) && ! empty( $this->follow_twitter_api_secret_key ) ) { ?>
							<input type="submit" class="button button-secondary" name="get_tiwtter_bearer_token" value="<?php esc_html_e( 'Get Bearer Token', self::DOMAIN ); ?>" />
						<?php } ?>
						</div>
					</div>
				</div>
			<?php } // End if(). ?>
				<div id="follow-variation-analysis" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Follow Variation Analysis', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="follow_variation_analysis_mode"><?php esc_html_e( 'Method to update basis of comparison', self::DOMAIN ); ?></label></th>
								<td>
									<select id="follow_variation_analysis_mode" name="follow_variation_analysis_mode">
										<option value="1"<?php if ( self::OPT_FOLLOW_VARIATION_ANALYSIS_NONE === $this->follow_variation_analysis_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Disabled (None)', self::DOMAIN ); ?></option>
										<option value="2"<?php if ( self::OPT_FOLLOW_VARIATION_ANALYSIS_MANUAL === $this->follow_variation_analysis_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Enabled (Manual)', self::DOMAIN ); ?></option>
										<option value="3"<?php if ( self::OPT_FOLLOW_VARIATION_ANALYSIS_SCHEDULER === $this->follow_variation_analysis_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Enabled (Scheduler)', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Disabled (None)', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php
							if ( self::OPT_FOLLOW_VARIATION_ANALYSIS_SCHEDULER === $this->follow_variation_analysis_mode ) {
								list( $cronstr['minutes'], $cronstr['hours'], $cronstr['mday'], $cronstr['mon'], $cronstr['wday'] ) = explode( ' ', $this->follow_variation_analysis_schedule, 5 );
								if ( strstr( $cronstr['minutes'], '*/' ) ) {
									$minutes = explode( '/', $cronstr['minutes'] );
								} else {
									$minutes = explode( ',', $cronstr['minutes'] );
								}
								if ( strstr( $cronstr['hours'], '*/' ) ) {
									$hours = explode( '/', $cronstr['hours'] );
								} else {
									$hours = explode( ',', $cronstr['hours'] );
								}
								if ( strstr( $cronstr['mday'], '*/' ) ) {
									$mday = explode( '/', $cronstr['mday'] );
								} else {
									$mday = explode( ',', $cronstr['mday'] );
								}
								if ( strstr( $cronstr['mon'], '*/' ) ) {
									$mon = explode( '/', $cronstr['mon'] );
								} else {
									$mon = explode( ',', $cronstr['mon'] );
								}
								if ( strstr( $cronstr['wday'], '*/' ) ) {
									$wday = explode( '/', $cronstr['wday'] );
								} else {
									$wday = explode( ',', $cronstr['wday'] );
								}
						?>
							<tr class="a_wpcron">
								<th scope="row"><?php esc_html_e( 'Scheduler', self::DOMAIN ); ?></th>
								<td>
									<table class="wpcron">
										<tr>
											<th>
												<?php esc_html_e( 'Type', self::DOMAIN ); ?>
											</th>
											<th>
											</th>
											<th>
												<?php esc_html_e( 'Hour', self::DOMAIN ); ?>
											</th>
											<th>
												<?php esc_html_e( 'Minute', self::DOMAIN ); ?>
											</th>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-mon">
													<?php echo '<input class="radio" type="radio"' . checked( true, is_numeric( $mday[0] ), false ) . ' name="b_cronbtype" value="mon" /> ' . esc_html( __( 'monthly', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
												<select name="b_moncronmday">
													<?php
														for ( $i = 1; $i <= 31; $i ++ ) {
															$on_day = '';

															switch ( $i ) {
																case 1:
																	$on_day = __( 'on 1.', self::DOMAIN );
																	break;
																case 2:
																	$on_day = __( 'on 2.', self::DOMAIN );
																	break;
																case 3:
																	$on_day = __( 'on 3.', self::DOMAIN );
																	break;
																case 4:
																	$on_day = __( 'on 4.', self::DOMAIN );
																	break;
																case 5:
																	$on_day = __( 'on 5.', self::DOMAIN );
																	break;
																case 6:
																	$on_day = __( 'on 6.', self::DOMAIN );
																	break;
																case 7:
																	$on_day = __( 'on 7.', self::DOMAIN );
																	break;
																case 8:
																	$on_day = __( 'on 8.', self::DOMAIN );
																	break;
																case 9:
																	$on_day = __( 'on 9.', self::DOMAIN );
																	break;
																case 10:
																	$on_day = __( 'on 10.', self::DOMAIN );
																	break;
																case 11:
																	$on_day = __( 'on 11.', self::DOMAIN );
																	break;
																case 12:
																	$on_day = __( 'on 12.', self::DOMAIN );
																	break;
																case 13:
																	$on_day = __( 'on 13.', self::DOMAIN );
																	break;
																case 14:
																	$on_day = __( 'on 14.', self::DOMAIN );
																	break;
																case 15:
																	$on_day = __( 'on 15.', self::DOMAIN );
																	break;
																case 16:
																	$on_day = __( 'on 16.', self::DOMAIN );
																	break;
																case 17:
																	$on_day = __( 'on 17.', self::DOMAIN );
																	break;
																case 18:
																	$on_day = __( 'on 18.', self::DOMAIN );
																	break;
																case 19:
																	$on_day = __( 'on 19.', self::DOMAIN );
																	break;
																case 20:
																	$on_day = __( 'on 20.', self::DOMAIN );
																	break;
																case 21:
																	$on_day = __( 'on 21.', self::DOMAIN );
																	break;
																case 22:
																	$on_day = __( 'on 22.', self::DOMAIN );
																	break;
																case 23:
																	$on_day = __( 'on 23.', self::DOMAIN );
																	break;
																case 24:
																	$on_day = __( 'on 24.', self::DOMAIN );
																	break;
																case 25:
																	$on_day = __( 'on 25.', self::DOMAIN );
																	break;
																case 26:
																	$on_day = __( 'on 26.', self::DOMAIN );
																	break;
																case 27:
																	$on_day = __( 'on 27.', self::DOMAIN );
																	break;
																case 28:
																	$on_day = __( 'on 28.', self::DOMAIN );
																	break;
																case 29:
																	$on_day = __( 'on 29.', self::DOMAIN );
																	break;
																case 30:
																	$on_day = __( 'on 30.', self::DOMAIN );
																	break;
																case 31:
																	$on_day = __( 'on 31.', self::DOMAIN );
																	break;
															} // End switch().

															echo '<option ' . selected( in_array( "$i", $mday, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $on_day ) . '</option>';
														} // End for().
													?>
												</select>
											</td>
											<td>
												<select name="b_moncronhours">
													<?php for ( $i = 0; $i < 24; $i ++ ) {
														echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
											<td>
												<select name="b_moncronminutes">
													<?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
														echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-week">
													<?php echo '<input class="radio" type="radio"' . checked( true, is_numeric( $wday[0] ), false ) . ' name="a_cronbtype" value="week" /> ' . esc_html( __( 'weekly', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
												<select name="b_weekcronwday">
													<?php
														echo '<option ' . selected( in_array( '0', $wday, true ), true, false ) . '  value="0" />' . esc_html( __( 'Sunday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '1', $wday, true ), true, false ) . '  value="1" />' . esc_html( __( 'Monday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '2', $wday, true ), true, false ) . '  value="2" />' . esc_html( __( 'Tuesday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '3', $wday, true ), true, false ) . '  value="3" />' . esc_html( __( 'Wednesday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '4', $wday, true ), true, false ) . '  value="4" />' . esc_html( __( 'Thursday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '5', $wday, true ), true, false ) . '  value="5" />' . esc_html( __( 'Friday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '6', $wday, true ), true, false ) . '  value="6" />' . esc_html( __( 'Saturday', self::DOMAIN ) ) . '</option>';
													?>
												</select>
											</td>
											<td>
												<select name="b_weekcronhours">
													<?php for ( $i = 0; $i < 24; $i ++ ) {
														echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
											<td>
												<select name="b_weekcronminutes">
													<?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
														echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-day">
													<?php echo '<input class="radio" type="radio"' . checked( "**", $mday[0] . $wday[0], false ) . ' name="b_cronbtype" value="day" /> ' . esc_html( __( 'daily', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
											</td>
											<td>
												<select name="b_daycronhours">
													<?php
														for ( $i = 0; $i < 24; $i ++ ) {
															echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
											<td>
												<select name="b_daycronminutes">
													<?php for ( $i = 0; $i < 60; $i = $i + 5 ) {
														echo '<option ' . selected( in_array( "$i", $minutes, ture ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
													} ?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						<?php
							}
						?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
							<input type="submit" class="button button-secondary" name="update_follow_comparison_base" value="<?php esc_html_e( 'Update Basis of Comparison', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
				<div id="common-dynamic-cache" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Dynamic Cache', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="dynamic_cache_mode"><?php esc_html_e( 'Dynamic caching based on user access', self::DOMAIN ); ?></label></th>
								<td>
									<select id="dynamic_cache_mode" name="dynamic_cache_mode">
										<option value="1"<?php if ( self::OPT_COMMON_ACCESS_BASED_CACHE_OFF === $this->dynamic_cache_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Disabled', self::DOMAIN ); ?></option>
										<option value="5"<?php if ( self::OPT_COMMON_ACCESS_BASED_CACHE_ON === $this->dynamic_cache_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Enabled', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Disabled', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
				<div id="common-fault-tolerance" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Fault Tolerance', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="fault_tolerance_mode"><?php esc_html_e( 'Fault tolerant mode of count retrieval', self::DOMAIN ); ?></label></th>
								<td>
									<select id="fault_tolerance_mode" name="fault_tolerance_mode">
										<option value="1"<?php if ( self::OPT_COMMON_FAULT_TOLERANCE_OFF === $this->fault_tolerance_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Disabled', self::DOMAIN ); ?></option>
										<option value="2"<?php if ( self::OPT_COMMON_FAULT_TOLERANCE_ON === $this->fault_tolerance_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Enabled', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Disabled', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
				<div id="common-data-crawler" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Data Crawler', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Crawl method', self::DOMAIN ); ?></label></th>
								<td>
									<?php
										switch ( $this->crawler_method ) {
											case self::OPT_COMMON_CRAWLER_METHOD_NORMAL:
												esc_html_e( 'Normal (Sequential Retrieval)', self::DOMAIN );
												break;
											case self::OPT_COMMON_CRAWLER_METHOD_CURL:
												esc_html_e( 'Extended (Parallel Retrieval)', self::DOMAIN );
												break;
										}
									?>
								</td>
							</tr>
							<tr>
								<th><label for="common_data_crawler_retry_limit"><?php esc_html_e( 'Crawl retry limit', self::DOMAIN ); ?></label></th>
								<td>
									<select id="common_data_crawler_retry_limit" name="common_data_crawler_retry_limit">
										<option value="-1"<?php if ( -1 === $this->crawler_retry_limit ) echo ' selected="selected"'; ?>><?php esc_html_e( 'no retry', self::DOMAIN ); ?></option>
										<option value="1"<?php if ( 1 === $this->crawler_retry_limit ) echo ' selected="selected"'; ?>><?php esc_html_e( '1 time', self::DOMAIN ); ?></option>
										<option value="2"<?php if ( 2 === $this->crawler_retry_limit ) echo ' selected="selected"'; ?>><?php esc_html_e( '2 times', self::DOMAIN ); ?></option>
										<option value="3"<?php if ( 3 === $this->crawler_retry_limit ) echo ' selected="selected"'; ?>><?php esc_html_e( '3 times', self::DOMAIN ); ?></option>
										<option value="4"<?php if ( 4 === $this->crawler_retry_limit ) echo ' selected="selected"'; ?>><?php esc_html_e( '4 times', self::DOMAIN ); ?></option>
										<option value="5"<?php if ( 5 === $this->crawler_retry_limit ) echo ' selected="selected"'; ?>><?php esc_html_e( '5 times', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: no retry', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="crawler_ssl_verification"><?php esc_html_e( 'SSL verification', self::DOMAIN ); ?></label></th>
								<td>
									<select id="crawler_ssl_verification" name="crawler_ssl_verification">
										<option value="0"<?php if ( self::OPT_COMMON_CRAWLER_SSL_VERIFY_OFF === $this->crawler_ssl_verification ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Off', self::DOMAIN ); ?></option>
										<option value="1"<?php if ( self::OPT_COMMON_CRAWLER_SSL_VERIFY_ON === $this->crawler_ssl_verification ) echo ' selected="selected"'; ?>><?php esc_html_e( 'On', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: On', self::DOMAIN ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="crawl_throttling_mode"><?php esc_html_e( 'Crawl throttling mode', self::DOMAIN ); ?></label></th>
								<td>
									<select id="crawl_throttling_mode" name="crawl_throttling_mode">
										<option value="1"<?php if ( self::OPT_COMMON_CRAWL_THROTTLING_MODE_OFF === $this->crawl_throttling_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Disabled', self::DOMAIN ); ?></option>
										<option value="2"<?php if ( self::OPT_COMMON_CRAWL_THROTTLING_MODE_ON === $this->crawl_throttling_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Enabled', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Disabled', self::DOMAIN ); ?></span>
								</td>
							</tr>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
				<div id="common-data-export" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e( 'Data Export', self::DOMAIN ); ?></span></h3>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="data_export_mode"><?php esc_html_e( 'Method of data export', self::DOMAIN ); ?></label></th>
								<td>
									<select id="data_export_mode" name="data_export_mode">
										<option value="1"<?php if ( self::OPT_COMMON_DATA_EXPORT_MANUAL === $this->data_export_mode ) echo ' selected="selected"'; ?>><?php esc_html_e( 'Manual', self::DOMAIN ); ?></option>
										<option value="2"<?php if ( self::OPT_COMMON_DATA_EXPORT_SCHEDULER === $this->data_export_mode ) echo ' selected="selected"'; ?> disabled="disabled"><?php esc_html_e( 'Scheduler', self::DOMAIN ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( 'Default: Manual', self::DOMAIN ); ?></span>
								</td>
							</tr>
						<?php
							if ( self::OPT_COMMON_DATA_EXPORT_SCHEDULER === $this->data_export_mode ) {
								list( $cronstr[ 'minutes' ], $cronstr[ 'hours' ], $cronstr[ 'mday' ], $cronstr[ 'mon' ], $cronstr[ 'wday' ] ) = explode( ' ', $this->data_export_schedule, 5 );
								if ( strstr( $cronstr[ 'minutes' ], '*/' ) ) {
									$minutes = explode( '/', $cronstr[ 'minutes' ] );
								} else {
									$minutes = explode( ',', $cronstr[ 'minutes' ] );
								}
								if ( strstr( $cronstr[ 'hours' ], '*/' ) ) {
									$hours = explode( '/', $cronstr[ 'hours' ] );
								} else {
									$hours = explode( ',', $cronstr[ 'hours' ] );
								}
								if ( strstr( $cronstr[ 'mday' ], '*/' ) ) {
									$mday = explode( '/', $cronstr[ 'mday' ] );
								} else {
									$mday = explode( ',', $cronstr[ 'mday' ] );
								}
								if ( strstr( $cronstr[ 'mon' ], '*/' ) ) {
									$mon = explode( '/', $cronstr[ 'mon' ] );
								} else {
									$mon = explode( ',', $cronstr[ 'mon' ] );
								}
								if ( strstr( $cronstr[ 'wday' ], '*/' ) ) {
									$wday = explode( '/', $cronstr[ 'wday' ] );
								} else {
									$wday = explode( ',', $cronstr[ 'wday' ] );
								}
						?>
							<tr class="e_wpcron">
								<th scope="row"><?php esc_html_e( 'Scheduler', self::DOMAIN ); ?></th>
								<td>
									<table class="wpcron">
										<tr>
											<th>
												<?php esc_html_e( 'Type', self::DOMAIN ); ?>
											</th>
											<th>
											</th>
											<th>
												<?php esc_html_e( 'Hour', self::DOMAIN ); ?>
											</th>
											<th>
												<?php esc_html_e( 'Minute', self::DOMAIN ); ?>
											</th>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-mon">
													<?php echo '<input class="radio" type="radio"' . checked( true, is_numeric( $mday[0] ), false ) . ' name="e_cronbtype" value="mon" /> ' . esc_html( __( 'monthly', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
												<select name="e_moncronmday">
													<?php
														for ( $i = 1; $i <= 31; $i ++ ) {
															$on_day = '';

															switch ( $i ) {
																case 1:
																	$on_day = __( 'on 1.', self::DOMAIN );
																	break;
																case 2:
																	$on_day = __( 'on 2.', self::DOMAIN );
																	break;
																case 3:
																	$on_day = __( 'on 3.', self::DOMAIN );
																	break;
																case 4:
																	$on_day = __( 'on 4.', self::DOMAIN );
																	break;
																case 5:
																	$on_day = __( 'on 5.', self::DOMAIN );
																	break;
																case 6:
																	$on_day = __( 'on 6.', self::DOMAIN );
																	break;
																case 7:
																	$on_day = __( 'on 7.', self::DOMAIN );
																	break;
																case 8:
																	$on_day = __( 'on 8.', self::DOMAIN );
																	break;
																case 9:
																	$on_day = __( 'on 9.', self::DOMAIN );
																	break;
																case 10:
																	$on_day = __( 'on 10.', self::DOMAIN );
																	break;
																case 11:
																	$on_day = __( 'on 11.', self::DOMAIN );
																	break;
																case 12:
																	$on_day = __( 'on 12.', self::DOMAIN );
																	break;
																case 13:
																	$on_day = __( 'on 13.', self::DOMAIN );
																	break;
																case 14:
																	$on_day = __( 'on 14.', self::DOMAIN );
																	break;
																case 15:
																	$on_day = __( 'on 15.', self::DOMAIN );
																	break;
																case 16:
																	$on_day = __( 'on 16.', self::DOMAIN );
																	break;
																case 17:
																	$on_day = __( 'on 17.', self::DOMAIN );
																	break;
																case 18:
																	$on_day = __( 'on 18.', self::DOMAIN );
																	break;
																case 19:
																	$on_day = __( 'on 19.', self::DOMAIN );
																	break;
																case 20:
																	$on_day = __( 'on 20.', self::DOMAIN );
																	break;
																case 21:
																	$on_day = __( 'on 21.', self::DOMAIN );
																	break;
																case 22:
																	$on_day = __( 'on 22.', self::DOMAIN );
																	break;
																case 23:
																	$on_day = __( 'on 23.', self::DOMAIN );
																	break;
																case 24:
																	$on_day = __( 'on 24.', self::DOMAIN );
																	break;
																case 25:
																	$on_day = __( 'on 25.', self::DOMAIN );
																	break;
																case 26:
																	$on_day = __( 'on 26.', self::DOMAIN );
																	break;
																case 27:
																	$on_day = __( 'on 27.', self::DOMAIN );
																	break;
																case 28:
																	$on_day = __( 'on 28.', self::DOMAIN );
																	break;
																case 29:
																	$on_day = __( 'on 29.', self::DOMAIN );
																	break;
																case 30:
																	$on_day = __( 'on 30.', self::DOMAIN );
																	break;
																case 31:
																	$on_day = __( 'on 31.', self::DOMAIN );
																	break;
															}

															echo '<option ' . selected( in_array( "$i", $mday, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $on_day ) . '</option>';
														}
													?>
												</select>
											</td>
											<td>
												<select name="e_moncronhours">
													<?php
														for ( $i = 0; $i < 24; $i ++ ) {
															echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
											<td>
												<select name="e_moncronminutes">
													<?php
														for ( $i = 0; $i < 60; $i = $i + 5 ) {
															echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-week">
													<?php echo '<input class="radio" type="radio"' . checked( true, is_numeric( $wday[0] ), false ) . ' name="e_cronbtype" value="week" /> ' . esc_html( __( 'weekly', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
												<select name="e_weekcronwday">
													<?php
														echo '<option ' . selected( in_array( '0', $wday, true ), true, false ) . '  value="0" />' . esc_html( __( 'Sunday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '1', $wday, true ), true, false ) . '  value="1" />' . esc_html( __( 'Monday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '2', $wday, true ), true, false ) . '  value="2" />' . esc_html( __( 'Tuesday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '3', $wday, true ), true, false ) . '  value="3" />' . esc_html( __( 'Wednesday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '4', $wday, true ), true, false ) . '  value="4" />' . esc_html( __( 'Thursday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '5', $wday, true ), true, false ) . '  value="5" />' . esc_html( __( 'Friday', self::DOMAIN ) ) . '</option>';
														echo '<option ' . selected( in_array( '6', $wday, true ), true, false ) . '  value="6" />' . esc_html( __( 'Saturday', self::DOMAIN ) ) . '</option>';
													?>
												</select>
											</td>
											<td>
												<select name="e_weekcronhours">
													<?php
														for ( $i = 0; $i < 24; $i ++ ) {
															echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
											<td>
												<select name="e_weekcronminutes">
													<?php
														for ( $i = 0; $i < 60; $i = $i + 5 ) {
															echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-day">
													<?php echo '<input class="radio" type="radio"' . checked( "**", $mday[0] . $wday[0], FALSE ) . ' name="e_cronbtype" value="day" /> ' . esc_html( __( 'daily', self::DOMAIN ) ); ?>
												</label>
											</td>
											<td>
											</td>
											<td>
												<select name="e_daycronhours">
													<?php
														for ( $i = 0; $i < 24; $i ++ ) {
															echo '<option ' . selected( in_array( "$i", $hours, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
											<td>
												<select name="e_daycronminutes">
													<?php
														for ( $i = 0; $i < 60; $i = $i + 5 ) {
															echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<label for="idcronbtype-hour">
													<?php echo '<input class="radio" type="radio"' . checked( "*", $hours[0], false, false ) . ' name="e_cronbtype" value="hour" /> ' . __( 'hourly', self::DOMAIN ); ?>
												</label>
											</td>
											<td></td>
											<td></td>
											<td>
												<select name="e_hourcronminutes">
													<?php
														for ( $i = 0; $i < 60; $i = $i + 5 ) {
															echo '<option ' . selected( in_array( "$i", $minutes, true ), true, false ) . '  value="' . esc_attr( $i ) . '" />' . esc_html( $i ) . '</option>';
														}
													?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						<?php
							}
						?>
						</table>
						<div class="submit-button">
							<input type="submit" class="button button-primary" name="update_all_options" value="<?php esc_html_e( 'Update All Options', self::DOMAIN ); ?>" />
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="metabox-holder">
			<div id="common-exported-file" class="postbox">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span><?php esc_html_e( 'Exported File', self::DOMAIN ); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php esc_html_e( 'Disk usage of exported file', self::DOMAIN ); ?></th>
								<td>
									<?php
										$abs_path = WP_PLUGIN_DIR . '/sns-count-cache/data/sns-count-cache-data.csv';
										$file_size = SCC_File::get_file_size( $abs_path );

										if ( isset( $file_size ) ) {
											echo esc_html( $file_size );
										} else {
											esc_html_e( 'No exported file', self::DOMAIN );
										}
									?>
								</td>
							</tr>
						</tbody>
					</table>
					<form action="admin.php?page=scc-setting" method="post">
						<?php wp_nonce_field( __FILE__, '_wpnonce' ); ?>
						<table class="form-table">
							<tbody>
								<tr>
									<th><?php esc_html_e( 'Manual export', self::DOMAIN ); ?></th>
									<td>
										<input type="submit" class="button button-secondary" name="export_data" value="<?php esc_html_e( 'Export', self::DOMAIN ); ?>" />
										<br>
										<span class="description"><?php esc_html_e( 'Export share count to a csv file.', self::DOMAIN ); ?></span>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				<?php
					if ( file_exists( $abs_path ) ) {
				?>
					<form action="admin.php?page=scc-setting" method="post">
						<?php wp_nonce_field( __FILE__, '_wpnonce' ); ?>
						<table class="form-table">
							<tbody>
								<tr>
									<th><?php esc_html_e( 'Reset of exported file', self::DOMAIN ); ?></th>
									<td>
										<input type="submit" class="button button-secondary" name="reset_data" value="<?php esc_html_e( 'Reset', self::DOMAIN ); ?>" />
										<br>
										<span class="description"><?php esc_html_e( 'Clear exported csv file.', self::DOMAIN ); ?></span>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
					<form action="<?php echo plugins_url(); ?>/sns-count-cache/includes/download.php" method="post">
						<?php wp_nonce_field( 'download', '_wpnonce' ); ?>
						<table class="form-table">
							<tbody>
								<tr>
									<th><?php esc_html_e( 'Download of exported file', self::DOMAIN ); ?></th>
									<td>
										<input type="submit" class="button button-secondary" name="download_data" value="<?php esc_html_e( 'Download', self::DOMAIN ); ?>" />
										<br>
										<span class="description"><?php esc_html_e( 'Download the exported csv file.', self::DOMAIN ); ?></span>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				<?php
					}
				?>
				</div>
			</div>
		</div>
	</div>
</div>
