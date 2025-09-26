<?php
/**
 * jd-micro-child functions and definitions
 */

add_action( 'wp_enqueue_scripts', 'jd_micro_child_enqueue_styles' );
function jd_micro_child_enqueue_styles() {
    $parenthandle = 'jd-micro-style'; // Parent theme's main stylesheet handle (guessed)
    $parent_style = get_template_directory_uri() . '/style.css';

    // Enqueue parent style
    wp_enqueue_style( 'parent-style', $parent_style, array(), wp_get_theme( get_template() )->get('Version') );

    // Enqueue child theme stylesheet
    wp_enqueue_style( 'jd-micro-child-style', get_stylesheet_directory_uri() . '/assets/css/skyworld.css', array('parent-style'), '1.0.0' );

    // Enqueue child theme JS
    wp_enqueue_script( 'jd-micro-child-js', get_stylesheet_directory_uri() . '/assets/js/skyworld.js', array('jquery'), '1.0.0', true );

    // Load Font Awesome from CDN used by the template
    wp_enqueue_style( 'font-awesome-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );
}

// Allow SVG uploads (optional convenience)
add_filter( 'upload_mimes', 'jd_micro_child_mime_types' );
function jd_micro_child_mime_types( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
