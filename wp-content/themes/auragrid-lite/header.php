<?php
/**
 * Header template
 *
 * @package Auragrid_Lite
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> >
<?php wp_body_open(); ?>
<a href="#main-content" class="skip-link screen-reader-text"><?php esc_html_e( 'Skip to content', 'auragrid-lite' ); ?></a>
<header class="site-header" role="banner">
  <div class="site-logo">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
      <?php
        if ( has_custom_logo() ) {
          the_custom_logo();
        } else {
          bloginfo( 'name' );
        }
      ?>
    </a>
  </div>
  <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'auragrid-lite' ); ?>">
    <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => '',
        'fallback_cb'    => '__return_false',
      ]);
    ?>
  </nav>
  <button class="hamburger-menu" aria-label="<?php esc_attr_e( 'Open menu', 'auragrid-lite' ); ?>" aria-expanded="false" aria-controls="main-navigation">
    <span class="bar bar1"></span>
    <span class="bar bar2"></span>
    <span class="bar bar3"></span>
  </button>
</header>
<div id="page" class="site-content">
