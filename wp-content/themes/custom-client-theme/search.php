<?php
/**
 * Search Results Template
 *
 * @package CustomClientTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                printf(
                    esc_html__('Search Results for: %s', 'custom-client-theme'),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            
            <div class="search-form-container">
                <?php get_search_form(); ?>
            </div>
        </header>

        <?php
        if (have_posts()) :
            ?>
            <div class="search-results">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('search-result card'); ?>>
                        <div class="result-content">
                            <header class="entry-header">
                                <h2 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="entry-meta">
                                    <span class="post-type">
                                        <?php 
                                        $post_type = get_post_type();
                                        echo ucfirst($post_type);
                                        ?>
                                    </span>
                                    <span class="posted-on">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <?php if ($post_type === 'post') : ?>
                                        <span class="byline">
                                            by <?php the_author(); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </header>
                            
                            <div class="entry-summary">
                                <?php 
                                $excerpt = get_the_excerpt();
                                if ($excerpt) {
                                    echo '<p>' . $excerpt . '</p>';
                                } else {
                                    echo '<p>' . custom_excerpt(30) . '</p>';
                                }
                                ?>
                            </div>
                            
                            <footer class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <?php _e('View', 'custom-client-theme'); ?>
                                </a>
                            </footer>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>
            
            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('← Previous', 'custom-client-theme'),
                'next_text' => __('Next →', 'custom-client-theme'),
            ));
            
        else :
            ?>
            <section class="no-results not-found">
                <header class="page-header">
                    <h2 class="page-title"><?php _e('Nothing Found', 'custom-client-theme'); ?></h2>
                </header>
                
                <div class="page-content">
                    <p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'custom-client-theme'); ?></p>
                    
                    <div class="search-suggestions">
                        <h3><?php _e('Search Suggestions:', 'custom-client-theme'); ?></h3>
                        <ul>
                            <li><?php _e('Make sure all words are spelled correctly', 'custom-client-theme'); ?></li>
                            <li><?php _e('Try different keywords', 'custom-client-theme'); ?></li>
                            <li><?php _e('Try more general keywords', 'custom-client-theme'); ?></li>
                            <li><?php _e('Try fewer keywords', 'custom-client-theme'); ?></li>
                        </ul>
                    </div>
                </div>
            </section>
            <?php
        endif;
        ?>
    </div>
</main>

<style>
.search-form-container {
    max-width: 500px;
    margin: 2rem auto 0;
}

.search-results {
    margin-top: 3rem;
}

.search-result {
    margin-bottom: 2rem;
    padding: 2rem;
}

.search-result:hover {
    transform: translateY(-2px);
}

.entry-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    font-size: 0.85rem;
    color: var(--muted-foreground);
    margin-bottom: 1rem;
}

.post-type {
    background: var(--primary);
    color: var(--primary-foreground);
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius);
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 500;
}

.entry-summary {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.search-suggestions {
    margin-top: 2rem;
    padding: 2rem;
    background: var(--muted);
    border: 1px solid var(--border);
    border-radius: var(--radius);
}

.search-suggestions h3 {
    color: var(--accent);
    margin-bottom: 1rem;
}

.search-suggestions ul {
    padding-left: 1.5rem;
}

.search-suggestions li {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .entry-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .search-result {
        padding: 1rem;
    }
}
</style>

<?php get_footer(); ?>