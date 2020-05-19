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

/*---------------------------------------------------------------------------
 * constant
 *---------------------------------------------------------------------------*/
const DSEP = DIRECTORY_SEPARATOR;
const INC = TPATH . DSEP . 'inc' . DSEP;

/*---------------------------------------------------------------------------
 * global
 *---------------------------------------------------------------------------*/
//$luxe = apply_filters( 'default_option_' . THEME, '', THEME, 1 );
$luxe = get_option( 'theme_mods_' . THEME );
$fchk = false;
$default_set = false;
$awesome = [];

$_is['ssl']			= is_ssl();
$_is['mobile']			= wp_is_mobile();
$_is['admin']			= is_admin();
$_is['customize_preview']	= is_customize_preview();
$_is['user_logged_in']		= is_user_logged_in();
if( $_is['user_logged_in'] === true ) {
	$_is['edit_posts']		= current_user_can( 'edit_posts' );
	$_is['edit_published_posts']	= current_user_can( 'edit_published_posts' );
	$_is['edit_theme_options']	= current_user_can( 'edit_theme_options' );
}
else {
	$_is['edit_posts']		= false;
	$_is['edit_published_posts']	= false;
	$_is['edit_theme_options']	= false;
}

$_is['preview']		= false;
$_is['front_page']	= false;
$_is['home']		= false;
$_is['singular']	= false;
$_is['single']		= false;
$_is['page']		= false;
$_is['archive']		= false;
$_is['category']	= false;
$_is['tag']		= false;
$_is['search']		= false;
$_is['attachment']	= false;
$_is['comments_open']	= false;
$_is['feed']		= false;
$_is['404']		= false;

$_is['tax']		= false;
$_is['day']		= false;
$_is['month']		= false;
$_is['year']		= false;
$_is['author']		= false;
$_is['search']		= false;
$_is['post_type_archive'] = false;

if( isset( $luxe['awesome_version'] ) && (int)$luxe['awesome_version'] === 4 ) {
	$awesome['ver'] = '4.7.0';
	$awesome['cdn'] = 'maxcdn.bootstrapcdn.com';
	$awesome['uri'] = 'https://' . $awesome['cdn'] . '/font-awesome/' . $awesome['ver'] . '/';
	$awesome['css'] = 'css/font-awesome.min.css';
	$awesome['fas'] = 'fa ';
	$awesome['far'] = 'fa ';
	$awesome['fab'] = 'fa ';
}
else {
	$awesome['ver'] = '5.12.1';
	$awesome['cdn'] = 'use.fontawesome.com';
	$awesome['uri'] = 'https://' . $awesome['cdn'] . '/releases/v' . $awesome['ver'] . '/';
	$awesome['css'] = 'css/all.css';
	$awesome['fas'] = 'fas ';
	$awesome['far'] = 'far ';
	$awesome['fab'] = 'fab ';
}
