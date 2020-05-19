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

class thk_files extends thk_optimize {
	private $_js_dir	= null;
	private $_css_dir	= null;
	private $_tmpl_dir	= null;

	public function __construct() {
		$this->_js_dir = TPATH . DSEP . 'js' . DSEP;
		$this->_css_dir = TPATH . DSEP . 'css' . DSEP;
		$this->_tmpl_dir = TPATH . DSEP . 'styles' . DSEP;
	}

	//protected function styles() {
	public function styles() {
		global $awesome;

		$ret = array(
			'luxe-mode'		=> $this->_css_dir . 'luxe-mode.css',
			'bootstrap'		=> $this->_css_dir . 'bootstrap3/bootstrap.min.css',
			'bootstrap4'		=> $this->_css_dir . 'bootstrap4/bootstrap.min.css',
			'bootstrap-clear'	=> $this->_css_dir . 'bootstrap-clear.css',
			'awesome'		=> $this->_css_dir . 'fontawesome5.css',
			//'awesome-minimum'	=> $this->_css_dir . 'fontawesome5.luxe.minimum.css',
			'icomoon'		=> $this->_css_dir . 'icomoon.css',

			'style_thk'	=> TPATH . DSEP . 'style.css',
			'grid'		=> $this->_tmpl_dir . 'grid.css',

			'sns'		=> $this->_tmpl_dir . 'sns.css',
			'sns-flat'	=> $this->_tmpl_dir . 'sns-flat.css',
			'sns-icon'	=> $this->_tmpl_dir . 'sns-icon.css',
			'sns-normal'	=> $this->_tmpl_dir . 'sns-normal.css',

			'toc'		=> $this->_tmpl_dir . 'toc.css',
			'blogcard'	=> $this->_tmpl_dir . 'blogcard.css',
			'search'	=> $this->_tmpl_dir . 'widget-search.css',
			'archive'	=> $this->_tmpl_dir . 'widget-archive.css',
			'calendar'	=> $this->_tmpl_dir . 'widget-calendar.css',
			'tagcloud'	=> $this->_tmpl_dir . 'widget-tagcloud.css',
			'new-post'	=> $this->_tmpl_dir . 'widget-new-post.css',
			'rcomments'	=> $this->_tmpl_dir . 'widget-rcomments.css',
			'adsense'	=> $this->_tmpl_dir . 'widget-adsense.css',
			'follow-button'	=> $this->_tmpl_dir . 'widget-follow-button.css',
			'rss-feedly'	=> $this->_tmpl_dir . 'widget-rss-feedly.css',
			'qr-code'	=> $this->_tmpl_dir . 'widget-qr-code.css',

			'head-search'	=> $this->_tmpl_dir . 'head-search.css',
			'mobile-common'	=> $this->_tmpl_dir . 'mobile-common.css',
			//'mobile-menu'	=> $this->_tmpl_dir . 'mobile-menu.css',
			'mobile-luxury'	=> $this->_tmpl_dir . 'mobile-luxury.css',

			'balloon'	=> $this->_tmpl_dir . 'balloon.css',
		);

		if( $awesome['ver'][0] === '4' ) {
			$ret['awesome'] = str_replace( 5, 4, $ret['awesome'] );
		}

		return $ret;
	}

	protected function styles_async() {
		global $awesome;

		$ret = array(
			'awesome'		=> $this->_css_dir . 'fontawesome5.css',
			'awesome-minimum'	=> $this->_css_dir . 'fontawesome5.luxe.minimum.css',
			'icomoon'		=> $this->_css_dir . 'icomoon.css',
			'block-library-style'	=> ABSPATH . WPINC . '/css/dist/block-library/style.min.css',
			'block-library-theme'	=> ABSPATH . WPINC . '/css/dist/block-library/theme.min.css',
			'spotlight'		=> $this->_css_dir . 'spotlight.css',
			'strip'			=> $this->_css_dir . 'strip.css',
			'tosrus'		=> $this->_css_dir . 'jquery.tosrus.all.css',
			'lightcase'		=> $this->_css_dir . 'lightcase.min.css',
			'highslide'		=> $this->_css_dir . 'highslide.css',
			'floatbox'		=> $this->_js_dir . 'floatbox/floatbox.css',
			'fluidbox'		=> $this->_css_dir . 'fluidbox.min.css',
			'autocomplete'		=> $this->_css_dir . 'autocomplete.css',
			'print'			=> $this->_tmpl_dir . 'print.css',
		);

		// WP 5.0 未満
		if( version_compare( $GLOBALS['wp_version'], '5.0', '<' ) === true ) {
			$block_library_style = WP_PLUGIN_DIR . '/gutenberg/build/block-library/style.css';
			$block_library_theme = WP_PLUGIN_DIR . '/gutenberg/build/block-library/theme.css';
			// Gutenberg プラグインが入ってる場合
			if( file_exists( $block_library_style ) === true ) {
				$ret['block-library-style'] = $block_library_style;
			}
			else {
				unset( $ret['block-library-style'] );
			}
			if( file_exists( $block_library_theme ) === true ) {
				$ret['block-library-theme'] = $block_library_theme;
			}
			else {
				unset( $ret['block-library-theme'] );
			}
		}

		if( $awesome['ver'][0] === '4' ) {
			$ret['awesome'] = str_replace( 5, 4, $ret['awesome'] );
			$ret['awesome-minimum'] = str_replace( 5, 4, $ret['awesome-minimum'] );
		}

		return $ret;
	}

