<?php
/**
 * Page Template
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
            <article id="page-<?php the_ID(); ?>" <?php post_class('single-page vhs-glitch'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
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

                <?php
                // Frontend editing for logged-in users
                if (current_user_can('edit_posts') && function_exists('acf_form')) :
                    $field_groups = array();
                    
                    // Check if this is the homepage
                    if (is_front_page()) {
                        $field_groups[] = 'group_homepage_content';
                    }
                    
                    if (!empty($field_groups)) :
                        ?>
                        <section class="frontend-editing-section">
                            <div class="editing-toggle">
                                <button id="toggle-editing" class="btn btn-secondary">
                                    <?php _e('Edit Page Content', 'custom-client-theme'); ?>
                                </button>
                            </div>
                            
                            <div id="frontend-editing-form" class="frontend-form" style="display: none;">
                                <h2><?php _e('Edit Page Content', 'custom-client-theme'); ?></h2>
                                <?php
                                acf_form(array(
                                    'post_id' => get_the_ID(),
                                    'field_groups' => $field_groups,
                                    'submit_value' => __('Update Page', 'custom-client-theme'),
                                    'updated_message' => __('Page updated successfully!', 'custom-client-theme'),
                                ));
                                ?>
                            </div>
                        </section>
                        <?php
                    endif;
                endif;
                ?>

                <footer class="entry-footer">
                    <?php if (current_user_can('edit_page', get_the_ID())) : ?>
                        <div class="edit-link">
                            <?php
                            edit_post_link(
                                __('Edit Page', 'custom-client-theme'),
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
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

        endwhile;
        ?>
    </div>
</main>

<style>
/* Page Styles */
.single-page {
    max-width: 900px;
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
    margin-bottom: 0;
    color: var(--primary);
    line-height: 1.2;
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

.frontend-editing-section {
    background: var(--muted);
    padding: 2rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 2rem;
}

.editing-toggle {
    text-align: center;
    margin-bottom: 1rem;
}

.frontend-form {
    max-width: 100%;
}

.entry-footer {
    border-top: 1px solid var(--border);
    padding-top: 2rem;
    text-align: center;
}

@media (max-width: 768px) {
    .single-page {
        margin: 1rem auto;
    }
    
    .entry-title {
        font-size: 2rem;
    }
    
    .entry-content {
        padding: 1rem;
    }
    
    .frontend-editing-section {
        padding: 1rem;
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
                '<?php _e('Edit Page Content', 'custom-client-theme'); ?>' : 
                '<?php _e('Hide Editor', 'custom-client-theme'); ?>';
        });
    }
});
</script>

<?php get_footer(); ?>