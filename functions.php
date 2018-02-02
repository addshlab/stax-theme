<?php

if ( ! isset( $content_width ) ) {
	$content_width = 600;
}

/**
 * テーマにコンポーネントされていないファイルの検知
 */
function no_component_files() {
	$message = '<p>Stax Themeの基本構成ファイルは functions.php, index.php, screenshot.png, style.css の4つです。</p>';
	$flag = 0;
	$theme_dir_path = get_stylesheet_directory();
	$allow_files = array(
		$theme_dir_path . '/functions.php',
		$theme_dir_path . '/index.php',
		$theme_dir_path . '/screenshot.png',
		$theme_dir_path . '/style.css',
	);

	foreach ( glob( $theme_dir_path . '/*', GLOB_BRACE ) as $file_path ) {
		if( is_dir( $file_path ) ){
			if ( false === in_array( $file_path, $allow_files ) ) {
				$dir_alert .= '<p>テーマディレクトリ内にコンポーネント外のディレクトリ ' . $file_path . ' が存在します。</p>';
				$flag = 1;
			}
		}
		if( is_file( $file_path ) ){
			if ( false === in_array( $file_path, $allow_files ) ) {
				$file_alert .= '<p>テーマディレクトリ内にコンポーネント外のファイル ' . $file_path . ' が存在します。</p>';
				$flag = 1;
			}
		}
	}

	if ( $flag === 1 ) {
		echo '<div class="message error">';
		echo $dir_alert;
		echo $file_alert;
		echo $message;
		echo '</div>';
	}
}
add_action ( 'admin_notices', 'no_component_files' );

/**
 * 管理画面以外でもDashiconsを使用する
 * DashiconsはWordPress3.8以上で使用可能なアイコンフォントセットです
 * @see https://developer.wordpress.org/resource/dashicons/
 */
function enqueue_scripts() {
    wp_enqueue_style( 'dashicons', site_url( '/' ) . '/wp-includes/css/dashicons.min.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

/**
 * style.cssにファイルタイムスタンプ由来のフィンガープリントを付与
 * @see https://worklog.be/archives/2983
*/
function default_style_fingerprint( $styles ) {
	$mtime = filemtime( get_stylesheet_directory() . '/style.css' );
	$styles->default_version = $mtime;
}
add_action( 'wp_default_styles', 'default_style_fingerprint' );

/**
 * テーマのサポートする機能の定義
 */
function custom_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo' );
}
add_action( 'after_setup_theme', 'custom_theme_setup' );

/**
 * タイトルタグのカスタマイズ
 */
function wpdocs_hack_wp_title_for_home( $title ) {
	if ( empty( $title ) && ( is_home() || is_front_page() ) ) {
		$title = get_bloginfo( 'name' );
	} elseif ( is_single() ) {
		$title = get_the_title() . ' | ' . get_bloginfo( 'name' );
	} else {
		$title = get_the_title();
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'wpdocs_hack_wp_title_for_home' );

/**
 * Meta Description のカスタマイズ
 */
 function add_meta_description() {
 	if ( is_home() ) {
		$content = get_bloginfo( 'description' );
	} elseif ( is_single() ) {
		$content = get_the_excerpt();
	} else {
		$content = '';
	}

 	echo '<meta name="description" content="' . $content . '">';
 }
 add_filter( 'wp_head', 'add_meta_description' );

/**
 * ウィジェットエリアの定義
 */
function theme_slug_widgets_init() {
	register_sidebar(
		array(
			'name' => __( 'Footer menu(Stax Theme)', 'stax' ),
			'id' => 'footer-menu',
			'description' => __( 'Display menu to footer.', 'stax' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name' => __( 'Sidebar menu(Stax Theme)', 'stax' ),
			'id' => 'sidebar-menu',
			'description' => __( 'Display menu to sidebar.', 'stax' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		)

	);
}
add_action( 'widgets_init', 'theme_slug_widgets_init' );

