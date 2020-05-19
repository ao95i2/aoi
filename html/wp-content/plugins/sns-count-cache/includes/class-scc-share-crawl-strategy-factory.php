<?php
/**
class-scc-share-crawl-strategy-factory.php

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

/**
 * SCC_Share_Crawl_Strategy_Factory
 */
class SCC_Share_Crawl_Strategy_Factory {

	/**
	 * Carete crawl strategy
	 *
	 * @param string $sns
	 * @return SCC_Crawl_Strategy
	 */
	public static function create_crawl_strategy( $sns ) {
		SCC_Logger::log( '[' . __METHOD__ . '] (line=' . __LINE__ . ')' );

		switch ( $sns ) {
			case SNS_Count_Cache::REF_SHARE_TWITTER:
				SCC_Logger::log( '[' . __METHOD__ . '] create crawl strategy: ' . $sns );
				return SCC_Share_Twitter_Crawl_Strategy::get_instance();
			case SNS_Count_Cache::REF_SHARE_FACEBOOK:
				SCC_Logger::log( '[' . __METHOD__ . '] create crawl strategy: ' . $sns );
				return SCC_Share_Facebook_Crawl_Strategy::get_instance();
			case SNS_Count_Cache::REF_SHARE_GPLUS:
				SCC_Logger::log( '[' . __METHOD__ . '] create crawl strategy: ' . $sns );
				return SCC_Share_Google_Crawl_Strategy::get_instance();
			case SNS_Count_Cache::REF_SHARE_POCKET:
				SCC_Logger::log( '[' . __METHOD__ . '] create crawl strategy: ' . $sns );
				return SCC_Share_Pocket_Crawl_Strategy::get_instance();
			case SNS_Count_Cache::REF_SHARE_HATEBU:
				SCC_Logger::log( '[' . __METHOD__ . '] create crawl strategy: ' . $sns );
				return SCC_Share_Hatebu_Crawl_Strategy::get_instance();
			case SNS_Count_Cache::REF_SHARE_PINTEREST:
				SCC_Logger::log( '[' . __METHOD__ . '] create crawl strategy: ' . $sns );
				return SCC_Share_Pinterest_Crawl_Strategy::get_instance();
			case SNS_Count_Cache::REF_SHARE_LINKEDIN:
				SCC_Logger::log( '[' . __METHOD__ . '] create crawl strategy: ' . $sns );
				return SCC_Share_Linkedin_Crawl_Strategy::get_instance();
		}
	}

}

?>
