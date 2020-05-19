<?php
/**
admin-dashboard-widget.php

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

$query_args = array(
	'post_type' => $this->share_base_cache_post_types,
	'post_status' => 'publish',
	'nopaging' => true,
	'update_post_term_cache' => false,
	'update_post_meta_cache' => false,
	);

$site_query = new WP_Query( $query_args );

?>
<div class="sns-cnt-cache">
	<div id="scc-dashboard-widget">
		<div id="site-summary-share-cache" class="site-summary">
			<h3><span><?php esc_html_e( 'Share', self::DOMAIN ); ?></span></h3>
			<h4><a href="admin.php?page=scc-cache-status"><?php esc_html_e( 'Cache Status', self::DOMAIN ); ?></a></h4>
			<table class="view-table">
				<thead>
					<tr class="thead">
						<th class="dummy"></th>
						<th><?php esc_html_e( 'Cache Type', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'Cache Progress', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'Total Content', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'State - Full Cache', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'State - Partial Cache', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'State - No Cache', self::DOMAIN ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="dummy"></td>
						<td><?php esc_html_e( 'Primary Cache', self::DOMAIN ); ?></td>
						<td data-label="<?php echo esc_attr( __( 'Cache Progress', self::DOMAIN ) ); ?>">
							<img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc="spcs"></span>
						</td>
						<td class="share-count" data-label="<?php echo esc_attr( __( 'Total Content', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='spc'></span></td>
						<td class="share-count full-cache" data-label="<?php echo esc_attr( __( 'State - Full Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='spfcc'></span></td>
						<td class="share-count partial-cache" data-label="<?php echo esc_attr( __( 'State - Partial Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='sppcc'></span></td>
						<td class="share-count no-cache" data-label="<?php echo esc_attr( __( 'State - No Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='spncc'></span></td>
					</tr>
					<tr>
						<td class="dummy"></td>
						<td><?php esc_html_e( 'Secondary Cache', self::DOMAIN ); ?></td>
						<td data-label="<?php echo esc_attr( __( 'Cache Progress', self::DOMAIN ) ); ?>">
							<img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc="sscs"></span>
						</td>
						<td class="share-count" data-label="<?php echo esc_attr( __( 'Total Content', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='spc'></span></td>
						<td class="share-count full-cache" data-label="<?php echo esc_attr( __( 'State - Full Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='ssfcc'></span></td>
						<td class="share-count partial-cache" data-label="<?php echo esc_attr( __( 'State - Partial Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='sspcc'></span></td>
						<td class="share-count no-cache" data-label="<?php echo esc_attr( __( 'State - No Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='ssncc'></span></td>
					</tr>
				</tbody>
			</table>
			<h4><a href="admin.php?page=scc-share-count"><?php esc_html_e( 'Share Count', self::DOMAIN ); ?></a></h4>
			<table class="view-table">
				<thead>
					<tr class="thead">
						<th class="dummy"></th>
						<th class="dummy"><?php echo esc_attr( __( 'SNS', self::DOMAIN ) ); ?></th>
						<?php
						$share_base_cache_target = $this->share_base_cache_target ;
						unset( $share_base_cache_target[ self::REF_CRAWL_DATE ] );

						foreach ( $share_base_cache_target as $sns => $active ) {
							if ( $active ) {
								$sns_name = $this->get_sns_name( $sns );

								echo '<th>' . esc_html( $sns_name ) . '</th>';
							}
						}
						?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="dummy"></td>
						<td class="dummy"><?php echo esc_attr( __( 'Share Count', self::DOMAIN ) ); ?></td>
						<?php
						foreach ( $share_base_cache_target as $sns => $active ) {
							if ( $active ) {
								$sns_name = $this->get_sns_name( $sns );

								if ( self::REF_SHARE_GPLUS === $sns ) {
									echo '<td class="share-count" data-label="' . esc_attr( $sns_name ) . '">';
									echo '<img class="loading" src="' . esc_url( $this->loading_img_url ) . '" /><span data-scc="sgplus"></span>';
									echo '</td>';
								} else {
									echo '<td class="share-count" data-label="' . esc_attr( $sns_name ) . '">';
									echo '<img class="loading" src="' . esc_url( $this->loading_img_url ) . '" /><span data-scc="s' . esc_attr( strtolower( $sns ) ) . '"></span>';
									echo '</td>';
								}
							}
						}
						?>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="site-summary-follow-cache" class="site-summary">
			<h3><span><?php esc_html_e( 'Follow', self::DOMAIN ); ?></span></h3>
			<h4><?php esc_html_e( 'Cache Status', self::DOMAIN ); ?></h4>
				<table class="view-table">
				<thead>
					<tr class="thead">
						<th class="dummy"></th>
						<th><?php esc_html_e( 'Cache Type', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'Cache Progress', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'Total Content', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'State - Full Cache', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'State - Partial Cache', self::DOMAIN ); ?></th>
						<th><?php esc_html_e( 'State - No Cache', self::DOMAIN ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="dummy"></td>
						<td><?php esc_html_e( 'Primary Cache', self::DOMAIN ); ?></td>
						<td data-label="<?php echo esc_attr( __( 'Cache Progress', self::DOMAIN ) ); ?>">
							<img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc="fpcs"></span>
						</td>
						<td class="share-count" data-label="<?php echo esc_attr( __( 'Total Content', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fpc'></span></td>
						<td class="share-count full-cache" data-label="<?php echo esc_attr( __( 'State - Full Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fpfcc'></span></td>
						<td class="share-count partial-cache" data-label="<?php echo esc_attr( __( 'State - Partial Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fppcc'></span></td>
						<td class="share-count no-cache" data-label="<?php echo esc_attr( __( 'State - No Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fpncc'></span></td>
					</tr>
					<tr>
						<td class="dummy"></td>
						<td><?php esc_html_e( 'Secondary Cache', self::DOMAIN ); ?></td>
						<td data-label="<?php echo esc_attr( __( 'Cache Progress', self::DOMAIN ) ); ?>">
							<img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc="fscs"></span>
						</td>
						<td class="share-count" data-label="<?php echo esc_attr( __( 'Total Content', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fpc'></span></td>
						<td class="share-count full-cache" data-label="<?php echo esc_attr( __( 'State - Full Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fsfcc'></span></td>
						<td class="share-count partial-cache" data-label="<?php echo esc_attr( __( 'State - Partial Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fspcc'></span></td>
						<td class="share-count no-cache" data-label="<?php echo esc_attr( __( 'State - No Cache', self::DOMAIN ) ); ?>"><img class="loading" src="<?php echo esc_url( $this->loading_img_url ); ?>" /><span data-scc='fsncc'></span></td>
					</tr>
				</tbody>
			</table>
			<h4><?php esc_html_e( 'Follower Count', self::DOMAIN ); ?></h4>
			<table class="view-table">
				<thead>
					<tr class="thead">
						<th class="dummy"></th>
						<th class="dummy"><?php echo esc_attr( __( 'SNS', self::DOMAIN ) ); ?></th>
						<?php
						$follow_base_cache_target = $this->follow_base_cache_target ;
						unset( $follow_base_cache_target[ self::REF_CRAWL_DATE ] );

						foreach ( $follow_base_cache_target as $sns => $active ) {
							if ( $active ) {
								$sns_name = $this->get_sns_name( $sns );

								echo '<th>' . esc_html( $sns_name ) . '</th>';
							}
						}
						?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="dummy"></td>
						<td class="dummy"><?php echo esc_attr( __( 'Follower Count', self::DOMAIN ) ); ?></td>
						<?php
						foreach ( $follow_base_cache_target as $sns => $active ) {
							if ( $active ) {
								$sns_name = $this->get_sns_name( $sns );

								echo '<td class="share-count" data-label="' . esc_attr( $sns_name ) . '">';
								echo '<img class="loading" src="' . esc_url( $this->loading_img_url ) . '" /><span data-scc="f' . esc_attr( strtolower( $sns ) ) . '"></span>';
								echo '</td>';
							}
						}
						?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
