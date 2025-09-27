<?php
/**
 * Header Template
 *
 * @package CustomClientTheme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site pixel-pattern">
    <a class="skip-link screen-reader-text" href="#primary">
        <?php _e('Skip to content', 'custom-client-theme'); ?>
    </a>

    <header id="masthead" class="site-header vhs-glitch">
        <div class="container">
            <div class="header-content flex items-center justify-between">
                <div class="site-branding">
                    <?php
                    $custom_logo = get_theme_option('site_logo');
                    if ($custom_logo) :
                        ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="custom-logo-link">
                            <img src="<?php echo esc_url($custom_logo['url']); ?>" 
                                 alt="<?php echo esc_attr($custom_logo['alt']); ?>" 
                                 class="custom-logo">
                        </a>
                        <?php
                    else :
                        if (has_custom_logo()) :
                            the_custom_logo();
                        else :
                            ?>
                            <h1 class="site-title font-pixel">
                                <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
                            </h1>
                            <?php
                        endif;
                    endif;
                    
                    $tagline = get_theme_option('site_tagline', get_bloginfo('description'));
                    if ($tagline) :
                        ?>
                        <p class="site-description"><?php echo esc_html($tagline); ?></p>
                        <?php
                    endif;
                    ?>
                </div>

                <nav id="site-navigation" class="main-navigation site-nav">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span class="screen-reader-text"><?php _e('Primary Menu', 'custom-client-theme'); ?></span>
                    </button>
                    
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => 'custom_theme_fallback_menu',
                    ));
                    ?>
                </nav>

                <div class="header-actions">
                    <?php if (current_user_can('edit_posts')) : ?>
                        <a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>" 
                           class="btn btn-primary btn-sm edit-site-btn">
                            <?php _e('Edit Site', 'custom-client-theme'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <?php
    // Add ACF form head if needed
    if (function_exists('acf_form_head')) {
        acf_form_head();
    }
    ?>