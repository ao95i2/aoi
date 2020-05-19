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
 * @translators rakeem( http://rakeem.jp/ ) # en-Us
 * @translators boite de douze( https://boitededouze.com/ ) # fr_FR
 */

/*---------------------------------------------------------------------------
 * WordPress の例の定数を使うとチェックで怒られるので再定義
 *---------------------------------------------------------------------------*/
define( 'TPATH', get_template_directory() );
define( 'SPATH', get_stylesheet_directory() );
define( 'THEME', get_option( 'stylesheet' ) );

/*---------------------------------------------------------------------------
 * luxe Theme only works in PHP 5.6 or later.
 * luxe Theme only works in WordPress 4.4 or later.
 *---------------------------------------------------------------------------*/
if(
	version_compare( PHP_VERSION, '5.6', '<' ) === true ||
	version_compare( $GLOBALS['wp_version'], '4.4-alpha', '<' ) === true
) {
	require( TPATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'back-compat.php' );
	if( defined( 'WP_DEFAULT_THEME' ) !== false ) {
		switch_theme( WP_DEFAULT_THEME );
	}
	else {
		switch_theme( 'default' );
	}
}
else {
	require( TPATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'global-const.php' );
}

/*---------------------------------------------------------------------------
 * setup
 *---------------------------------------------------------------------------*/
//add_action( 'after_setup_theme', function() {
call_user_func( function() {
	global $luxe, $_is, $fchk;

	if( function_exists( 'wp_raise_memory_limit' ) === true ) {
		$wp_int = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
		$wp_max = wp_convert_hr_to_bytes( WP_MAX_MEMORY_LIMIT );
		if( $wp_max > $wp_int ) {
			wp_raise_memory_limit( 'admin' );
		}
		else {
			add_filter( 'wp_memory_limit', function() {
				return WP_MEMORY_LIMIT;
			});
			wp_raise_memory_limit( 'wp' );
		}
	}

	// textdomain
	if( $_is['admin'] === true && $_is['edit_posts'] === true ) {
		load_theme_textdomain( 'luxeritas', TPATH . DSEP . 'languages' . DSEP . 'admin' );
	}
	else {
		load_theme_textdomain( 'luxeritas', TPATH . DSEP . 'languages' . DSEP . 'site' );
	}

	require( INC . 'wpfunc.php' );
	require( INC . 'const.php' );
	require( INC . 'widget.php' );
	require( INC . 'stinger.php' );
	require( INC . 'sns-cache.php' );
	require( INC . 'thk-mod-class.php' );

	if( $_is['customize_preview'] === true ) {
		$fchk = true;
		require( INC . 'custom.php' );
		require( INC . 'custom-css.php' );
		require( INC . 'compress.php' );
		require( INC . 'admin-common-func.php' );
	}
	elseif( $_is['admin'] === true ) {
		$fchk = true;
		require( INC . 'admin-common-func.php' );
		if( $_is['edit_theme_options'] === true ) {
			require( INC . 'admin-func.php' );
		}
		if( $_is['edit_posts'] === true ) {
			if( stripos( $_SERVER['REQUEST_URI'], 'wp-admin/post' ) !== false ) {
				add_editor_style( 'editor-style.css' );
				require( INC . 'admin-post-func.php' );
			}
		}
		require( INC . 'admin-comments.php' );
	}

	if( isset( $luxe['amp_enable'] ) ) {
		if( $_is['admin'] === true && $_is['edit_theme_options'] === true ) {
			if( function_exists( 'thk_amp_mu_plugin_copy' ) === true ) thk_amp_mu_plugin_copy();
		}
		else {
			$rules = get_option( 'rewrite_rules' );
			if( !isset( $rules['^amp/?$'] ) ) {
				require( INC . 'rewrite-rules.php' );
				add_action( 'init', 'thk_add_endpoint', 11 );
			}
			unset( $rules );
		}
	}

	// jetpack og tags
	if( isset( $luxe['disable_jetpack_ogp'] ) ) {
		if( has_filter( 'wp_head', 'jetpack_og_tags' ) !== false ) {
			remove_action( 'wp_head', 'jetpack_og_tags', 10000 );
		}
		add_filter( 'jetpack_enable_open_graph', '__return_false', 99 );
	}
	// jetpack lazy image
	if( isset( $luxe['disable_jetpack_lazyload'] ) ) {
		if( class_exists( 'Jetpack_Lazy_Images' ) === true ) {
			add_action( 'wp_head', function() {
				$instance = Jetpack_Lazy_Images::instance();
				$instance->remove_filters();
			}, 10000 );
		}
		add_filter( 'lazyload_is_enabled', '__return_false', 99 );
	}
});

/*---------------------------------------------------------------------------
 * initialization
 *---------------------------------------------------------------------------*/
add_action( 'init', function() use( $luxe ) {
	global $_is; // "use" cannot be used. Need to be global.

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'caption', 'gallery', 'search-form' ) );
	register_nav_menus( array(
		'global-nav' => __( 'Header Nav (Global Nav)', 'luxeritas' ),
		'head-band' => __( 'Header Band Menu', 'luxeritas' )
	));

	// get sns count
	if( stripos( $_SERVER['REQUEST_URI'], 'wp-admin/admin-ajax.php' ) !== false ) {
		if( $_is['customize_preview'] === false ) {
			if( isset( $luxe['sns_tops_count'] ) || isset( $luxe['sns_bottoms_count'] ) ) {
				add_action( 'wp_ajax_thk_sns_real', 'thk_sns_real' );
				add_action( 'wp_ajax_nopriv_thk_sns_real', 'thk_sns_real' );
			}
		}
	}

	if( isset( $luxe['sns_count_cache_enable'] ) && $_is['customize_preview'] === false ) {
		if( isset( $_POST['url'] ) && isset( $_POST['action'] ) && $_POST['action'] === 'thk_sns_cache' ) {
			add_action( 'wp_ajax_thk_sns_cache', 'thk_sns_cache' );
			add_action( 'wp_ajax_nopriv_thk_sns_cache', 'thk_sns_cache' );
		}
	}

	// set amp endpoint
	if( $_is['admin'] === false && isset( $luxe['amp_enable'] ) ) {
		$q_amp = stripos( $_SERVER['QUERY_STRING'], 'amp=1' );
		if( $q_amp !== false ) {
			if( $q_amp > 0 ) {
				add_rewrite_endpoint( 'amp', EP_PERMALINK | EP_PAGES );
			}
		}
		else {
			add_rewrite_endpoint( 'amp', EP_PERMALINK | EP_PAGES );
		}

		add_filter( 'request', function( $vars ) {
			if( isset( $vars['amp'] ) && ( $vars['amp'] === '' ) ) {
				$vars['amp'] = 1;
			}
			return $vars;
		});
	}

	if( $_is['edit_posts'] === true && isset( $_GET['respond_frame'] ) ) {
		add_filter( 'show_' . 'admin_' . 'bar', '__return_false' );
		$_is['mobile'] = true;
	}
}, 10 );

