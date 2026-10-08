<?php
/**
 * Plugin Name: Simple Speed Cleanup
 * Description: Removes unneeded WordPress extras and defers scripts to help page speed and Core Web Vitals.
 * Version:     1.0.0
 * Author:      Adeel Ahmed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Remove the emoji scripts and styles (most sites do not need them).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// 2. Remove extra links WordPress adds to <head>.
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// 3. Keep only the last 5 revisions per post to keep the database lean.
add_filter( 'wp_revisions_to_keep', function () {
	return 5;
} );

// 4. Defer front-end scripts so they do not. block page rendering.
//    jQuery is skipped because many themes and plugins run inline code that needs it right away.
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
	if ( is_admin() ) {
		return $tag;
	}

	$skip = array( 'jquery', 'jquery-core', 'jquery-migrate' );
	if ( in_array( $handle, $skip, true ) ) {
		return $tag;
	}

	// Leave scripts that already have defer or async alone.
	if ( false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' ) ) {
		return $tag;
	}

	return str_replace( ' src=', ' defer src=', $tag );
}, 10, 3 );
