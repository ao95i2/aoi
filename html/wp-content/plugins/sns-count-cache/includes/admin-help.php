<?php
/**
admin-help.php

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
?>

<div class="wrap">
	<h2><a href="admin.php?page=scc-help"><?php esc_html_e( 'SNS Count Cache', self::DOMAIN ); ?></a></h2>
	<div class="sns-cnt-cache">
		<h3 class="nav-tab-wrapper">
			<a class="nav-tab" href="admin.php?page=scc-dashboard"><?php esc_html_e( 'Dashboard', self::DOMAIN ); ?></a>
			<a class="nav-tab" href="admin.php?page=scc-cache-status"><?php esc_html_e( 'Cache Status', self::DOMAIN ); ?></a>
			<a class="nav-tab" href="admin.php?page=scc-share-count"><?php esc_html_e( 'Share Count', self::DOMAIN ); ?></a>
		<?php if ( self::OPT_SHARE_VARIATION_ANALYSIS_NONE !== $this->share_variation_analysis_mode ) { ?>
			<a class="nav-tab" href="admin.php?page=scc-hot-content"><?php esc_html_e( 'Hot Content', self::DOMAIN ); ?></a>
		<?php } ?>
			<a class="nav-tab" href="admin.php?page=scc-setting"><?php esc_html_e( 'Setting', self::DOMAIN ) ?></a>
			<a class="nav-tab nav-tab-active" href="admin.php?page=scc-help"><?php esc_html_e( 'Help', self::DOMAIN ); ?></a>
		</h3>
		<div class="metabox-holder">
			<div id="share-site-summary" class="postbox">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span><?php esc_html_e( 'Help', self::DOMAIN ); ?></span></h3>
				<div class="inside">
					<h4><?php esc_html_e( 'What is WordPress plugin SNS Cout Cache?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'WordPress plugin SNS Count Cache is a plugin which helps you to shorten page loading time when your site displays share and follower counts. This plugin gets share counts such as Twitter and Facebook, Pocket, Pinterest, Linkedin and caches these counts in the background. This plugin alse caches follower counts such as Twitter and Facebook, Feedly, Instagram, OneSignal, Push7 in the same way. The share and follower counts can be retrieved quickly not through network but through the cache using given functions.', self::DOMAIN ); ?></p>
					<h4><?php esc_html_e( 'How often does this plugin get and cache share counts?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'Although this plugin gets share count of 15 posts at a time every 15 minutes by default, you can modify the setting in the "Setting" tab in the administration page.', self::DOMAIN ); ?></p>
					<h4><?php esc_html_e( 'How can I know whether share count of each post is cached or not?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'Cache status is described in the "Cache Status" tab in the administration page.', self::DOMAIN ); ?></p>
					<h4><?php esc_html_e( 'How often does this plugin get and cache follower counts?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'Although this plugin gets follower count once a day by default, you can modify the setting in the "Setting" tab in the administration page.', self::DOMAIN ); ?></p>
					<h4><?php esc_html_e( 'What is cache status such as "full cache", "partial cache" and "no cache"?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'The following is the explanation of the cache status.', self::DOMAIN ); ?></p>
					<table class="view-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Cache Status', self::DOMAIN ); ?></th>
								<th><?php esc_html_e( 'Description', self::DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr><td><?php esc_html_e( 'full cache', self::DOMAIN ); ?></td><td><?php esc_html_e( 'Counts for configured all SNS are cached.', self::DOMAIN ); ?></td></tr>
							<tr><td><?php esc_html_e( 'partial cache', self::DOMAIN ); ?></td><td><?php esc_html_e( 'A subset of counts for configured SNS are cached.', self::DOMAIN ); ?></td></tr>
							<tr><td><?php esc_html_e( 'no cache', self::DOMAIN ); ?></td><td><?php esc_html_e( 'Count for configured SNS are not cached at all.', self::DOMAIN ); ?></td></tr>
						</tbody>
					</table>
					<h4><?php esc_html_e( 'How can I get share count from the cache?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'The share count is retrieved from the cache using the following functions in the WordPress loop such as query_posts(), get_posts() and WP_Query().', self::DOMAIN ); ?></p>
					<table class="view-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Function', self::DOMAIN ); ?></th>
								<th><?php esc_html_e( 'Description', self::DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr><td>scc_get_share_twitter()</td><td><?php esc_html_e( 'Twitter share count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_share_facebook()</td><td><?php esc_html_e( 'Facebook share count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_share_hatebu()</td><td><?php esc_html_e( 'Hatena Bookmark share count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_share_pocket()</td><td><?php esc_html_e( 'Pocket share count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_share_pinterest()</td><td><?php esc_html_e( 'Pinterest share count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_share_linkedin()</td><td><?php esc_html_e( 'Linkedin share count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_share_total()</td><td><?php esc_html_e( 'Total share count of selected SNS is returned from cache.', self::DOMAIN ); ?></td></tr>
						</tbody>
					</table>
					<h4><?php esc_html_e( 'How can I get follower count from the cache?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'The follower count is retrieved from the cache using the following functions.', self::DOMAIN ); ?></p>
					<table class="view-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Function', self::DOMAIN ); ?></th>
								<th><?php esc_html_e( 'Description', self::DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr><td>scc_get_follow_feedly()</td><td><?php esc_html_e( 'Feedly follower count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_follow_twitter()</td><td><?php esc_html_e( 'Twitter follower count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_follow_facebook()</td><td><?php esc_html_e( 'Facebook follower count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_follow_instagram()</td><td><?php esc_html_e( 'Instagram follower count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_follow_push7()</td><td><?php esc_html_e( 'Push7 follower count is returned from cache.', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_get_follow_onesignal()</td><td><?php esc_html_e( 'OneSignal follower count is returned from cache.', self::DOMAIN ); ?></td></tr>
						</tbody>
					</table>
					<h4><?php esc_html_e( 'Example Code', self::DOMAIN ); ?></h4>
					<?php esc_html_e( 'The code below describes a simple example which displays share count and follower count using the above functions.', self::DOMAIN ); ?>
<pre><code class="php">
&lt;?php
    $query_args = array(
        &#039;post_type&#039; =&gt; &#039;post&#039;,
        &#039;post_status&#039; =&gt; &#039;publish&#039;,
        &#039;posts_per_page&#039; =&gt; 5
    );

    $posts_query = new WP_Query( $query_args );

    if ( $posts_query-&gt;have_posts() ) {
        while ( $posts_query-&gt;have_posts() ){
            $posts_query-&gt;the_post();

            echo &#039;&lt;div&gt;Article Title: &#039; . esc_html( get_the_title( $posts_query-&gt;ID ) ) . &#039;&lt;/div&gt;&#039;;

            if ( function_exists( &#039;scc_get_share_twitter&#039; ) &amp;&amp;
                function_exists( &#039;scc_get_share_facebook&#039; ) &amp;&amp;
                function_exists( &#039;scc_get_share_pocket&#039; ) &amp;&amp;
                function_exists( &#039;scc_get_share_pinterest&#039; ) &amp;&amp;
                function_exists( &#039;scc_get_share_linkedin&#039; ) &amp;&amp;
                function_exists( &#039;scc_get_share_total&#039; )
            ) {
                // In WordPress loop, you can use the given function
                // in order to get share count for current post and follower count.
                echo &#039;&lt;div&gt;Twitter share count: &#039; . scc_get_share_twitter() . &#039;&lt;/div&gt;&#039;;
                echo &#039;&lt;div&gt;Facebook share count: &#039; . scc_get_share_facebook() . &#039;&lt;/div&gt;&#039;;
                echo &#039;&lt;div&gt;Pocket share count: &#039; . scc_get_share_pocket() . &#039;&lt;/div&gt;&#039;;
                echo &#039;&lt;div&gt;Pinterest share count: &#039; . scc_get_share_pinterest() . &#039;&lt;/div&gt;&#039;;
                echo &#039;&lt;div&gt;Linkedin share count: &#039; . scc_get_share_linkedin() . &#039;&lt;/div&gt;&#039;;
                echo &#039;&lt;div&gt;Total of share count: &#039; . scc_get_share_total() . &#039;&lt;/div&gt;&#039;;
            }
        }
        wp_reset_postdata();
    }

    if ( function_exists( &#039;scc_get_follow_feedly&#039; ) &amp;&amp;
        function_exists( &#039;scc_get_follow_twitter&#039; ) &amp;&amp;
        function_exists( &#039;scc_get_follow_facebook&#039; ) &amp;&amp;
        function_exists( &#039;scc_get_follow_instagram&#039; ) &amp;&amp;
        function_exists( &#039;scc_get_follow_push7&#039; ) &amp;&amp;
        function_exists( &#039;scc_get_follow_onesignal&#039; )
    ) {
        // You can use the given function in order to get follower count.
        echo &#039;&lt;div&gt;Feedly follower count: &#039; . scc_get_follow_feedly() . &#039;&lt;/div&gt;&#039;;
        echo &#039;&lt;div&gt;Twitter follower count: &#039; . scc_get_follow_twitter() . &#039;&lt;/div&gt;&#039;;
        echo &#039;&lt;div&gt;Facebook follower count: &#039; . scc_get_follow_facebook() . &#039;&lt;/div&gt;&#039;;
        echo &#039;&lt;div&gt;Instagram follower count: &#039; . scc_get_follow_instagram() . &#039;&lt;/div&gt;&#039;;
        echo &#039;&lt;div&gt;Push7 follower count: &#039; . scc_get_follow_push7() . &#039;&lt;/div&gt;&#039;;
        echo &#039;&lt;div&gt;OneSignal follower count: &#039; . scc_get_follow_onesignal() . &#039;&lt;/div&gt;&#039;;
    }
?&gt;
</code></pre>
					<script>hljs.initHighlightingOnLoad();</script>
					<h4><?php esc_html_e( 'How can I access specific custom field containing each share count?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'Custom fields including share count are accessed using the following meta keys.', self::DOMAIN ); ?></p>
					<table class="view-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Meta Key', self::DOMAIN ); ?></th>
								<th><?php esc_html_e( 'Description', self::DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr><td>scc_share_count_twitter</td><td><?php esc_html_e( 'A meta key to access Twitter share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_count_facebook</td><td><?php esc_html_e( 'A meta key to access Facebook share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_count_hatebu</td><td><?php esc_html_e( 'A meta key to access Hatena Bookmark share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_count_pocket</td><td><?php esc_html_e( 'A meta key to access Pocket share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_count_pinterest</td><td><?php esc_html_e( 'A meta key to access Pinterest share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_count_linkedin</td><td><?php esc_html_e( 'A meta key to access Linkedin share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_count_total</td><td><?php esc_html_e( 'A meta key to access total share count', self::DOMAIN ); ?></td></tr>
						</tbody>
					</table>
					<h4><?php esc_html_e( 'How can I access specific custom field containing each variation of share count?', self::DOMAIN ); ?></h4>
					<p><?php esc_html_e( 'Custom fields including variation of share count are accessed using the following meta keys.', self::DOMAIN ); ?></p>
					<table class="view-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Meta Key', self::DOMAIN ); ?></th>
								<th><?php esc_html_e( 'Description', self::DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr><td>scc_share_delta_twitter</td><td><?php esc_html_e( 'A meta key to access variation of Twitter share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_delta_facebook</td><td><?php esc_html_e( 'A meta key to access variation of Facebook share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_delta_hatebu</td><td><?php esc_html_e( 'A meta key to access variation of Hatena Bookmark share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_delta_pocket</td><td><?php esc_html_e( 'A meta key to access variation of Pocket share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_delta_pinterest</td><td><?php esc_html_e( 'A meta key to access variation of Pinterest share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_delta_linkedin</td><td><?php esc_html_e( 'A meta key to access variation of Linkedin share count', self::DOMAIN ); ?></td></tr>
							<tr><td>scc_share_delta_total</td><td><?php esc_html_e( 'A meta key to access variation of total share count', self::DOMAIN ); ?></td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