/*---------------------------------------------------------------------------
 * parse request
 *---------------------------------------------------------------------------*/
add_action( 'parse_request', function( $q ) {
	global $luxe, $_is;

	if( $_is['admin'] === false && isset( $luxe['pwa_dynamic_files'] ) && ( isset( $luxe['pwa_enable'] ) || isset( $luxe['pwa_manifest'] ) ) ) {
		$protocol = $_is['ssl'] ? 'https://' : 'http://';
		$request  = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

		$manifest_file  = home_url('/') . 'luxe-manifest.json';
		$service_worker = home_url('/') . 'luxe-serviceworker.js';

		require( INC . 'create-pwd-files.php' );
		$create_pwd_files = new create_pwd_files();

		if( $request === $manifest_file ) {
			header( 'Content-Type: application/json' );
			echo $create_pwd_files->create_manifest();
			exit();
		}
		if( $request === $service_worker ) {
			header( 'Content-Type: text/javascript' );
			echo $create_pwd_files->create_service_worker();
			exit();
		}
	}
}, 10 );

/*---------------------------------------------------------------------------
 * pre get posts
 *---------------------------------------------------------------------------*/
add_action( 'pre_get_posts', function( $q ) {
	if( $q->is_admin === false && $q->is_main_query() === true ) {
		global $luxe, $_is;

		if( $q->is_search === true ) {
			if( isset( $luxe['items_search'] ) && isset( $luxe['items_search_num'] ) ) {
				$q->set( 'posts_per_page', $luxe['items_search_num'] );
			}
			get_template_part( 'inc/search-func' );
			thk_search_extend();
		}
		elseif( $q->is_home === true || $q->is_category === true || $q->is_archive === true ) {
			$posts_per_page = null;

			if( $q->is_home === true && isset( $luxe['items_home'] ) && isset( $luxe['items_home_num'] ) ) {
				$q->set( 'posts_per_page', $luxe['items_home_num'] );
				$posts_per_page = $luxe['items_home_num'];
			}
			elseif( $q->is_category === true && isset( $luxe['items_category'] ) && isset( $luxe['items_category_num'] ) ) {
				$q->set( 'posts_per_page', $luxe['items_category_num'] );
				$posts_per_page = $luxe['items_category_num'];
			}
			elseif( $q->is_archive === true && $q->is_category === false && isset( $luxe['items_archive'] ) && isset( $luxe['items_archive_num'] ) ) {
				$q->set( 'posts_per_page', $luxe['items_archive_num'] );
				$posts_per_page = $luxe['items_archive_num'];
			}

			if(
				( isset( $luxe['grid_home'] ) && $luxe['grid_home'] === 'none' ) ||
				( isset( $luxe['grid_archive'] ) && $luxe['grid_archive'] === 'none' ) ||
				( isset( $luxe['grid_category'] ) && $luxe['grid_category'] === 'none' )
			) {
				return;
			}

			// グリッドの通常表示部分は１ページに表示する件数に含めないようにする
			if( empty( $q->query['posts_per_page'] ) && empty( $q->query['offset'] ) ) {
				if( $_is['customize_preview'] === true ) {
					$luxe = wp_parse_args( get_option( 'theme_mods_' . THEME ), $luxe );
				}

				$grid_first = 0;

				if( $q->is_home === true && isset( $luxe['grid_home_first'] ) ) {
					$grid_first = $luxe['grid_home_first'];
				}
				elseif( $q->is_category === true && isset( $luxe['grid_category_first'] ) ) {
					$grid_first = $luxe['grid_category_first'];
				}
				elseif( $q->is_archive === true && isset( $luxe['grid_archive_first'] ) ) {
					$grid_first = $luxe['grid_archive_first'];
				}

				if( $grid_first <= 0 ) return;

				if( !isset( $posts_per_page ) ) $posts_per_page = get_option( 'posts_per_page' );
				$per = $posts_per_page + $grid_first;

				$paged = get_query_var( 'paged' ) ? (int)get_query_var( 'paged' ) : 1;
				if( $paged >= 2 ){
					$q->set( 'offset', $per + ( $paged - 2 ) * $posts_per_page );
					$q->set( 'posts_per_page', $posts_per_page );
				}
				else {
					$q->set( 'posts_per_page', $per );
				}
			}
		}
	}
});

