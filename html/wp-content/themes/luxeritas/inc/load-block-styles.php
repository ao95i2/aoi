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

if( class_exists( 'thk_block_styles' ) === false ):
class thk_block_styles {
	private $_styles  = array( 'styles' => '' );
	private $_list_view_content = false;

	public function __construct() {
	}

	public function block_styles( $list_view_content = false ) {
		global $_is, $luxe, $post, $sidebars_widgets;

		// 再利用ブロックウィジェットのブロックスタイル
		$widget_areas = array();
		$contents = '';

		foreach( (array)$sidebars_widgets as $key => $val ) {
			if( isset( $luxe['amp'] ) ) {
				if( $key === 'wp_inactive_widgets' || strrpos( $key, '-amp') !== strlen( $key ) - 4 ) continue;
			}
			else {
				if( $key === 'wp_inactive_widgets' || strrpos( $key, '-amp') === strlen( $key ) - 4 ) continue;
			}
			if( !empty( $val ) ) {
				foreach( (array)$val as $k => $v ) {
					if( stripos( $v, 'thk_reusable_blocks_widget' ) === 0 ) {
						$widget_areas[] = $key;
						break;
					}
				}
			}
		}

		if( !empty( $widget_areas ) ) {
			foreach( (array)$widget_areas as $val ) {
				ob_start();
				dynamic_sidebar( $val );
				$contents .= ob_get_clean();
			}
		}

		// 投稿コンテンツ内のブロックスタイル
		if( $_is['singular'] === true ) {
			$contents .= $post->post_content;
		}

		// アーカイブが全文表示の場合
		if( $list_view_content === true ) {
			if( have_posts() === true ) {
				$ret = $this->create_block_styles( $contents );
				while( have_posts() === true ) {
					the_post();
					$ret = $this->create_block_styles( $post->post_content );
				}
			}
			return $ret;
		}

		return $this->create_block_styles( $contents );
	}

