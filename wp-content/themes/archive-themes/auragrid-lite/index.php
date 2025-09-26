<?php
/**
 * Index template
 *
 * This file is the fallback template used by WordPress to render any request
 * that does not have a more specific template. In this lightweight theme,
 * it displays a list of posts (or the blog page) with a simple layout. The
 * header and footer are loaded as usual. If you have a static front page
 * assigned via the Customizer, WordPress will use front-page.php instead
 * of this file.
 *
 * @package auragrid-lite
 */

get_header();
?>

<main id="main-content" class="site-main">
  <div class="container" style="padding-top: var(--spacing-section); padding-bottom: var(--spacing-section);">
    <?php if ( have_posts() ) : ?>
      <?php if ( is_home() && ! is_front_page() ) : ?>
        <header class="page-header text-center mb-3 anim-reveal">
          <h1 class="page-title"><?php single_post_title(); ?></h1>
        </header>
      <?php endif; ?>
      <div class="project-grid">
        <?php
        $index = 0;
        while ( have_posts() ) : the_post();
          $index++; ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class( 'project-card anim-reveal' ); ?> style="--stagger-index: <?php echo esc_attr( $index ); ?>;">
            <a href="<?php the_permalink(); ?>" class="card-link">
              <?php if ( has_post_thumbnail() ) : ?>
                <div class="card-image-wrapper">
                  <?php the_post_thumbnail( 'large' ); ?>
                </div>
              <?php endif; ?>
              <div class="card-content">
                <h3 class="card-title"><?php the_title(); ?></h3>
                <div class="card-excerpt">
                  <?php the_excerpt(); ?>
                </div>
              </div>
            </a>
          </article>
        <?php endwhile; ?>
      </div>
      <?php the_posts_pagination([
        'mid_size'  => 2,
        'prev_text' => __( '&laquo; Previous', 'auragrid-lite' ),
        'next_text' => __( 'Next &raquo;', 'auragrid-lite' ),
      ]);
    <?php else : ?>
      <div class="glass-panel text-center">
        <p><?php esc_html_e( 'No posts found.', 'auragrid-lite' ); ?></p>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php get_footer();