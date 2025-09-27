<?php
/**
 * Comments Template
 *
 * @package CustomClientTheme
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area card">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ($comments_number === 1) {
                printf(_x('One comment', 'comments title', 'custom-client-theme'));
            } else {
                printf(
                    _nx(
                        '%1$s comment',
                        '%1$s comments',
                        $comments_number,
                        'comments title',
                        'custom-client-theme'
                    ),
                    number_format_i18n($comments_number)
                );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 60,
                'callback'    => 'custom_theme_comment_callback',
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation(array(
            'prev_text' => __('← Older Comments', 'custom-client-theme'),
            'next_text' => __('Newer Comments →', 'custom-client-theme'),
        ));
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php _e('Comments are closed.', 'custom-client-theme'); ?></p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply'          => __('Leave a Reply', 'custom-client-theme'),
        'title_reply_to'       => __('Leave a Reply to %s', 'custom-client-theme'),
        'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'    => '</h3>',
        'cancel_reply_before'  => ' <small>',
        'cancel_reply_after'   => '</small>',
        'cancel_reply_link'    => __('Cancel reply', 'custom-client-theme'),
        'label_submit'         => __('Post Comment', 'custom-client-theme'),
        'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s btn btn-primary" value="%4$s" />',
        'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun', 'custom-client-theme') . ' <span class="required">*</span></label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" class="form-textarea" required="required"></textarea></p>',
        'fields'               => array(
            'author' => '<p class="comment-form-author">' .
                       '<label for="author">' . __('Name', 'custom-client-theme') . ' <span class="required">*</span></label> ' .
                       '<input id="author" name="author" type="text" class="form-input" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245" required="required" /></p>',
            'email'  => '<p class="comment-form-email">' .
                       '<label for="email">' . __('Email', 'custom-client-theme') . ' <span class="required">*</span></label> ' .
                       '<input id="email" name="email" type="email" class="form-input" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes" required="required" /></p>',
            'url'    => '<p class="comment-form-url">' .
                       '<label for="url">' . __('Website', 'custom-client-theme') . '</label> ' .
                       '<input id="url" name="url" type="url" class="form-input" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" /></p>',
        ),
    ));
    ?>
</div>

<style>
/* Comments Styles */
.comments-area {
    margin-top: 3rem;
    padding: 2rem;
}

.comments-title {
    color: var(--primary);
    border-bottom: 1px solid var(--border);
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}

.comment-list {
    list-style: none;
    padding: 0;
}

.comment {
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--muted);
}

.comment .children {
    margin-top: 1rem;
    padding-left: 2rem;
    border-left: 2px solid var(--border);
}

.comment-author {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.comment-author .avatar {
    border-radius: 50%;
    margin-right: 1rem;
}

.comment-author .fn {
    color: var(--primary);
    font-weight: 500;
    text-decoration: none;
}

.comment-metadata {
    font-size: 0.85rem;
    color: var(--muted-foreground);
    margin-bottom: 1rem;
}

.comment-metadata a {
    color: var(--muted-foreground);
    text-decoration: none;
}

.comment-metadata a:hover {
    color: var(--primary);
}

.comment-content p {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.comment-reply-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    transition: all 0.3s ease;
}

.comment-reply-link:hover {
    background: var(--primary);
    color: var(--primary-foreground);
}

.comment-form {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border);
}

.comment-reply-title {
    color: var(--accent);
    margin-bottom: 1rem;
}

.comment-form p {
    margin-bottom: 1rem;
}

.comment-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--foreground);
}

.required {
    color: var(--primary);
}

.comment-form .form-input,
.comment-form .form-textarea {
    width: 100%;
    padding: 0.75rem;
    background: var(--input-background);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--foreground);
    font-family: 'Orbitron', sans-serif;
    transition: border-color 0.3s ease;
}

.comment-form .form-input:focus,
.comment-form .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(241, 91, 39, 0.2);
}

.no-comments {
    text-align: center;
    color: var(--muted-foreground);
    font-style: italic;
    padding: 2rem;
}

.comment-navigation {
    margin: 2rem 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-navigation a {
    padding: 0.5rem 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-decoration: none;
    color: var(--foreground);
    transition: all 0.3s ease;
}

.comment-navigation a:hover {
    border-color: var(--primary);
    background: var(--primary);
    color: var(--primary-foreground);
}

@media (max-width: 768px) {
    .comments-area {
        padding: 1rem;
    }
    
    .comment .children {
        padding-left: 1rem;
    }
    
    .comment-author {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .comment-author .avatar {
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
}
</style>

<?php
/**
 * Custom comment callback function
 */
function custom_theme_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
        <article class="comment-body">
            <div class="comment-author vcard">
                <?php echo get_avatar($comment, 60); ?>
                <b class="fn"><?php comment_author_link(); ?></b>
            </div>
            
            <div class="comment-metadata">
                <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                    <time datetime="<?php comment_time('c'); ?>">
                        <?php
                        printf(
                            __('%1$s at %2$s', 'custom-client-theme'),
                            get_comment_date('', $comment),
                            get_comment_time()
                        );
                        ?>
                    </time>
                </a>
                <?php edit_comment_link(__('Edit', 'custom-client-theme'), '&nbsp;&nbsp;', ''); ?>
            </div>
            
            <div class="comment-content">
                <?php if ($comment->comment_approved == '0') : ?>
                    <em class="comment-awaiting-moderation">
                        <?php _e('Your comment is awaiting moderation.', 'custom-client-theme'); ?>
                    </em>
                <?php endif; ?>
                
                <?php comment_text(); ?>
            </div>
            
            <?php
            comment_reply_link(array_merge($args, array(
                'add_below' => 'comment',
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
            )));
            ?>
        </article>
    <?php
}
?>