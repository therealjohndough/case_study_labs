<?php
/**
 * Custom Client Theme Functions
 *
 * @package CustomClientTheme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function custom_client_theme_setup() {
    // Add theme support for various features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style'
    ));
    
    // Custom logo support
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'custom-client-theme'),
        'footer'  => __('Footer Menu', 'custom-client-theme'),
    ));
    
    // Add editor styles
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'custom_client_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function custom_client_theme_scripts() {
    // Main stylesheet
    wp_enqueue_style(
        'custom-client-theme-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
    
    // Google Fonts are loaded via CSS @import in style.css
    
    // Custom JavaScript
    wp_enqueue_script(
        'custom-client-theme-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('custom-client-theme-script', 'customTheme', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('custom_theme_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'custom_client_theme_scripts');

/**
 * ACF Configuration
 */
// Enable ACF frontend forms
add_action('init', 'custom_theme_acf_init');
function custom_theme_acf_init() {
    // Ensure ACF is active
    if (!function_exists('acf_form_head')) {
        return;
    }
}

/**
 * Add ACF Field Groups for Theme Customization
 */
function custom_theme_add_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    // Site Settings Field Group
    acf_add_local_field_group(array(
        'key' => 'group_site_settings',
        'title' => 'Site Settings',
        'fields' => array(
            array(
                'key' => 'field_site_logo',
                'label' => 'Site Logo',
                'name' => 'site_logo',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_site_tagline',
                'label' => 'Site Tagline',
                'name' => 'site_tagline',
                'type' => 'text',
                'default_value' => 'Your Custom Tagline Here',
            ),
            array(
                'key' => 'field_contact_email',
                'label' => 'Contact Email',
                'name' => 'contact_email',
                'type' => 'email',
            ),
            array(
                'key' => 'field_contact_phone',
                'label' => 'Contact Phone',
                'name' => 'contact_phone',
                'type' => 'text',
            ),
            array(
                'key' => 'field_social_links',
                'label' => 'Social Media Links',
                'name' => 'social_links',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_social_platform',
                        'label' => 'Platform',
                        'name' => 'platform',
                        'type' => 'select',
                        'choices' => array(
                            'facebook' => 'Facebook',
                            'twitter' => 'Twitter',
                            'instagram' => 'Instagram',
                            'linkedin' => 'LinkedIn',
                            'youtube' => 'YouTube',
                        ),
                    ),
                    array(
                        'key' => 'field_social_url',
                        'label' => 'URL',
                        'name' => 'url',
                        'type' => 'url',
                    ),
                ),
                'min' => 0,
                'max' => 10,
                'layout' => 'table',
                'button_label' => 'Add Social Link',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
    ));
    
    // Homepage Content Field Group
    acf_add_local_field_group(array(
        'key' => 'group_homepage_content',
        'title' => 'Homepage Content',
        'fields' => array(
            array(
                'key' => 'field_hero_title',
                'label' => 'Hero Title',
                'name' => 'hero_title',
                'type' => 'text',
                'default_value' => 'Welcome to Our Site',
            ),
            array(
                'key' => 'field_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Discover amazing content and experiences.',
            ),
            array(
                'key' => 'field_hero_image',
                'label' => 'Hero Background Image',
                'name' => 'hero_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'large',
            ),
            array(
                'key' => 'field_hero_cta_text',
                'label' => 'Hero CTA Button Text',
                'name' => 'hero_cta_text',
                'type' => 'text',
                'default_value' => 'Get Started',
            ),
            array(
                'key' => 'field_hero_cta_url',
                'label' => 'Hero CTA Button URL',
                'name' => 'hero_cta_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_content_sections',
                'label' => 'Content Sections',
                'name' => 'content_sections',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_section_title',
                        'label' => 'Section Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_section_content',
                        'label' => 'Section Content',
                        'name' => 'content',
                        'type' => 'wysiwyg',
                        'toolbar' => 'basic',
                        'media_upload' => 1,
                    ),
                    array(
                        'key' => 'field_section_image',
                        'label' => 'Section Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                    ),
                ),
                'min' => 0,
                'max' => 10,
                'layout' => 'block',
                'button_label' => 'Add Section',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-home.php',
                ),
            ),
            array(
                array(
                    'param' => 'page_type',
                    'operator' => '==',
                    'value' => 'front_page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
    ));
}
add_action('acf/init', 'custom_theme_add_acf_fields');

/**
 * Add ACF Options Page
 */
function custom_theme_add_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Theme Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug'  => 'theme-settings',
            'capability' => 'edit_posts',
            'icon_url'   => 'dashicons-admin-customizer',
        ));
    }
}
add_action('acf/init', 'custom_theme_add_options_page');

