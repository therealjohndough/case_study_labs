<?php
/**
 * Template Name: Custom Homepage
 * 
 * @package CustomClientTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <?php
    // Hero Section
    $hero_title = get_theme_field('hero_title', get_the_ID(), 'Welcome to Our Site');
    $hero_subtitle = get_theme_field('hero_subtitle', get_the_ID(), 'Discover amazing content and experiences.');
    $hero_image = get_theme_field('hero_image', get_the_ID());
    $hero_cta_text = get_theme_field('hero_cta_text', get_the_ID(), 'Get Started');
    $hero_cta_url = get_theme_field('hero_cta_url', get_the_ID(), '#');
    ?>
    
    <section class="hero-section vhs-glitch star-pattern" 
             <?php if ($hero_image) echo 'style="background-image: url(' . esc_url($hero_image['url']) . ');"'; ?>>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="hero-title font-pixel"><?php echo esc_html($hero_title); ?></h1>
                <p class="hero-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
                <?php if ($hero_cta_url) : ?>
                    <a href="<?php echo esc_url($hero_cta_url); ?>" class="btn btn-primary btn-lg hero-cta">
                        <?php echo esc_html($hero_cta_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php
    // Content Sections
    $content_sections = get_theme_field('content_sections', get_the_ID());
    if ($content_sections) :
        ?>
        <section class="content-sections">
            <div class="container">
                <?php foreach ($content_sections as $index => $section) : ?>
                    <div class="content-section <?php echo ($index % 2 === 0) ? 'section-left' : 'section-right'; ?>">
                        <div class="section-inner card">
                            <?php if (!empty($section['image'])) : ?>
                                <div class="section-image">
                                    <img src="<?php echo esc_url($section['image']['url']); ?>" 
                                         alt="<?php echo esc_attr($section['image']['alt']); ?>"
                                         class="section-img">
                                </div>
                            <?php endif; ?>
                            
                            <div class="section-content">
                                <?php if (!empty($section['title'])) : ?>
                                    <h2 class="section-title"><?php echo esc_html($section['title']); ?></h2>
                                <?php endif; ?>
                                
                                <?php if (!empty($section['content'])) : ?>
                                    <div class="section-text">
                                        <?php echo wp_kses_post($section['content']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    endif;
    ?>

    <?php
    // Frontend Editing Form for Logged-in Users
    if (current_user_can('edit_posts') && function_exists('acf_form')) :
        ?>
        <section class="frontend-editing-section">
            <div class="container">
                <div class="editing-toggle">
                    <button id="toggle-editing" class="btn btn-secondary">
                        <?php _e('Edit Homepage Content', 'custom-client-theme'); ?>
                    </button>
                </div>
                
                <div id="frontend-editing-form" class="frontend-form" style="display: none;">
                    <h2><?php _e('Edit Homepage Content', 'custom-client-theme'); ?></h2>
                    <?php
                    acf_form(array(
                        'post_id' => get_the_ID(),
                        'field_groups' => array('group_homepage_content'),
                        'submit_value' => __('Update Homepage', 'custom-client-theme'),
                        'updated_message' => __('Homepage updated successfully!', 'custom-client-theme'),
                    ));
                    ?>
                </div>
            </div>
        </section>
        <?php
    endif;
    ?>

    <?php
    // Plugin Integration Areas
    ?>
    <section class="plugin-integrations">
        <div class="container">
            <?php
            // Brevo Email Marketing Integration
            if (function_exists('brevo_form')) :
                ?>
                <div class="integration-section brevo-section card">
                    <h2><?php _e('Stay Connected', 'custom-client-theme'); ?></h2>
                    <p><?php _e('Subscribe to our newsletter for updates and exclusive content.', 'custom-client-theme'); ?></p>
                    <?php
                    // Brevo form will be integrated here once plugin is active
                    echo '<div class="brevo-form-placeholder">';
                    echo '<p><em>' . __('Brevo email form will appear here once the plugin is configured.', 'custom-client-theme') . '</em></p>';
                    echo '</div>';
                    ?>
                </div>
                <?php
            endif;
            
            // Agile Store Locator Integration
            if (function_exists('asl_store_locator')) :
                ?>
                <div class="integration-section store-locator-section card">
                    <h2><?php _e('Find Our Locations', 'custom-client-theme'); ?></h2>
                    <p><?php _e('Discover our stores and services near you.', 'custom-client-theme'); ?></p>
                    <?php
                    // Store locator will be integrated here once plugin is active
                    echo '<div class="store-locator-placeholder">';
                    echo '<p><em>' . __('Agile Store Locator will appear here once the plugin is configured.', 'custom-client-theme') . '</em></p>';
                    echo '</div>';
                    ?>
                </div>
                <?php
            endif;
            ?>
        </div>
    </section>

    <?php
    // Recent Posts Section
    $recent_posts = new WP_Query(array(
        'posts_per_page' => 3,
        'post_status' => 'publish',
    ));
    
    if ($recent_posts->have_posts()) :
        ?>
        <section class="recent-posts-section">
            <div class="container">
                <h2 class="section-title text-center"><?php _e('Latest Updates', 'custom-client-theme'); ?></h2>
                <div class="posts-grid">
                    <?php
                    while ($recent_posts->have_posts()) :
                        $recent_posts->the_post();
                        ?>
                        <article class="post-card card vhs-glitch">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="post-excerpt"><?php echo custom_excerpt(15); ?></p>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <?php _e('Read More', 'custom-client-theme'); ?>
                                </a>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </section>
        <?php
    endif;
    ?>
</main>

<style>
/* Homepage Specific Styles */
.hero-section {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        45deg,
        rgba(10, 10, 10, 0.8),
        rgba(241, 91, 39, 0.1)
    );
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
}

