<?php
/**
 * Template for displaying a single Case Study.
 *
 * This template provides a clean, accessible layout for showcasing a single
 * case study. It preserves the information architecture of the original
 * theme (challenge, solution, details, gallery) but drops heavy third‑party
 * libraries in favour of light CSS and a small vanilla JS slider. All
 * dynamic content is escaped to prevent XSS, and Advanced Custom Fields
 * (ACF) values are pulled if available. If no ACF field exists, the
 * corresponding section is simply not rendered. See the accompanying
 * `main.js` for the slider implementation.
 *
 * @package auragrid-lite
 */

get_header();
?>

<main id="main-content" class="site-main">
  <?php while ( have_posts() ) : the_post(); ?>
    <!-- Hero Section -->
    <header class="container-narrow text-center" style="padding-top: var(--spacing-section); padding-bottom: 4rem;">
      <div class="case-study-header anim-reveal">
        <h1 class="section-heading" style="font-size: clamp(2rem, 5vw, 3rem); margin-bottom: 1.5rem; line-height: 1.2;">
          <?php the_title(); ?>
        </h1>
        <?php if ( has_excerpt() ) : ?>
          <div class="case-study-excerpt" style="color: var(--color-text-secondary); max-width: 60ch; margin: 0 auto 2rem; font-size: 1.2rem; line-height: 1.6;">
            <?php the_excerpt(); ?>
          </div>
        <?php endif; ?>
      </div>
    </header>

    <!-- Featured Image -->
    <?php if ( has_post_thumbnail() ) : ?>
      <div class="container anim-reveal">
        <figure class="glass-panel featured-image" style="padding: 0.5rem; margin-bottom: var(--spacing-section); border-radius: 12px; overflow: hidden;">
          <?php the_post_thumbnail( 'full', [ 'style' => 'width: 100%; height: auto; display: block; border-radius: 8px;' ] ); ?>
        </figure>
      </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="container">
      <div class="split-section split-70-30" style="gap: 3rem;">
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'split-content' ); ?>>
          <?php
          // Challenge section
          $challenge = function_exists( 'get_field' ) ? get_field( 'the_challenge' ) : null;
          if ( $challenge ) :
            ?>
            <section class="case-section anim-slide-left" style="margin-bottom: 3rem;">
              <div class="section-header">
                <span class="section-label" style="color: var(--color-primary); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;"><?php esc_html_e( 'The Challenge', 'auragrid-lite' ); ?></span>
                <h2 class="h3 mb-1" style="margin-top: 0.5rem;">
                  <?php esc_html_e( 'The Challenge', 'auragrid-lite' ); ?>
                </h2>
              </div>
              <div class="content-wrapper" style="font-size: 1.1rem; line-height: 1.8; color: var(--color-text-primary);">
                <?php echo wp_kses_post( $challenge ); ?>
              </div>
            </section>
          <?php endif; ?>

          <?php
          // Solution section
          $solution = function_exists( 'get_field' ) ? get_field( 'our_solution' ) : null;
          if ( $solution ) :
            ?>
            <section class="case-section anim-slide-left" style="margin-bottom: 3rem;">
              <div class="section-header">
                <span class="section-label" style="color: var(--color-primary); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;"><?php esc_html_e( 'Our Solution', 'auragrid-lite' ); ?></span>
                <h2 class="h3 mb-1" style="margin-top: 0.5rem;">
                  <?php esc_html_e( 'Our Solution', 'auragrid-lite' ); ?>
                </h2>
              </div>
              <div class="content-wrapper" style="font-size: 1.1rem; line-height: 1.8; color: var(--color-text-primary);">
                <?php echo wp_kses_post( $solution ); ?>
              </div>
            </section>
          <?php endif; ?>

          <?php
          // Project details & gallery
          $gallery = function_exists( 'get_field' ) ? get_field( 'project_gallery' ) : null;
          if ( $gallery || get_the_content() ) :
            ?>
            <section class="case-section anim-slide-left" style="margin-bottom: 5rem;">
              <div class="section-header">
                <span class="section-label" style="color: var(--color-primary); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">
                  <?php esc_html_e( 'Project Details', 'auragrid-lite' ); ?>
                </span>
                <h2 class="h3 mb-1" style="margin-top: 0.5rem;">
                  <?php esc_html_e( 'Project Details & Gallery', 'auragrid-lite' ); ?>
                </h2>
              </div>
              <div class="content-wrapper" style="font-size: 1.1rem; line-height: 1.8; color: var(--color-text-primary);">
                <?php the_content(); ?>
                <?php if ( $gallery ) : ?>
                  <div class="mt-5 js-simple-slider" style="margin-top: 4rem; margin-bottom: 4rem;">
                    <div class="slider-wrapper" style="position: relative; overflow: hidden; border-radius: 12px;">
                      <div class="slides" style="display: flex; transition: transform 0.5s ease;">
                        <?php foreach ( $gallery as $image ) : ?>
                          <div class="js-slide" style="min-width: 100%; position: relative;">
                            <figure style="margin:0;">
                              <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" style="width:100%; height:auto; display:block; border-radius:12px;">
                              <?php if ( ! empty( $image['caption'] ) ) : ?>
                                <figcaption style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 1rem; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; font-size: 0.9rem;">
                                  <?php echo esc_html( $image['caption'] ); ?>
                                </figcaption>
                              <?php endif; ?>
                            </figure>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <!-- Slider controls -->
                      <button class="js-prev" type="button" style="position: absolute; top: 50%; left: 1rem; transform: translateY(-50%); background: rgba(255,255,255,0.8); border: none; border-radius: 50%; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <span class="screen-reader-text"><?php esc_html_e( 'Previous slide', 'auragrid-lite' ); ?></span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                      </button>
                      <button class="js-next" type="button" style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); background: rgba(255,255,255,0.8); border: none; border-radius: 50%; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <span class="screen-reader-text"><?php esc_html_e( 'Next slide', 'auragrid-lite' ); ?></span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                      </button>
                      <!-- Status indicator (optional) -->
                      <div class="js-status" style="position: absolute; bottom: 1rem; right: 1rem; background: rgba(0,0,0,0.5); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;"></div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </section>
          <?php endif; ?>
        </article>

        <!-- Sidebar -->
        <aside class="case-study-sidebar anim-slide-right">
          <div class="glass-panel" style="position: sticky; top: 120px; padding: 2rem; border-radius: 12px;">
            <h4 class="h4 mb-3" style="font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
              <?php esc_html_e( 'Project Info', 'auragrid-lite' ); ?>
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
              <?php if ( function_exists( 'get_field' ) && ( $client = get_field( 'client_name' ) ) ) : ?>
                <li style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                  <strong style="display: block; margin-bottom: 0.5rem; color: var(--color-text-secondary); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                    <?php esc_html_e( 'Client', 'auragrid-lite' ); ?>
                  </strong>
                  <span style="font-size: 1.1rem;">
                    <?php echo esc_html( $client ); ?>
                  </span>
                </li>
              <?php endif; ?>
              <?php if ( function_exists( 'get_field' ) && ( $year = get_field( 'project_year' ) ) ) : ?>
                <li style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                  <strong style="display: block; margin-bottom: 0.5rem; color: var(--color-text-secondary); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                    <?php esc_html_e( 'Year', 'auragrid-lite' ); ?>
                  </strong>
                  <span style="font-size: 1.1rem;">
                    <?php echo esc_html( $year ); ?>
                  </span>
                </li>
              <?php endif; ?>
              <?php if ( function_exists( 'get_field' ) && ( $url = get_field( 'project_url' ) ) ) : ?>
                <li style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                  <strong style="display: block; margin-bottom: 0.5rem; color: var(--color-text-secondary); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                    <?php esc_html_e( 'Live Site', 'auragrid-lite' ); ?>
                  </strong>
                  <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" style="display: inline-block; font-size: 1.1rem; color: var(--color-primary); text-decoration: none;">
                    <?php echo esc_html( preg_replace( '#^https?://#', '', $url ) ); ?>
                  </a>
                </li>
              <?php endif; ?>
              <?php
              // Display post tags as term pills
              $tags = get_the_tags();
              if ( $tags ) :
                ?>
                <li>
                  <strong style="display: block; margin-bottom: 0.5rem; color: var(--color-text-secondary); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                    <?php esc_html_e( 'Services & Keywords', 'auragrid-lite' ); ?>
                  </strong>
                  <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <?php foreach ( $tags as $tag ) : ?>
                      <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="term-pill" style="display: inline-block; background: var(--glass-bg-light); color: var(--color-text-primary); padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.8rem; text-decoration: none;">
                        <?php echo esc_html( $tag->name ); ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </aside>
      </div>
    </div>
    <!-- Spacing before footer -->
    <div style="height: 60px;"></div>
  <?php endwhile; ?>
</main>

<?php get_footer();