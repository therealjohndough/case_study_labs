<?php
/**
 * Default page template.
 *
 * Provides a simple full‑width layout for static pages. If you wish to use
 * specialized layouts (e.g. service landing), create a separate template
 * file with a Template Name header. This fallback template displays
 * the page title and content with optional featured image.
 *
 * @package auragrid-lite
 */

get_header();
?>

<main id="main-content" class="site-main">
  <?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'container-narrow' ); ?> style="padding-top: var(--spacing-section); padding-bottom: var(--spacing-section);">
      <header class="page-header anim-reveal text-center" style="margin-bottom: 2rem;">
        <h1 class="page-title">
          <?php the_title(); ?>
        </h1>
      </header>
      <?php if ( has_post_thumbnail() ) : ?>
        <figure class="featured-image glass-panel anim-reveal" style="padding: 0.5rem; margin-bottom: var(--spacing-section); border-radius: 12px; overflow: hidden;">
          <?php the_post_thumbnail( 'large', [ 'style' => 'width: 100%; height: auto; display:block; border-radius: 8px;' ] ); ?>
        </figure>
      <?php endif; ?>
      <div class="page-content anim-reveal" style="font-size: 1.1rem; line-height: 1.8;">
        <?php the_content(); ?>
      </div>
    </article>
  <?php endwhile; ?>
</main>

<?php get_footer();