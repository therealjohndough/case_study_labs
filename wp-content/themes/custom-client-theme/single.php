<?php
/**
 * Single Post Template
 *
 * @package CustomClientTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post vhs-glitch'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    
                    <div class="entry-meta">
                        <span class="posted-on">
                            <time datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </span>
                        <span class="byline">
                            <?php _e('by', 'custom-client-theme'); ?> 
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                <?php the_author(); ?>
                            </a>
                        </span>
                        <?php if (has_category()) : ?>
                            <span class="cat-links">
                                <?php _e('in', 'custom-client-theme'); ?> 
                                <?php the_category(', '); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content card">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('Pages:', 'custom-client-theme'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="entry-footer">
                    <?php if (has_tag()) : ?>
                        <div class="tags-links">
                            <strong><?php _e('Tags:', 'custom-client-theme'); ?></strong>
                            <?php the_tags('', ', '); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (current_user_can('edit_post', get_the_ID())) : ?>
                        <div class="edit-link">
                            <?php
                            edit_post_link(
                                __('Edit Post', 'custom-client-theme'),
                                '<span class="edit-link">',
                                '</span>',
                                null,
                                'btn btn-secondary btn-sm'
                            );
                            ?>
                        </div>
                    <?php endif; ?>
                </footer>
            </article>

            <?php
            // Navigation between posts
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . __('Previous:', 'custom-client-theme') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . __('Next:', 'custom-client-theme') . '</span> <span class="nav-title">%title</span>',
            ));

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

        endwhile;
        ?>
    </div>
</main>

<style>
/* Single Post Styles */
.single-post {
    max-width: 800px;
    margin: 2rem auto;
}

.entry-header {
    margin-bottom: 2rem;
    text-align: center;
    border-bottom: 1px solid var(--border);
    padding-bottom: 2rem;
}

.entry-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--primary);
    line-height: 1.2;
}

.entry-meta {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    font-size: 0.9rem;
    color: var(--muted-foreground);
}

.entry-meta a {
    color: var(--primary);
    text-decoration: none;
}

.entry-meta a:hover {
    color: var(--accent);
}

.entry-featured-image {
    margin-bottom: 2rem;
    text-align: center;
}

.entry-featured-image img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: cover;
    border-radius: var(--radius);
}

.entry-content {
    margin-bottom: 2rem;
    padding: 2rem;
}

.entry-content h2,
.entry-content h3,
.entry-content h4 {
    color: var(--accent);
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.entry-content p {
    margin-bottom: 1.5rem;
    line-height: 1.7;
}

.entry-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius);
    margin: 1rem 0;
}

.entry-content blockquote {
    border-left: 3px solid var(--primary);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    background: var(--muted);
    padding: 1rem 1.5rem;
    border-radius: var(--radius);
}

.entry-content ul,
.entry-content ol {
    padding-left: 2rem;
    margin-bottom: 1.5rem;
}

.entry-content li {
    margin-bottom: 0.5rem;
}

.page-links {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border);
    text-align: center;
}

.page-links a {
    display: inline-block;
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-decoration: none;
    transition: all 0.3s ease;
}

.page-links a:hover {
    border-color: var(--primary);
    background: var(--primary);
    color: var(--primary-foreground);
}

.entry-footer {
    border-top: 1px solid var(--border);
    padding-top: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.tags-links {
    flex: 1;
}

.tags-links a {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    margin: 0 0.25rem 0.5rem 0;
    background: var(--muted);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.85rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.tags-links a:hover {
    border-color: var(--primary);
    background: var(--primary);
    color: var(--primary-foreground);
}

.post-navigation {
    margin: 3rem 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}

.post-navigation .nav-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 2rem 0;
}

.post-navigation .nav-previous,
.post-navigation .nav-next {
    padding: 1rem;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    transition: all 0.3s ease;
}

.post-navigation .nav-previous:hover,
.post-navigation .nav-next:hover {
    border-color: var(--primary);
    box-shadow: 0 0 20px rgba(241, 91, 39, 0.1);
}

.post-navigation a {
    display: block;
    text-decoration: none;
    color: var(--foreground);
}

.post-navigation .nav-subtitle {
    display: block;
    font-size: 0.85rem;
    color: var(--muted-foreground);
    margin-bottom: 0.5rem;
}

.post-navigation .nav-title {
    display: block;
    font-weight: 500;
    color: var(--primary);
}

@media (max-width: 768px) {
    .single-post {
        margin: 1rem auto;
    }
    
    .entry-title {
        font-size: 2rem;
    }
    
    .entry-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .entry-content {
        padding: 1rem;
    }
    
    .entry-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .post-navigation .nav-links {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>

<?php get_footer(); ?>