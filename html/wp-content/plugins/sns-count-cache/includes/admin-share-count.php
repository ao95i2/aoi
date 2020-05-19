<?php
/**
admin-share-count.php

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

$posts_per_page = 50;

$sort_mode = false;
$sort_exec_key = '';

if ( isset( $_GET['action'] ) && 'sort' === wp_unslash( $_GET['action'] ) ) {
	if ( current_user_can( self::OPT_COMMON_CAPABILITY ) ) {
		if ( isset( $_GET['key'] ) ) {
			$sort_mode = true;
			$sns = wp_unslash( $_GET['key'] );

			if ( 'Google' === $sns ) {
				$sns = $sns . '+';
			}

			$sort_exec_key = $sns;

			$meta_key = $this->cache_engines[ self::REF_SHARE_2ND ]->get_cache_key( $sns );
		}
	}
}

$paged = 1;

if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) && 0 < wp_unslash( $_GET['paged'] ) ) {
	$paged = (int) wp_unslash( $_GET['paged'] );
} else {
	$paged = 1;
}

$share_base_cache_target = $this->share_base_cache_target ;
unset( $share_base_cache_target[ self::REF_CRAWL_DATE ] );

?>
<div class="wrap">
	<h2><a href="admin.php?page=scc-share-count"><?php esc_html_e( 'SNS Count Cache', self::DOMAIN ); ?></a></h2>
	<div class="sns-cnt-cache">
		<h3 class="nav-tab-wrapper">
			<a class="nav-tab" href="admin.php?page=scc-dashboard"><?php esc_html_e( 'Dashboard', self::DOMAIN ); ?></a>
			<a class="nav-tab" href="admin.php?page=scc-cache-status"><?php esc_html_e( 'Cache Status', self::DOMAIN ); ?></a>
			<a class="nav-tab nav-tab-active" href="admin.php?page=scc-share-count"><?php esc_html_e( 'Share Count', self::DOMAIN ); ?></a>
		<?php if ( self::OPT_SHARE_VARIATION_ANALYSIS_NONE !== $this->share_variation_analysis_mode ) { ?>
			<a class="nav-tab" href="admin.php?page=scc-hot-content"><?php esc_html_e( 'Hot Content', self::DOMAIN ); ?></a>
		<?php } ?>
			<a class="nav-tab" href="admin.php?page=scc-setting"><?php esc_html_e( 'Setting', self::DOMAIN ); ?></a>
			<a class="nav-tab" href="admin.php?page=scc-help"><?php esc_html_e( 'Help', self::DOMAIN ); ?></a>
		</h3>
		<div class="metabox-holder">
			<div id="share-each-content" class="postbox">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span><?php esc_html_e( 'Share Count', self::DOMAIN ); ?></span></h3>
				<div class="inside">
					<?php
					if ( $sort_mode ) {
						$query_args = array(
							'post_type' => $this->share_base_cache_post_types,
							'post_status' => 'publish',
							'posts_per_page' => $posts_per_page,
							'paged' => $paged,
							'meta_key' => $meta_key,
							'orderby'  => 'meta_value_num',
							'update_post_term_cache' => false,
							'order' => 'DESC',
						);
					} else {
						$query_args = array(
							'post_type' => $this->share_base_cache_post_types,
							'post_status' => 'publish',
							'posts_per_page' => $posts_per_page,
							'paged' => $paged,
							'update_post_term_cache' => false,
						);
					}

					$posts_query = new WP_Query( $query_args );

					$this->pagination( $posts_query->max_num_pages, '', $paged, true );
					?>
					<table class="tfloat view-table">
						<thead>
							<tr class="thead">
								<th>No.</th>
								<th><?php esc_html_e( 'Content', self::DOMAIN ); ?></th>
									<?php
									foreach ( $share_base_cache_target as $sns => $active ) {
										if ( $active ) {
											$sort_key = $sns;

											if ( self::REF_SHARE_GPLUS === $sort_key ) {
												$sort_key = str_replace( '+', '', $sort_key );
											}

											$sort_url = 'admin.php?page=scc-share-count&action=sort&key=' . $sort_key;

											$sns_name = $this->get_sns_name( $sns );

											if ( $sns === $sort_exec_key ) {
												echo '<th style="white-space:nowrap"><a class="sort-exec-key" href="' . esc_url( $sort_url ) . '">' . esc_html( $sns_name ) . '</th>';
											} else {
												echo '<th style="white-space:nowrap"><a href="' . esc_url( $sort_url ) . '">' . esc_html( $sns_name ) . '</th>';
											}
										} // End if().
									} // End foreach().
									?>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = ( $paged - 1 ) * $posts_per_page + 1;

							if ( 1 === $paged ) {
							?>
							<tr class="home">
								<td><?php echo '-'; ?></td>
								<td><a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="title"><?php echo esc_html( bloginfo( 'name' ) ); ?></a></td>
									<?php
									if ( self::OPT_SHARE_VARIATION_ANALYSIS_NONE !== $this->share_variation_analysis_mode ) {
										$transient_id = $this->cache_engines[ self::REF_SHARE_BASE ]->get_cache_key( 'home' );

										$sns_counts = get_transient( $transient_id );

										if ( false !== $sns_counts ) {
											$option_key = $this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->get_delta_key( 'home' );

											$sns_deltas = get_option( $option_key );

											if ( false !== $sns_deltas ) {
												foreach ( $share_base_cache_target as $sns => $active ) {
													if ( $active ) {
														$sns_name = $this->get_sns_name( $sns );

														if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
															echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
															echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );

															if ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 < $sns_deltas[ $sns ] ) {
																echo ' (<span class="delta-rise">+' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
															} elseif ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 > $sns_deltas[ $sns ] ) {
																echo ' (<span class="delta-fall">' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
															}

															echo '</td>';
														} else {
															echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
															esc_html_e( 'N/A', self::DOMAIN );
															echo '</td>';
														}
													}
												}
											} else {
												foreach ( $share_base_cache_target as $sns => $active ) {
													if ( $active ) {
														$sns_name = $this->get_sns_name( $sns );

														if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
															echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
															echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );
															echo '</td>';
														} else {
															echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
															esc_html_e( 'N/A', self::DOMAIN );
															echo '</td>';
														}
													}
												}
											} // End if().
										} else {
											$option_key = $this->cache_engines[ self::REF_SHARE_2ND ]->get_cache_key( 'home' );

											$sns_counts = get_option( $option_key );

											if ( false !== $sns_counts ) {
												$option_key = $this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->get_delta_key( 'home' );

												$sns_deltas = get_option( $option_key );

												if ( false !== $sns_deltas ) {
													foreach ( $share_base_cache_target as $sns => $active ) {
														if ( $active ) {
															$sns_name = $this->get_sns_name( $sns );

															if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
																echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
																echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );

																if ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 < $sns_deltas[ $sns ] ) {
																	echo ' (<span class="delta-rise">+' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
																} elseif ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 < $sns_deltas[ $sns ] ) {
																	echo ' (<span class="delta-fall">' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
																}

																echo '</td>';
															} else {
																echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
																esc_html_e( 'N/A', self::DOMAIN );
																echo '</td>';
															}
														}
													}
												} else {
													foreach ( $share_base_cache_target as $sns => $active ) {
														if ( $active ) {
															$sns_name = $this->get_sns_name( $sns );

															if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
																echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
																echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );
																echo '</td>';
															} else {
																echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
																esc_html_e( 'N/A', self::DOMAIN );
																echo '</td>';
															}
														}
													}
												} // End if().
											} else {
												foreach ( $share_base_cache_target as $sns => $active ) {
													if ( $active ) {
														$sns_name = $this->get_sns_name( $sns );

														echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
														esc_html_e( 'N/A', self::DOMAIN );
														echo '</td>';
													}
												}
											} // End if().
										} // End if().
									} else {
										$transient_id = $this->cache_engines[ self::REF_SHARE_BASE ]->get_cache_key( 'home' );

										$sns_counts = get_transient( $transient_id );

										if ( false !== $sns_counts ) {
											$option_key = $this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->get_delta_key( 'home' );

											$sns_deltas = get_option( $option_key );

											if ( false !== $sns_deltas ) {
												foreach ( $share_base_cache_target as $sns => $active ) {
													if ( $active ) {
														$sns_name = $this->get_sns_name( $sns );

														if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
															echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
															echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );

															if ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 < $sns_deltas[ $sns ] ) {
																echo ' (<span class="delta-rise">+' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
															} elseif ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 > $sns_deltas[ $sns ] ) {
																echo ' (<span class="delta-fall">' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
															}

															echo '</td>';
														} else {
															echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
															esc_html_e( 'N/A', self::DOMAIN );
															echo '</td>';
														}
													}
												}
											} else {
												foreach ( $share_base_cache_target as $sns => $active ) {
													if ( $active ) {
														$sns_name = $this->get_sns_name( $sns );

														if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {

															echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
															echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );
															echo '</td>';
														} else {
															echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
															esc_html_e( 'N/A', self::DOMAIN );
															echo '</td>';
														}
													}
												}
											} // End if().
										} else {
											$option_key = $this->cache_engines[ self::REF_SHARE_2ND ]->get_cache_key( 'home' );

											$sns_counts = get_option( $option_key );

											if ( false !== $sns_counts ) {
												$option_key = $this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->get_delta_key( 'home' );

												$sns_deltas = get_option( $option_key );

												if ( false !== $sns_deltas ) {
													foreach ( $share_base_cache_target as $sns => $active ) {
														if ( $active ) {
															$sns_name = $this->get_sns_name( $sns );

															if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
																echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
																echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );

																if ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 < $sns_deltas[ $sns ] ) {
																	echo ' (<span class="delta-rise">+' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
																} elseif ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 > $sns_deltas[ $sns ] ) {
																	echo ' (<span class="delta-fall">' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
																}

																echo '</td>';
															} else {
																echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
																esc_html_e( 'N/A', self::DOMAIN );
																echo '</td>';
															}
														}
													}
												} else {
													foreach ( $share_base_cache_target as $sns => $active ) {
														if ( $active ) {
															$sns_name = $this->get_sns_name( $sns );

															if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {
																echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';
																echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );
																echo '</td>';
															} else {
																echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
																esc_html_e( 'N/A', self::DOMAIN );
																echo '</td>';
															}
														}
													}
												} // End if().
											} else {
												foreach ( $share_base_cache_target as $sns => $active ) {
													if ( $active ) {
														$sns_name = $this->get_sns_name( $sns );

														echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
														esc_html_e( 'N/A', self::DOMAIN );
														echo '</td>';
													}
												}
											} // End if().
										} // End if().
									} // End if().
									?>
							</tr>
							<?php
							} // End if().
							if ( $posts_query->have_posts() ) {
								while ( $posts_query->have_posts() ) {
									$posts_query->the_post();
							?>
							<tr>
								<td><?php echo esc_html( $count ); ?></td>
								<td><a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" target="_blank" class="title"><?php echo esc_html( get_the_title( get_the_ID() ) ); ?></a></td>
									<?php
									$transient_id = $this->cache_engines[ self::REF_SHARE_BASE ]->get_cache_key( get_the_ID() );

									$sns_counts = get_transient( $transient_id );

									if ( ! $sort_mode && false !== $sns_counts ) {
										foreach ( $share_base_cache_target as $sns => $active ) {
											if ( $active ) {
												$sns_name = $this->get_sns_name( $sns );

												if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {

													echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';

													echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );

													if ( self::OPT_SHARE_VARIATION_ANALYSIS_NONE !== $this->share_variation_analysis_mode ) {
														// delta
														$meta_key = $this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->get_delta_key( $sns );
														$sns_deltas[ $sns ] = get_post_meta( get_the_ID(), $meta_key, true );

														if ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 < $sns_deltas[ $sns ] ) {
															echo ' (<span class="delta-rise">+' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
														} elseif ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 > $sns_deltas[ $sns ] ) {
															echo ' (<span class="delta-fall">' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
														}
													}

													echo '</td>';
												} else {
													echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
													esc_html_e( 'N/A', self::DOMAIN );
													echo '</td>';
												}
											}
										}
									} else {
										foreach ( $share_base_cache_target as $sns => $active ) {
											if ( $active ) {
												$sns_name = $this->get_sns_name( $sns );

												$meta_key = $this->cache_engines[ self::REF_SHARE_2ND ]->get_cache_key( $sns );

												$sns_counts[ $sns ] = get_post_meta( get_the_ID(), $meta_key, true );

												if ( SCC_Cache::has_value( $sns_counts[ $sns ] ) && 0 <= $sns_counts[ $sns ] ) {

													echo '<td class="share-count" style="white-space:nowrap" data-label="' . esc_attr( $sns_name ) . '">';

													echo esc_html( number_format( (int) $sns_counts[ $sns ] ) );

													if ( self::OPT_SHARE_VARIATION_ANALYSIS_NONE !== $this->share_variation_analysis_mode ) {
														//delta
														$meta_key = $this->analytical_engines[ self::REF_SHARE_ANALYSIS ]->get_delta_key( $sns );
														$sns_deltas[ $sns ] = get_post_meta( get_the_ID(), $meta_key, true );

														if ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 < $sns_deltas[ $sns ] ) {
															echo ' (<span class="delta-rise">+' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
														} elseif ( SCC_Cache::has_value( $sns_deltas[ $sns ] ) && 0 > $sns_deltas[ $sns ] ) {
															echo ' (<span class="delta-fall">' . esc_html( number_format( (int) $sns_deltas[ $sns ] ) ) . '</span>)';
														}
													}

													echo '</td>';
												} else {
													echo '<td class="not-cached share-count" data-label="' . esc_attr( $sns_name ) . '">';
													esc_html_e( 'N/A', self::DOMAIN );
													echo '</td>';
												}
											}
										}
									} // End if().
								?>
							</tr>
							<?php
									++$count;

								} // End while().
							} // End if().
						?>
						</tbody>
					</table>
					<?php
						$this->pagination( $posts_query->max_num_pages, '', $paged, true );
						wp_reset_postdata();
					?>
				</div>
			</div>
		</div>
	</div>
</div>
