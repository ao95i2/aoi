<?php
/**
 * Luxeritas WordPress Theme - free/libre wordpress platform
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * @copyright Copyright (C) 2015 Thought is free.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @author LunaNuko
 * @link https://thk.kanzae.net/
 * @translators rakeem( http://rakeem.jp/ )
 */

global $luxe, $_is, $awesome;
?>
<div id="nav">
<div id="gnavi">
<?php
// AMP 用グローバルナビ
if( isset( $luxe['amp'] ) ) {
?>
<label for="mnav" class="mobile-nav"><i class="<?php echo $awesome['fas']; ?>fa-bars"></i><?php echo __(' Menu', 'luxeritas'); ?></label>
<input type="checkbox" id="mnav" class="nav-on" />
<?php
}
// グローバルナビ本体
$trims = $luxe['html_compress'] === 'high' ? array( "\r", "\n", "\t" ) : array( "\t" );

echo str_replace( $trims, '',
	wp_nav_menu(
		array(
			'theme_location' => 'global-nav',
			'depth' => '3',
			'link_before' => '<span>',
			'link_after' => '</span>',
			'echo' => false,
			'items_wrap' => '<ul class="%2$s clearfix">%3$s</ul>',
		)
	)
);

if( isset( $luxe['amp'] ) ) {
?>
</div><!--/#gnavi-->
<div class="cboth"></div>
</div><!--/#nav-->
<?php
	return true;
}

if( $luxe['global_navi_mobile_type'] !== 'luxury' ) {
?>
<div class="mobile-nav mob-menu"><i class="<?php echo $awesome['fas']; ?>fa-bars"></i><?php echo __(' Menu', 'luxeritas'); ?></div>
<?php
}
else {
	// 豪華版モバイルメニュー用、前の記事と次の記事

	if(
		( $_is['single'] === true && isset( $luxe['next_prev_nav_visible'] ) ) ||
		( $_is['page'] === true && isset( $luxe['next_prev_nav_page_visible'] ) )
	) {
		$prv = get_adjacent_post( false, '', true );
		$nxt = get_adjacent_post( false, '', false );

		if( !empty( $prv ) ) {
?>
<div id="data-prev" data-prev="<?php echo get_permalink( $prv->ID ); ?>"></div>
<?php
		}
		if( !empty( $nxt ) ) {
?>
<div id="data-next" data-next="<?php echo get_permalink( $nxt->ID ); ?>"></div>
<?php
		}
	}
	elseif( $_is['home'] === true || $_is['archive'] === true || $_is['search'] === true ) {
		$prv = str_replace( array( ' ', '<a', 'href="', '">', '</a>' ), '', get_previous_posts_link( '' ) );
		$nxt = str_replace( array( ' ', '<a', 'href="', '">', '</a>' ), '', get_next_posts_link( '' ) );

		if( !empty( $prv ) && filter_var( $prv, FILTER_VALIDATE_URL ) !== false ) {
?>
<div id="data-prev" data-prev="<?php echo $prv; ?>"></div>
<?php
		}
		if( !empty( $nxt ) && filter_var( $nxt, FILTER_VALIDATE_URL ) !== false ) {
?>
<div id="data-next" data-next="<?php echo $nxt; ?>"></div>
<?php
		}
	}
?>
<ul class="mobile-nav">
<li class="mob-menu" title="<?php echo __( 'Menu', 'luxeritas' ) ?>"><i class="<?php echo $awesome['fas']; ?>fa-bars"></i><p><?php echo __( 'Menu', 'luxeritas' ) ?></p></li>
<?php
//if( $luxe['column_style'] !== '1column' && !isset( $luxe['hide_mobile_sidebar'] ) ) {
if( $luxe['column_style'] !== '1column' ) {
	$fa_exchange = $awesome['ver'][0] === '4' ? 'fa-exchange' : 'fa-exchange-alt';
?>
<li class="mob-side" title="<?php echo __( 'Sidebar', 'luxeritas' ) ?>"><i class="<?php echo $awesome['fas'], $fa_exchange; ?>"></i><p><?php echo __( 'Sidebar', 'luxeritas' ) ?></p></li>
<?php
}
?>
<li class="mob-prev" title="<?php echo __( ' Prev ', 'luxeritas' ) ?>"><i class="<?php echo $awesome['fas']; ?>fa-angle-double-left"></i><p><?php echo __( ' Prev ', 'luxeritas' ) ?></p></li>
<li class="mob-next" title="<?php echo __( ' Next ', 'luxeritas' ) ?>"><i class="<?php echo $awesome['fas']; ?>fa-angle-double-right"></i><p><?php echo __( ' Next ', 'luxeritas' ) ?></p></li>
<li class="mob-search" title="<?php echo __( 'Search', 'luxeritas' ) ?>"><i class="<?php echo $awesome['fas']; ?>fa-search"></i><p><?php echo __( 'Search', 'luxeritas' ) ?></p></li>
</ul>
<?php
}
?>
</div><!--/#gnavi-->
<div class="cboth"></div>
</div><!--/#nav-->
