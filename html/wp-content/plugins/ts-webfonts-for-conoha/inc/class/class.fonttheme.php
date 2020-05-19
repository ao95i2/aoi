<?php

class TypeSquare_ST_Fonttheme {
	private static $fonttheme;
	private static $instance;

	private function __construct(){}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	public static function get_fonttheme() {
		static $fonttheme;

		$fonttheme = array(
			'basic' => array(
				'name'	=> 'ベーシック',
				'fonts' => array(
					'title'   => 'ゴシックMB101 M',
					'lead'    => 'ヒラギノ角ゴ W2',
					'content' => 'TBUDゴシック R',
					'bold'    => 'TBUDゴシック E',
				),
			),
			'business' => array(
				'name'	=> 'ビジネス',
				'fonts' => array(
					'title'   => '見出ゴMB31',
					'lead'    => 'UD黎ミン R',
					'content' => 'UD新ゴコンデンス90 L',
					'bold'    => 'UD新ゴコンデンス90 M',
				),
			),
			'pop' => array(
				'name'	=> 'ポップ',
				'fonts' => array(
					'title'   => 'キャピーN B',
					'lead'    => 'じゅん501',
					'content' => 'じゅん201',
					'bold'    => 'じゅん501',
				),
			),
			'crisp' => array(
				'name'	=> 'クリスプ',
				'fonts' => array(
					'title'   => 'DSダダ',
					'lead'    => 'DSそよ風',
					'content' => 'トーキング',
					'bold'    => 'トーキング',
				),
			),
			'luxury' => array(
				'name'	=> 'ラグジュアリー',
				'fonts' => array(
					'title'   => 'A1明朝',
					'lead'    => 'A1明朝',
					'content' => 'UD黎ミン R',
					'bold'    => 'UD黎ミン B',
				),
			),
			'conoha' => array(
				'name'	=> 'このは1',
				'fonts' => array(
					'title'   => 'ハルクラフト',
					'lead'    => 'ハルクラフト',
					'content' => '那欽',
					'bold'    => '那欽',
				),
			),
			'conoha2' => array(
				'name'	=> 'このは2',
				'fonts' => array(
					'title'   => 'はるひ学園',
					'lead'    => '丸フォーク M',
					'content' => 'シネマレター',
					'bold'    => 'シネマレター',
				),
			),
		);

		$options = get_option( 'typesquare_custom_theme' );
		if ( $options && isset( $options['theme'] ) && is_array( $options['theme'] ) ) {
			$fonttheme = $fonttheme + $options['theme'];
		}
		return $fonttheme;
	}

	public static function get_custom_theme_json() {
		$options = get_option( 'typesquare_custom_theme' );
		return json_encode($options['theme']);
	}
}
