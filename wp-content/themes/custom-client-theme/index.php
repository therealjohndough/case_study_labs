<?php
/**
 * Main Template File
 *
 * @package CustomClientTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
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
                                    <?php the_post_thumbnail('medium_large'); ?>
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
                                <?php
                                if (is_home() || is_archive()) {
                                    echo '<p>' . custom_excerpt(25) . '</p>';
                                } else {
                                    the_content();
                                }
                                ?>
                            </div>
                            
                            <?php if (is_home() || is_archive()) : ?>
                                <footer class="entry-footer">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                        <?php _e('Read More', 'custom-client-theme'); ?>
                                    </a>
                                </footer>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>
            
            <?php
            // Pagination
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('← Previous', 'custom-client-theme'),
                'next_text' => __('Next →', 'custom-client-theme'),
            ));
            
        else :
            ?>
            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php _e('Nothing here', 'custom-client-theme'); ?></h1>
                </header>
                
                <div class="page-content">
                    <?php if (is_home() && current_user_can('publish_posts')) : ?>
                        <p><?php
                            printf(
                                wp_kses(
                                    __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'custom-client-theme'),
                                    array('a' => array('href' => array()))
                                ),
                                esc_url(admin_url('post-new.php'))
                            );
                        ?></p>
                    <?php elseif (is_search()) : ?>
                        <p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'custom-client-theme'); ?></p>
                        <?php get_search_form(); ?>
                    <?php else : ?>
                        <p><?php _e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'custom-client-theme'); ?></p>
                        <?php get_search_form(); ?>
                    <?php endif; ?>
                </div>
            </section>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();