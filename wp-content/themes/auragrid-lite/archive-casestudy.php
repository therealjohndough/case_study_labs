<?php
/**
 * Archive template for the Case Study post type.
 *
 * This file is responsible for displaying a list of all case studies. It uses
 * a responsive grid of project cards similar to the front page and removes
 * unnecessary scripts. Query parameters are optimized with no_found_rows
 * and disabled caches for better performance. Each card links to the
 * corresponding single case study.
 *
 * @package auragrid-lite
 */

get_header();
?>

<main id="main-content" class="site-main">
  <div class="container-narrow text-center" style="padding-top: var(--spacing-section); padding-bottom: 4rem;">
    <h1 class="section-heading anim-reveal">
      <?php esc_html_e( 'Case Studies', 'auragrid-lite' ); ?>
    </h1>
    <p class="anim-reveal" style="color: var(--color-text-secondary); max-width: 60ch; margin-inline: auto;">
      <?php esc_html_e( 'Results mean more than recognition. Explore by industry or project type.', 'auragrid-lite' ); ?>
    </p>
  </div>

  <div class="container" style="padding-top: 0;">
    <?php
    // Ensure we only query case studies
    $query = new WP_Query([
      'post_type'      => 'casestudy',
      'paged'          => max( 1, get_query_var( 'paged', 1 ) ),
      'no_found_rows'  => false, // we need pagination counts
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ]);
    if ( $query->have_posts() ) : ?>
      <div class="project-grid">
        <?php
        $index = 0;
        while ( $query->have_posts() ) :
          $query->the_post();
          $index++; ?>
          <article class="project-card anim-reveal" style="--stagger-index: <?php echo esc_attr( $index ); ?>;">
            <a href="<?php the_permalink(); ?>" class="card-link">
              <div class="card-image-wrapper">
                <?php
                // Display term pills based on post tags
                $tags = get_the_tags();
                if ( $tags ) : ?>
                  <div class="term-pills-container">
                    <?php foreach ( $tags as $tag ) : ?>
                      <span class="term-pill" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; border-radius: 100px; background: var(--glass-bg-light); color: var(--color-text-primary);">
                        <?php echo esc_html( $tag->name ); ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
                <?php if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'large' );
                } else {
                    // Fallback placeholder
                    echo '<img src="' . esc_url( get_template_directory_uri() . '/assets/images/placeholder.jpg' ) . '" alt="' . esc_attr__( 'Placeholder Image', 'auragrid-lite' ) . '" />';
                } ?>
              </div>
              <div class="card-content">
                <h3 class="card-title"><?php the_title(); ?></h3>
                <div class="card-excerpt"><?php the_excerpt(); ?></div>
              </div>
            </a>
          </article>
        <?php endwhile; ?>
      </div>
      <?php
      // Pagination
      the_posts_pagination([
        'mid_size'  => 2,
        'prev_text' => __( '&laquo; Previous', 'auragrid-lite' ),
        'next_text' => __( 'Next &raquo;', 'auragrid-lite' ),
      ]);
    else : ?>
      <div class="glass-panel text-center">
        <p><?php esc_html_e( 'No case studies have been published yet. Please check back soon.', 'auragrid-lite' ); ?></p>
      </div>
    <?php endif; wp_reset_postdata(); ?>
  </div>
</main>

<?php get_footer();