/**
 * Frontend Form Handler
 */
function custom_theme_handle_frontend_form() {
    if (!isset($_POST['acf']) || !wp_verify_nonce($_POST['_wpnonce'], 'acf_form')) {
        return;
    }
    
    // Process ACF form submission
    acf_form_head();
}
add_action('init', 'custom_theme_handle_frontend_form');

/**
 * Helper Functions
 */

/**
 * Get ACF field with fallback
 */
function get_theme_field($field_name, $post_id = null, $fallback = '') {
    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        return $value ? $value : $fallback;
    }
    return $fallback;
}

/**
 * Get options field with fallback
 */
function get_theme_option($option_name, $fallback = '') {
    if (function_exists('get_field')) {
        $value = get_field($option_name, 'option');
        return $value ? $value : $fallback;
    }
    return $fallback;
}

/**
 * Display social media links
 */
function display_social_links() {
    $social_links = get_theme_option('social_links');
    if ($social_links) {
        echo '<div class="social-links">';
        foreach ($social_links as $link) {
            $platform = $link['platform'];
            $url = $link['url'];
            echo '<a href="' . esc_url($url) . '" class="social-link social-' . esc_attr($platform) . '" target="_blank" rel="noopener">';
            echo '<i class="fab fa-' . esc_attr($platform) . '"></i>';
            echo '</a>';
        }
        echo '</div>';
    }
}

/**
 * Custom excerpt function
 */
function custom_excerpt($limit = 20) {
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    if (count($excerpt) >= $limit) {
        array_pop($excerpt);
        $excerpt = implode(' ', $excerpt) . '...';
    } else {
        $excerpt = implode(' ', $excerpt);
    }
    return $excerpt;
}

/**
 * Add Frontend Editing Capability
 */
function add_frontend_editing_links() {
    if (current_user_can('edit_posts') && !is_admin()) {
        echo '<div class="frontend-edit-bar">';
        echo '<a href="' . admin_url('customize.php') . '" class="btn btn-primary">Customize Site</a>';
        if (is_singular()) {
            echo '<a href="' . get_edit_post_link() . '" class="btn btn-secondary">Edit Page</a>';
        }
        echo '</div>';
    }
}
add_action('wp_footer', 'add_frontend_editing_links');

/**
 * Brevo Integration Placeholder
 */
function custom_theme_brevo_integration() {
    // This function will be used to integrate with Brevo
    // Once the Brevo plugin is installed and configured
    if (function_exists('brevo_form')) {
        // Integration code will go here
    }
}

/**
 * Agile Store Locator Integration Placeholder
 */
function custom_theme_store_locator_integration() {
    // This function will be used to integrate with Agile Store Locator
    // Once the plugin is installed and configured
    if (function_exists('asl_store_locator')) {
        // Integration code will go here
    }
}

/**
 * Custom Widget Areas
 */
function custom_theme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'custom-client-theme'),
        'id'            => 'footer-widgets',
        'description'   => __('Widget area for the footer', 'custom-client-theme'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'custom_theme_widgets_init');

/**
 * Fallback menu function
 */
function custom_theme_fallback_menu() {
    echo '<ul id="primary-menu" class="menu nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'custom-client-theme') . '</a></li>';
    
    // Add pages to menu
    $pages = get_pages(array('sort_column' => 'menu_order'));
    foreach ($pages as $page) {
        echo '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html($page->post_title) . '</a></li>';
    }
    
    // Add blog link if not on front page
    if (get_option('show_on_front') == 'page') {
        echo '<li><a href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '">' . __('Blog', 'custom-client-theme') . '</a></li>';
    }
    
    echo '</ul>';
}

/**
 * Add custom body classes
 */
function custom_theme_body_classes($classes) {
    // Add class for logged-in users
    if (is_user_logged_in()) {
        $classes[] = 'logged-in-user';
    }
    
    // Add class for frontend editing capability
    if (current_user_can('edit_posts')) {
        $classes[] = 'can-edit';
    }
    
    return $classes;
}
add_filter('body_class', 'custom_theme_body_classes');

/**
 * Contact Form Handler (AJAX)
 */
function handle_contact_form_submission() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'custom_theme_nonce')) {
        wp_die('Security check failed');
    }
    
    // Process contact form data here
    // This is a placeholder for contact form functionality
    
    wp_send_json_success(__('Message sent successfully!', 'custom-client-theme'));
}
add_action('wp_ajax_submit_contact_form', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form_submission');

/**
 * Security Enhancements
 */
// Remove WordPress version from head
remove_action('wp_head', 'wp_generator');

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');