/*---------------------------------------------------------------------------
 * pre comment on post
 *---------------------------------------------------------------------------*/
add_action( 'pre_comment_on_post', function() use( $luxe, $_is ) {
	if( isset( $luxe['captcha_enable'] ) && $_is['user_logged_in'] === false ) {
		if( $luxe['captcha_enable'] === 'recaptcha' || $luxe['captcha_enable'] === 'recaptcha-v3' ) {
			$msg1 = 'Google reCAPTCHA : ' . __( 'Connection to the authentication server failed. Please try again later.', 'luxeritas' );
			$msg2 = 'Google reCAPTCHA : ' . __( 'You have been identified as a bot and could not authenticate.', 'luxeritas' );

			if( isset( $_POST['g-recaptcha-response'] ) ) {
				$verify = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $luxe['recaptcha_secret_key'] . '&response=' . $_POST['g-recaptcha-response'];
				$json = (object)array( 'success' => false );

				$ret = thk_remote_request( $verify );

				if( $ret !== false && is_array( $ret ) === false ) {
					$json = @json_decode( $ret );
				}
				if( !isset( $json->success ) || ( isset( $json->success ) && $json->success !== true ) ) {
					wp_die( $msg1, '', array( 'response' => 418, 'back_link' => true ) );
				}
				if( $luxe['captcha_enable'] === 'recaptcha-v3' ) {
					$score = isset( $luxe['recaptcha_v3_score'] ) ? (float)$luxe['recaptcha_v3_score'] : 0.5;
					if( !isset( $json->score ) ) {
						wp_die( $msg1, '', array( 'response' => 418, 'back_link' => true ) );
					}
					elseif( isset( $json->score ) && $json->score <= $score ) {
						wp_die( $msg2, '', array( 'response' => 418, 'back_link' => true ) );
					}
				}
			}
			else {
				wp_die( $msg1, '', array( 'response' => 418, 'back_link' => true ) );
			}
		}
		elseif( $luxe['captcha_enable'] === 'securimage' ) {
			if( !isset( $_SESSION ) ) session_start();

			if( isset( $_POST['captcha_code'] ) && empty( $_POST['captcha_code'] ) ) {
				wp_die( __( 'Please enter image authentication.', 'luxeritas' ), '', array( 'response' => 418, 'back_link' => true ) );
		        }
			elseif( isset( $_POST['captcha_code'] ) &&
				isset( $_SESSION['securimage_code_disp']['default'] ) &&
				$_POST['captcha_code'] !== $_SESSION['securimage_code_disp']['default']
			) {
				wp_die( __( 'Image authentication is incorrect.', 'luxeritas' ), '', array( 'response' => 418, 'back_link' => true ) );
		        }
		}
	}
	return;
});