.hero-title {
    font-size: 3rem;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    animation: pulse 2s ease-in-out infinite alternate;
}

.hero-subtitle {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.hero-cta {
    font-size: 1.1rem;
    padding: 1rem 2rem;
    box-shadow: 0 0 30px rgba(241, 91, 39, 0.3);
}

.content-sections {
    padding: 4rem 0;
}

.content-section {
    margin-bottom: 3rem;
}

.content-section .section-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: center;
}

.section-right .section-inner {
    grid-template-columns: 1fr 1fr;
}

.section-right .section-image {
    order: 2;
}

.section-right .section-content {
    order: 1;
}

.section-image img {
    width: 100%;
    height: auto;
    border-radius: var(--radius);
}

.section-title {
    margin-bottom: 1rem;
}

.frontend-editing-section {
    background: var(--muted);
    padding: 2rem 0;
    border-top: 1px solid var(--border);
}

.editing-toggle {
    text-align: center;
    margin-bottom: 2rem;
}

.frontend-form {
    max-width: 800px;
    margin: 0 auto;
}

.plugin-integrations {
    padding: 4rem 0;
    background: var(--secondary);
}

.integration-section {
    margin-bottom: 2rem;
    text-align: center;
}

.recent-posts-section {
    padding: 4rem 0;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.post-card {
    transition: transform 0.3s ease;
}

.post-card:hover {
    transform: translateY(-5px);
}

.post-thumbnail img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: var(--radius);
}

.post-content {
    padding: 1rem 0;
}

.post-title a {
    color: var(--foreground);
    text-decoration: none;
}

.post-title a:hover {
    color: var(--primary);
}

@keyframes pulse {
    from { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); }
    to { text-shadow: 2px 2px 20px rgba(241, 91, 39, 0.8); }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .content-section .section-inner {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .section-right .section-image {
        order: 1;
    }
    
    .section-right .section-content {
        order: 2;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle frontend editing form
    const toggleBtn = document.getElementById('toggle-editing');
    const editingForm = document.getElementById('frontend-editing-form');
    
    if (toggleBtn && editingForm) {
        toggleBtn.addEventListener('click', function() {
            const isVisible = editingForm.style.display !== 'none';
            editingForm.style.display = isVisible ? 'none' : 'block';
            toggleBtn.textContent = isVisible ? 
                '<?php _e('Edit Homepage Content', 'custom-client-theme'); ?>' : 
                '<?php _e('Hide Editor', 'custom-client-theme'); ?>';
        });
    }
    
    // Add smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php get_footer(); ?>