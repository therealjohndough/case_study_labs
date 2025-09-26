<?php
/**
 * Template Name: Service Landing Page
 * Description: A reusable landing page template for individual services. It
 * leverages Advanced Custom Fields (ACF) fields to populate the hero,
 * offerings list, process content, FAQs and final call‑to‑action. This
 * template mirrors the original site’s structure while dropping heavy
 * dependencies. Animated backgrounds are handled purely via CSS (see
 * style.css) and the slider is implemented in main.js.
 *
 * @package auragrid-lite
 */

get_header();
?>

<main id="main-content" class="site-main">
  <?php
  // Pull ACF fields with fallbacks
  $hero_title     = function_exists( 'get_field' ) ? get_field( 'service_hero_title' ) : '';
  $hero_subtitle  = function_exists( 'get_field' ) ? get_field( 'service_hero_subtitle' ) : '';
  $hero_intro     = function_exists( 'get_field' ) ? get_field( 'service_hero_intro' ) : '';
  $offerings_heading = function_exists( 'get_field' ) ? get_field( 'offerings_heading' ) : '';
  $detailed_process  = function_exists( 'get_field' ) ? get_field( 'service_detailed_process' ) : '';
  $cta_heading    = function_exists( 'get_field' ) ? get_field( 'cta_heading' ) : '';
  $cta_subheading = function_exists( 'get_field' ) ? get_field( 'cta_subheading' ) : '';
  $cta_button_text= function_exists( 'get_field' ) ? get_field( 'cta_button_text' ) : '';
  $cta_button_link= function_exists( 'get_field' ) ? get_field( 'cta_button_link' ) : '';
  ?>
  <!-- Hero -->
  <section class="hero" style="min-height: 60vh; display:flex; align-items:center; justify-content:center; text-align:center; padding-top: calc(var(--spacing-section));">
    <div class="container anim-reveal">
      <?php if ( $hero_subtitle ) : ?>
        <p class="h4" style="color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
          <?php echo esc_html( $hero_subtitle ); ?>
        </p>
      <?php endif; ?>
      <h1 class="headline" data-text="<?php echo esc_attr( $hero_title ?: get_the_title() ); ?>">
        <?php echo esc_html( $hero_title ?: get_the_title() ); ?>
      </h1>
      <?php if ( $hero_intro ) : ?>
        <p class="hero-intro" style="margin-top: 1rem; color: var(--color-text-secondary); max-width: 60ch; margin-inline:auto;">
          <?php echo esc_html( $hero_intro ); ?>
        </p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Offerings -->
  <section class="container" style="margin-top: var(--spacing-section);">
    <?php if ( $offerings_heading ) : ?>
      <h2 class="section-heading anim-reveal text-center">
        <?php echo esc_html( $offerings_heading ); ?>
      </h2>
    <?php endif; ?>
    <?php if ( have_rows( 'service_offerings' ) ) : ?>
      <div class="services-grid" style="margin-top: 4rem;">
        <?php $i = 0; while ( have_rows( 'service_offerings' ) ) : the_row(); $i++; ?>
          <div class="service-category offer-item anim-reveal" style="--stagger-index: <?php echo esc_attr( $i ); ?>;">
            <div class="service-header">
              <h3 class="service-title">
                <?php echo esc_html( get_sub_field( 'offering_title' ) ); ?>
              </h3>
            </div>
            <p class="service-text">
              <?php echo esc_html( get_sub_field( 'offering_description' ) ); ?>
            </p>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Detailed Process -->
  <?php if ( $detailed_process ) : ?>
    <section class="container-narrow" style="margin-top: var(--spacing-section);">
      <div class="glass-panel anim-reveal">
        <div class="content-wrapper">
          <?php echo wp_kses_post( $detailed_process ); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- FAQ Accordion -->
  <?php if ( have_rows( 'service_faqs' ) ) : ?>
    <section class="container-narrow" style="margin-top: var(--spacing-section);">
      <h2 class="section-heading anim-reveal text-center">
        <?php esc_html_e( 'Frequently Asked Questions', 'auragrid-lite' ); ?>
      </h2>
      <div class="faq-accordion anim-reveal" style="margin-top: 3rem;">
        <?php while ( have_rows( 'service_faqs' ) ) : the_row(); ?>
          <details class="faq-item">
            <summary class="faq-question">
              <?php echo esc_html( get_sub_field( 'faq_question' ) ); ?>
            </summary>
            <div class="faq-answer content-wrapper">
              <p><?php echo esc_html( get_sub_field( 'faq_answer' ) ); ?></p>
            </div>
          </details>
        <?php endwhile; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- Final CTA -->
  <?php if ( $cta_heading || $cta_button_text ) : ?>
    <section class="container-narrow" style="margin-top: var(--spacing-section);">
      <div class="glass-panel text-center anim-reveal glow-primary">
        <?php if ( $cta_heading ) : ?>
          <h2 class="h3 mt-0 mb-1">
            <?php echo esc_html( $cta_heading ); ?>
          </h2>
        <?php endif; ?>
        <?php if ( $cta_subheading ) : ?>
          <p class="mb-2" style="color: var(--color-text-secondary);">
            <?php echo esc_html( $cta_subheading ); ?>
          </p>
        <?php endif; ?>
        <?php if ( $cta_button_text ) : ?>
          <a href="<?php echo esc_url( $cta_button_link ?: home_url( '/contact' ) ); ?>" class="btn" style="margin-bottom: 1rem;">
            <?php echo esc_html( $cta_button_text ); ?>
          </a>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php get_footer();