/*---------------------------------------------------------------------------
 * wp
 *---------------------------------------------------------------------------*/
add_action( 'wp', function() {
	global $luxe, $_is;

	require_once( INC . 'global-is-template.php' );

	if( $_is['admin'] === false ) thk_default_set();
	if( $_is['singular'] === true ) wp_enqueue_script( 'comment-reply' );

	if( isset( $luxe['amp'] ) ) {
		// AMP for front_page
		$url = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$uri = trim( str_replace( pdel( THK_HOME_URL ), '',  $url ), '/' );
		if( $uri === 'amp' ) {
			set_fake_root_endpoint_for_amp();
		}
	}
	else {
		if( $_is['customize_preview'] === false ) {
			if( ( $_is['singular'] === true || ( isset( $luxe['list_view'] ) && $luxe['list_view'] === 'content' ) || ( isset( $luxe['sticky_no_excerpt'] ) && $luxe['sticky_no_excerpt'] && is_sticky() === true ) ) && isset( $luxe['lazyload_contents'] ) ) {
				add_filter( 'thk_content', 'thk_intersection_observer_replace_all', 99 );
			}
			if( isset( $luxe['lazyload_sidebar'] ) ) {
				add_filter( 'thk_sidebar', 'thk_intersection_observer_replace_all', 99 );
			}
			if( isset( $luxe['lazyload_footer'] ) ) {
				add_filter( 'thk_footer', 'thk_intersection_observer_replace_all', 99 );
			}
			if( isset( $luxe['lazyload_thumbs'] ) ) {
				add_filter( 'post_thumbnail_html', 'thk_intersection_observer_replace', 99 );
			}
			if( isset( $luxe['lazyload_avatar'] ) ) {
				add_filter( 'get_avatar', 'thk_intersection_observer_replace', 99 );
			}
		}
	}
});

/*---------------------------------------------------------------------------
 * template redirect
 *---------------------------------------------------------------------------*/
add_action( 'template_redirect', function() {
	global $luxe, $_is;

	if( $_is['feed'] === true ) {
		require( INC . 'feed.php' );
		return;
	}

	if( isset( $luxe['amp'] ) ) {
		$page_on_front = wp_cache_get( 'page_on_front', 'luxe' );
		if( $page_on_front !== false ) {
			remove_fake_root_endpoint_for_amp( $page_on_front );
		}
	}

	if( $_is['singular'] === true ) require( INC . 'add-shortcode.php' );

	if(
		$_is['singular']	=== true ||
		$_is['home']		=== true ||
		$_is['archive']		=== true ||
		$_is['search']		=== true ||
		$_is['404']		=== true
	) {
		require( INC . 'filters.php' );
		require( INC . 'load-styles.php' );
		require( INC . 'description.php' );
		require( INC . 'load-header.php' );
		if( isset( $luxe['blogcard_enable'] ) && $_is['singular'] === true ) {
			require( INC . 'blogcard-func.php' );
		}
		if( isset( $luxe['amp'] ) ) {
			require( INC . 'amp-extensions.php' );
		}
	}
}, 99 );
