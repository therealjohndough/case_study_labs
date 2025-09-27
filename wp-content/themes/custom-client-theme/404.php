<?php
/**
 * 404 Error Template
 *
 * @package CustomClientTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <section class="error-404 not-found">
            <div class="error-content card text-center">
                <div class="glitch-404">
                    <h1 class="error-title font-pixel">404</h1>
                    <div class="glitch-layers">
                        <span class="glitch-layer">404</span>
                        <span class="glitch-layer">404</span>
                        <span class="glitch-layer">404</span>
                    </div>
                </div>
                
                <h2 class="error-subtitle"><?php _e('Page Not Found', 'custom-client-theme'); ?></h2>
                
                <p class="error-description">
                    <?php _e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'custom-client-theme'); ?>
                </p>
                
                <div class="error-actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg">
                        <?php _e('Return Home', 'custom-client-theme'); ?>
                    </a>
                    
                    <button id="search-toggle" class="btn btn-secondary btn-lg">
                        <?php _e('Search', 'custom-client-theme'); ?>
                    </button>
                </div>
                
                <div id="error-search" class="error-search" style="display: none;">
                    <?php get_search_form(); ?>
                </div>
            </div>
            
            <?php
            // Show recent posts
            $recent_posts = new WP_Query(array(
                'posts_per_page' => 3,
                'post_status' => 'publish',
            ));
            
            if ($recent_posts->have_posts()) :
                ?>
                <div class="recent-posts-section">
                    <h3 class="section-title text-center"><?php _e('Recent Posts', 'custom-client-theme'); ?></h3>
                    <div class="posts-grid">
                        <?php
                        while ($recent_posts->have_posts()) :
                            $recent_posts->the_post();
                            ?>
                            <article class="post-card card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-content">
                                    <h4 class="post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    <p class="post-excerpt"><?php echo custom_excerpt(15); ?></p>
                                </div>
                            </article>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
                <?php
            endif;
            ?>
        </section>
    </div>
</main>

<style>
/* 404 Page Styles */
.error-404 {
    padding: 4rem 0;
}

.error-content {
    max-width: 600px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.glitch-404 {
    position: relative;
    margin-bottom: 2rem;
}

.error-title {
    font-size: 6rem;
    color: var(--primary);
    margin: 0;
    position: relative;
    z-index: 2;
}

.glitch-layers {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    height: 100%;
}

.glitch-layer {
    position: absolute;
    top: 0;
    left: 0;
    font-size: 6rem;
    font-family: 'Press Start 2P', monospace;
    width: 100%;
    text-align: center;
    z-index: 1;
    animation: glitch-animation 2s linear infinite;
}

.glitch-layer:nth-child(1) {
    color: #ff0000;
    animation-delay: 0.1s;
    clip: rect(0, 900px, 0, 0);
}

.glitch-layer:nth-child(2) {
    color: #00ff00;
    animation-delay: 0.2s;
    clip: rect(0, 900px, 0, 0);
}

.glitch-layer:nth-child(3) {
    color: #0000ff;
    animation-delay: 0.3s;
    clip: rect(0, 900px, 0, 0);
}

@keyframes glitch-animation {
    0% {
        clip: rect(64px, 9999px, 66px, 0);
        transform: translateX(-50%) skew(0.85deg);
    }
    5% {
        clip: rect(30px, 9999px, 36px, 0);
        transform: translateX(-50%) skew(0.4deg);
    }
    10% {
        clip: rect(70px, 9999px, 71px, 0);
        transform: translateX(-50%) skew(0.2deg);
    }
    15% {
        clip: rect(10px, 9999px, 15px, 0);
        transform: translateX(-50%) skew(0.1deg);
    }
    20% {
        clip: rect(40px, 9999px, 45px, 0);
        transform: translateX(-50%) skew(0.3deg);
    }
    25% {
        clip: rect(90px, 9999px, 95px, 0);
        transform: translateX(-50%) skew(0.5deg);
    }
    30% {
        clip: rect(20px, 9999px, 25px, 0);
        transform: translateX(-50%) skew(0.2deg);
    }
    35% {
        clip: rect(80px, 9999px, 85px, 0);
        transform: translateX(-50%) skew(0.4deg);
    }
    40% {
        clip: rect(50px, 9999px, 55px, 0);
        transform: translateX(-50%) skew(0.1deg);
    }
    45% {
        clip: rect(60px, 9999px, 65px, 0);
        transform: translateX(-50%) skew(0.3deg);
    }
    50% {
        clip: rect(35px, 9999px, 40px, 0);
        transform: translateX(-50%) skew(0.2deg);
    }
    55% {
        clip: rect(75px, 9999px, 80px, 0);
        transform: translateX(-50%) skew(0.4deg);
    }
    60% {
        clip: rect(25px, 9999px, 30px, 0);
        transform: translateX(-50%) skew(0.1deg);
    }
    65% {
        clip: rect(85px, 9999px, 90px, 0);
        transform: translateX(-50%) skew(0.3deg);
    }
    70% {
        clip: rect(45px, 9999px, 50px, 0);
        transform: translateX(-50%) skew(0.2deg);
    }
    75% {
        clip: rect(15px, 9999px, 20px, 0);
        transform: translateX(-50%) skew(0.5deg);
    }
    80% {
        clip: rect(65px, 9999px, 70px, 0);
        transform: translateX(-50%) skew(0.1deg);
    }
    85% {
        clip: rect(55px, 9999px, 60px, 0);
        transform: translateX(-50%) skew(0.3deg);
    }
    90% {
        clip: rect(5px, 9999px, 10px, 0);
        transform: translateX(-50%) skew(0.2deg);
    }
    95% {
        clip: rect(95px, 9999px, 100px, 0);
        transform: translateX(-50%) skew(0.4deg);
    }
    100% {
        clip: rect(64px, 9999px, 66px, 0);
        transform: translateX(-50%) skew(0.85deg);
    }
}

.error-subtitle {
    font-size: 1.5rem;
    color: var(--accent);
    margin-bottom: 1rem;
}

.error-description {
    font-size: 1.1rem;
    color: var(--muted-foreground);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 2rem;
}

.error-search {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border);
}

.recent-posts-section {
    margin-top: 4rem;
}

.section-title {
    color: var(--primary);
    margin-bottom: 2rem;
    font-size: 1.5rem;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.post-card {
    transition: transform 0.3s ease;
}

.post-card:hover {
    transform: translateY(-5px);
}

.post-thumbnail img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: var(--radius);
}

.post-content {
    padding: 1rem 0;
}

.post-title {
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.post-title a {
    color: var(--foreground);
    text-decoration: none;
}

.post-title a:hover {
    color: var(--primary);
}

.post-excerpt {
    font-size: 0.9rem;
    color: var(--muted-foreground);
    line-height: 1.5;
}

@media (max-width: 768px) {
    .error-title,
    .glitch-layer {
        font-size: 4rem;
    }
    
    .error-content {
        padding: 2rem 1rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.getElementById('search-toggle');
    const searchForm = document.getElementById('error-search');
    
    if (searchToggle && searchForm) {
        searchToggle.addEventListener('click', function() {
            const isVisible = searchForm.style.display !== 'none';
            searchForm.style.display = isVisible ? 'none' : 'block';
            searchToggle.textContent = isVisible ? 'Search' : 'Hide Search';
        });
    }
});
</script>

<?php get_footer(); ?>