	private function create_block_styles( $contents ) {
		global $luxe, $awesome;

		$css_dir = TPATH . DSEP . 'css' . DSEP;
		$styles_dir = TPATH . DSEP . 'styles' . DSEP;

		// font-size
		for( $i = 10; 56 >= $i; ++$i ) {
			if( !isset( $this->_styles['has-' . $i . '-px-font-size'] ) ) {
				if( strpos( $contents, 'has-' . $i . '-px-font-size' ) !== false ) {
					$this->_styles['styles'] .= '.has-' . $i . '-px-font-size{font-size:' . $i . 'px}';
					$this->_styles['has-' . $i . '-px-font-size'] = true;
				}
			}
		}
		if( !isset( $this->_styles['has-small-font-size'] ) ) {
			if( strpos( $contents, 'has-small-font-size' ) !== false ) {
				$this->_styles['styles'] .= '.has-small-font-size{font-size:13px}';
				$this->_styles['has-small-font-size']  = true;
			}
		}
		if( !isset( $this->_styles['has-medium-font-size'] ) ) {
			if( strpos( $contents, 'has-medium-font-size' ) !== false ) {
				$this->_styles['styles'] .= '.has-medium-font-size{font-size:20px}';
				$this->_styles['has-medium-font-size'] = true;
			}
		}
		if( !isset( $this->_styles['has-large-font-size'] ) ) {
			if( strpos( $contents, 'has-large-font-size' ) !== false ) {
				$this->_styles['styles'] .= '.has-large-font-size{font-size:36px}';
				$this->_styles['has-large-font-size']  = true;
			}
		}
		if( !isset( $this->_styles['has-huge-font-size'] ) ) {
			if( strpos( $contents, 'has-huge-font-size' ) !== false ) {
				$this->_styles['styles'] .= '.has-huge-font-size{font-size:48px}';
				$this->_styles['has-huge-font-size']   = true;
			}
		}

		$luxe_blocks = [
			'<div class="wp-block-luxe-blocks-vertical"'	=> 'vertical.css',				// 縦書き
			'<span class="wp-block-luxe-blocks-topic-icon"'	=> 'topic.css',					// トピック
			'<div class="wp-block-luxe-blocks-accordion"'	=> 'accordion-' . $awesome['ver'][0] . '.css',	// アコーディオン
			'<div class="wp-block-luxe-blocks-profile"'	=> 'profile.css',				// 紹介文（Profile）
			' luxe-overlay-'				=> 'block-overlay.css',				// オーバーレイ
		];

		if( isset( $luxe['amp'] ) || ( isset( $luxe['wp_block_library_load'] ) && $luxe['wp_block_library_load'] === 'none' ) ) {
			$luxe_blocks += [
				'<span class="luxe-hilight-'		=> 'inline-hilight.css',	// 蛍光ペン
				'<span class="luxe-dot-hilight-'	=> 'inline-hilight-dot.css',	// 蛍光ペン（ドット型）
			];

			if( isset( $luxe['amp'] ) ) {
				$luxe_blocks += [
					'<ul class="wp-block-gallery '	=> 'wp-block-gallery-amp.css',	// ブロックエディタのギャラリー
				];
			}
		}

		foreach( $luxe_blocks as $key => $css ) {
			$global_key = 'blocks-' . str_replace( '.css', '', $css );
			if( !isset( $this->_styles[$global_key] ) ) {
				if( strpos( $contents, $key ) !== false ) {
					$this->_styles['styles'] .= thk_fgc( $styles_dir . $css );
					$this->_styles[$global_key] = true;
				}
			}
		}

		// 吹き出し
		if( strpos( $contents, '<div class="wp-block-luxe-blocks-balloon"' ) !== false ) {
			// 全共通
			$balloon_dir = $css_dir . 'balloon' . DSEP;
			if( !isset( $this->_styles['balloon-common'] ) ) {
				$this->_styles['styles'] .= thk_fgc( $balloon_dir . 'common.css' );
				$this->_styles['balloon-common'] = true;
			}
			// 通常共通
			if( !isset( $this->_styles['balloon-normal-common'] ) ) {
				if(
					strpos( $contents, '<div class="luxe-bl-lmain"' ) !== false ||
					strpos( $contents, '<div class="luxe-bl-rmain"' ) !== false
				) {
					$this->_styles['styles'] .= thk_fgc( $balloon_dir . 'normal-common.css' );
					$this->_styles['balloon-normal-common'] = true;
				}
			}
			// 左通常
			if( !isset( $this->_styles['balloon-normal-left'] ) ) {
				if( strpos( $contents, '<div class="luxe-bl-lbf"' ) !== false ) {
					$this->_styles['styles'] .= thk_fgc( $balloon_dir . 'normal-left.css' );
					$this->_styles['balloon-normal-left'] = true;
				}
			}
			// 右通常
			if( !isset( $this->_styles['balloon-normal-right'] ) ) {
				if( strpos( $contents, '<div class="luxe-bl-rbf"' ) !== false ) {
					$this->_styles['styles'] .= thk_fgc( $balloon_dir . 'normal-right.css' );
					$this->_styles['balloon-normal-right'] = true;
				}
			}
			// 考え共通
			if( !isset( $this->_styles['balloon-thought-common'] ) ) {
				if(
					strpos( $contents, '<div class="luxe-bl-ltk"' ) !== false ||
					strpos( $contents, '<div class="luxe-bl-rtk"' ) !== false
				) {
					$this->_styles['styles'] .= thk_fgc( $balloon_dir . 'thought-common.css' );
					$this->_styles['balloon-thought-common'] = true;
				}
			}
			// 左考え
			if( !isset( $this->_styles['balloon-thought-left'] ) ) {
				if( strpos( $contents, '<div class="luxe-bl-tk-lbf"' ) !== false ) {
					$this->_styles['styles'] .= thk_fgc( $balloon_dir . 'thought-left.css' );
					$this->_styles['balloon-thought-left'] = true;
				}
			}
			// 右考え
			if( !isset( $this->_styles['balloon-thought-right'] ) ) {
				if( strpos( $contents, '<div class="luxe-bl-tk-rbf"' ) !== false ) {
					$this->_styles['styles'] .= thk_fgc( $balloon_dir . 'thought-right.css' );
					$this->_styles['balloon-thought-right'] = true;
				}
			}
		}

		// スクロールブロック
		if( strpos( $contents, 'wp-block-luxe-blocks-scroll-block' ) !== false ) {
			if( !isset( $this->_styles['scroll-block'] ) ) {
				if( isset( $luxe['amp'] ) ) {
					$this->_styles['styles'] .= thk_fgc( $styles_dir . 'scroll-block-amp.css' );
				}
				else {
					$this->_styles['styles'] .= thk_fgc( $styles_dir . 'scroll-block.css' );
				}
				$this->_styles['scroll-block'] = true;
			}

			// スクロールブロックで高さ・背景色・背景画像のスタイル指定があるもの
			if( strpos( $contents, 'luxe-scroll-block-css:' ) !== false ) {
				global $post;

				preg_match_all( '/<div[^>]+?class="[^>]+?luxe-scroll-block-css:[^>]+?>.*?<(figure|pre)[^>]+?>/ism', $contents, $scroll_block_css_array );

				foreach( array_unique( $scroll_block_css_array[0] ) as $scroll_block_css ) {
					$id_css = preg_replace( '/<div[^>]+?id="([^"]+?)"[^>]+?class="[^>]+?luxe-scroll-block-css\:([0-9a-f]+)[^>]+?>.*?<(figure|pre)[^>]+?>/ism', '$1#$2# $3', $scroll_block_css );

					if( stripos( $id_css, '#' ) !== false ) {
						$id_css_array = explode( '#', $id_css );
						if( count( $id_css_array ) >= 3 ) {
							$css = '#' . $id_css_array[0] . $id_css_array[2] . hex2bin( $id_css_array[1] );
							$this->_styles['styles'] .= $css;
							// 不要になった class 削除その1
							$post->post_content = str_replace( $id_css_array[1], '', $post->post_content );
						}
					}
				}
				// 不要になった class 削除その2
				$post->post_content = str_replace( ' luxe-scroll-block-css:', '', $post->post_content );
			}

			/* ドラッグスクロール用のスクリプトは thk_the_content() 内で wp_enqueue_script するようにした
			if( stripos( $contents, 'dragscroll-on' ) !== false ) {
				wp_enqueue_script( 'luxe-dragscroll', TURI . '/js/dragscroll.min.js', array(), false );
			}
			*/
		}

		return $this->_styles['styles'];
	}
}
endif;
