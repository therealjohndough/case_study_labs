<?php
/**
 * Front page template for Aura‑Grid Lite
 */

get_header();

// Utility function to conditionally render sections based on Customizer settings
$section = function( string $id, callable $callback ) {
    if ( get_theme_mod( "csl_{$id}_show", true ) ) {
        $callback();
    }
};
?>
<main id="main-content" class="site-main">
<?php
// 1. Hero section
$section( 'hero', function() {
    $headline  = get_theme_mod( 'csl_hero_headline', __( 'Made To Inspire', 'auragrid-lite' ) );
    $intro     = get_theme_mod( 'csl_hero_intro', __( 'We empower innovative brands with strategic design...', 'auragrid-lite' ) );
    $cta1_text = get_theme_mod( 'csl_hero_cta1_text', __( 'See Our Work', 'auragrid-lite' ) );
    $cta1_link = get_theme_mod( 'csl_hero_cta1_link', '#work' );
    $cta2_text = get_theme_mod( 'csl_hero_cta2_text', __( 'Start a Project', 'auragrid-lite' ) );
    $cta2_link = get_theme_mod( 'csl_hero_cta2_link', '/contact' );
    ?>
    <section class="hero container text-center">
      <h1 class="headline mb-2" data-text="<?php echo esc_attr( $headline ); ?>">
        <?php echo esc_html( $headline ); ?>
      </h1>
      <p class="hero-intro mb-2"><?php echo esc_html( $intro ); ?></p>
      <div class="hero-cta-group">
        <a href="<?php echo esc_url( $cta1_link ); ?>" class="btn"><?php echo esc_html( $cta1_text ); ?></a>
        <a href="<?php echo esc_url( $cta2_link ); ?>" class="btn btn-accent"><?php echo esc_html( $cta2_text ); ?></a>
      </div>
    </section>
<?php });

// 2. Logo grid
$section( 'logo_grid', function() {
    $heading = get_theme_mod( 'csl_logo_grid_heading', __( "Brands We've Worked With", 'auragrid-lite' ) );
    ?>
    <section class="container-narrow">
      <h2 class="section-heading text-center mb-2"><?php echo esc_html( $heading ); ?></h2>
      <div class="logo-grid">
        <?php
        // Load logos from a JSON Customizer field or from a local folder
        $logos = get_theme_mod( 'csl_logo_grid_images', [] );
        if ( ! is_array( $logos ) || empty( $logos ) ) {
            // Fallback local images in assets/images/logos (not included in this skeleton)
            $logos = [
                'logo1.png' => __( 'Client Logo', 'auragrid-lite' ),
                'logo2.png' => __( 'Client Logo', 'auragrid-lite' ),
                'logo3.png' => __( 'Client Logo', 'auragrid-lite' ),
            ];
        }
        foreach ( $logos as $file => $alt ) {
            $src = is_array( $logos ) ? $file : '';
            if ( ! empty( $src ) ) {
                echo '<img src="' . esc_url( get_template_directory_uri() . '/assets/images/logos/' . $file ) . '" alt="' . esc_attr( $alt ) . '">';
            }
        }
        ?>
      </div>
    </section>
<?php });

// 3. Work (Case studies)
$section( 'work', function() {
    $heading    = get_theme_mod( 'csl_work_heading', __( 'Case Studies', 'auragrid-lite' ) );
    $subheading = get_theme_mod( 'csl_work_subheading', __( 'We design brands and platforms...', 'auragrid-lite' ) );
    $selected   = get_theme_mod( 'csl_work_featured_ids', [] );
    if ( ! is_array( $selected ) ) {
        $selected = array_filter( array_map( 'absint', explode( ',', $selected ) ) );
    }
    $fallback_count = absint( get_theme_mod( 'csl_work_fallback_count', 3 ) );
    $args = [
        'post_type'      => 'casestudy',
        'posts_per_page' => empty( $selected ) ? $fallback_count : -1,
        'orderby'        => empty( $selected ) ? 'date' : 'post__in',
        'order'          => 'DESC',
        'no_found_rows'  => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ];
    if ( ! empty( $selected ) ) {
        $args['post__in'] = $selected;
    }
    $q = new \WP_Query( $args );
    ?>
    <section id="work" class="container">
      <h2 class="section-heading text-center mb-2"><?php echo esc_html( $heading ); ?></h2>
      <p class="text-center mb-2"><?php echo esc_html( $subheading ); ?></p>
      <div class="project-grid">
        <?php
        $i = 0;
        if ( $q->have_posts() ) {
            while ( $q->have_posts() ) {
                $q->the_post();
                $i++;
                ?>
                <a href="<?php the_permalink(); ?>" class="project-card-link" style="--stagger-index:<?php echo esc_attr( $i ); ?>;">
                  <article class="project-card glass-realistic">
                    <div class="card-image-wrapper">
                      <?php
                      if ( has_post_thumbnail() ) {
                          the_post_thumbnail( 'large' );
                      }
                      ?>
                    </div>
                    <div class="card-content">
                      <h3 class="card-title"><?php the_title(); ?></h3>
                      <div class="card-excerpt"><?php the_excerpt(); ?></div>
                    </div>
                  </article>
                </a>
                <?php
            }
            wp_reset_postdata();
        } else {
            echo '<p class="glass-panel text-center">' . esc_html__( 'No case studies yet.', 'auragrid-lite' ) . '</p>';
        }
        ?>
      </div>
      <div class="text-center mt-3">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'casestudy' ) ); ?>" class="btn btn-glass"><?php esc_html_e( 'View All', 'auragrid-lite' ); ?></a>
      </div>
    </section>
<?php });

// Additional sections (mission, services, client fit, final CTA) can be implemented similarly
// using $section() and appropriate sanitization.

?>
</main>
<?php get_footer(); ?>