	protected function styles_amp() {
		return array(
			'luxe-amp'		=> $this->_css_dir . 'luxe-amp.css',

			'icomoon'		=> $this->_css_dir . 'icomoon.css',

			'style_amp'	=> TPATH . DSEP . 'style-amp.css',
			'sns'		=> $this->_tmpl_dir . 'sns.css',
			'sns-flat'	=> $this->_tmpl_dir . 'sns-flat.css',
			'sns-icon'	=> $this->_tmpl_dir . 'sns-icon.css',

			'toc'		=> $this->_tmpl_dir . 'toc.css',
			//'blogcard'	=> $this->_tmpl_dir . 'blogcard.css',
			'search'	=> $this->_tmpl_dir . 'widget-search.css',
			'archive'	=> $this->_tmpl_dir . 'widget-archive.css',
			'calendar'	=> $this->_tmpl_dir . 'widget-calendar.css',
			'tagcloud'	=> $this->_tmpl_dir . 'widget-tagcloud.css',
			'new-post'	=> $this->_tmpl_dir . 'widget-new-post.css',
			'rcomments'	=> $this->_tmpl_dir . 'widget-rcomments.css',
			'adsense'	=> $this->_tmpl_dir . 'widget-adsense-amp.css',
			'follow-button'	=> $this->_tmpl_dir . 'widget-follow-button.css',
			'rss-feedly'	=> $this->_tmpl_dir . 'widget-rss-feedly.css',
			'qr-code'	=> $this->_tmpl_dir . 'widget-qr-code.css',

			'balloon'	=> $this->_tmpl_dir . 'balloon.css',
/*
			'prism-amp-default'		=> $this->_css_dir . DSEP . 'prism' . DSEP . 'prism-amp-default.css',
			'prism-amp-dark'		=> $this->_css_dir . DSEP . 'prism' . DSEP . 'prism-amp-dark.css',
			'prism-amp-okaidia'		=> $this->_css_dir . DSEP . 'prism' . DSEP . 'prism-amp-okaidia.css',
			'prism-amp-twilight'		=> $this->_css_dir . DSEP . 'prism' . DSEP . 'prism-amp-twilight.css',
			'prism-amp-coy'			=> $this->_css_dir . DSEP . 'prism' . DSEP . 'prism-amp-coy.css',
			'prism-amp-solarized-light'	=> $this->_css_dir . DSEP . 'prism' . DSEP . 'prism-amp-solarized-light.css',
			'prism-amp-tomorrow-night'	=> $this->_css_dir . DSEP . 'prism' . DSEP . 'prism-amp-tomorrow-night.css',
*/
		);
	}

	protected function scripts_dom_content_loaded() {
		return array(
			'luxe'		=> true,
		);
	}

	protected function scripts_jquery_depend() {
		return array(
			'sscroll'	=> $this->_js_dir . 'smoothScroll.min.js',
			'stickykit'	=> $this->_js_dir . 'jquery.sticky-kit.min.js',
			'strip'		=> $this->_js_dir . 'strip.pkgd.min.js',
			'tosrus'	=> $this->_js_dir . 'jquery.tosrus.all.min.js',
			'lightcase'	=> $this->_js_dir . 'lightcase.js',
			'throttle'	=> $this->_js_dir . 'jquery.ba-throttle-debounce.min.js',
			'fluidbox'	=> $this->_js_dir . 'jquery.fluidbox.min.js',
			'autosize'	=> $this->_js_dir . 'autosize.min.js',
			'autocomplete'	=> $this->_js_dir . 'jquery-ui-autocomplete.js',
			'luxe'		=> true,
		);
	}

	protected function scripts_async() {
		return array(
			'async'		=> true,
			//'md5'		=> $this->_js_dir . 'md5.min.js',
		);
	}

	protected function scripts_search_highlight() {
		return array(
			'highlight'	=> $this->_js_dir . 'jquery.highlight.js',
			'supertext'	=> $this->_js_dir . 'jquery-supertextconverter-plugin.min.js',
			'thk-highlight'	=> $this->_js_dir . 'thk-highlight.js'
		);
	}

	protected function dir_replace() {
		global $luxe, $awesome;

		$ret = array(
			'bootstrap'		=> true,
			'awesome'		=> $awesome['uri'],
			'awesome-minimum'	=> $awesome['uri'],
			'icomoon'		=> true,
			//'strip'		=> true,	// ライブラリの CSS 直で書き換えた
			//'tosrus'		=> true,	// 不要
			'lightcase'		=> true,
		);

		if( isset( $luxe['awesome_load_file'] ) && $luxe['awesome_load_file'] === 'local' ) {
			$ret['awesome']		= true;
			$ret['awesome-minimum']	= true;
		}

		return $ret;
	}

	protected function quote_to_apos() {
		return array(
			'awesome'		=> true,
			'awesome-minimum'	=> true,
		);
	}

	protected function jquery() {
		return array(
			'jquery'    => ABSPATH . WPINC . '/js/jquery/jquery.js',
			'migrate'   => ABSPATH . WPINC . '/js/jquery/jquery-migrate.min.js'
		);
	}
}
