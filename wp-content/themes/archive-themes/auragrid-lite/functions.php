<?php
/**
 * Aura‑Grid Lite functions and definitions
 */

namespace Auragrid\Lite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Setup theme features
 */
function setup() : void {
    // Load translation
    load_theme_textdomain( 'auragrid-lite', get_template_directory() . '/languages' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Featured images
    add_theme_support( 'post-thumbnails' );

    // Custom logo support
    add_theme_support( 'custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    // HTML5 markup
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );

    // Navigation menus
    register_nav_menus([
        'primary' => __( 'Primary Menu', 'auragrid-lite' ),
    ]);
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup' );

/**
 * Enqueue assets
 */
function assets() : void {
    $version = wp_get_theme()->get( 'Version' );
    // Main stylesheet with filemtime for cache busting
    $style_file = get_template_directory() . '/style.css';
    wp_enqueue_style( 'auragrid-lite', get_template_directory_uri() . '/style.css', [], filemtime( $style_file ) );

    // Main JS for navigation and other behaviours
    $script_file = get_template_directory() . '/js/main.js';
    if ( file_exists( $script_file ) ) {
        wp_enqueue_script( 'auragrid-lite-main', get_template_directory_uri() . '/js/main.js', [], filemtime( $script_file ), true );
    }
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets' );

/**
 * Load custom template tags and extras if needed
 */
// Add more includes here if you modularise functions.
