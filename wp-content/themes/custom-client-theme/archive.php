<?php
/**
 * Archive Template
 *
 * @package CustomClientTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_author()) {
                    printf(__('Posts by %s', 'custom-client-theme'), get_the_author());
                } elseif (is_date()) {
                    if (is_year()) {
                        printf(__('Posts from %s', 'custom-client-theme'), get_the_date('Y'));
                    } elseif (is_month()) {
                        printf(__('Posts from %s', 'custom-client-theme'), get_the_date('F Y'));
                    } else {
                        printf(__('Posts from %s', 'custom-client-theme'), get_the_date());
                    }
                } else {
                    __('Archives', 'custom-client-theme');
                }
                ?>
            </h1>
            
            <?php if (is_category() || is_tag()) : ?>
                <div class="archive-description">
                    <?php echo term_description(); ?>
                </div>
            <?php endif; ?>
        </header>

        <?php
        if (have_posts()) :
            ?>
            <div class="posts-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card card vhs-glitch'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <header class="entry-header">
                                <h2 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="entry-meta">
                                    <span class="posted-on">
                                        <time datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                    </span>
                                    <span class="byline">
                                        by <?php the_author(); ?>
                                    </span>
                                </div>
                            </header>
                            
                            <div class="entry-content">
                                <p><?php echo custom_excerpt(20); ?></p>
                            </div>
                            
                            <footer class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <?php _e('Read More', 'custom-client-theme'); ?>
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
                    <h1 class="page-title"><?php _e('Nothing Found', 'custom-client-theme'); ?></h1>
                </header>
                
                <div class="page-content">
                    <p><?php _e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'custom-client-theme'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </section>
            <?php
        endif;
        ?>
    </div>
</main>

<style>
.page-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border);
}

.page-title {
    color: var(--primary);
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.archive-description {
    font-size: 1.1rem;
    color: var(--muted-foreground);
    max-width: 600px;
    margin: 0 auto;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>

<?php get_footer(); ?>