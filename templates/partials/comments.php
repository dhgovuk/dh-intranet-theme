<?php if (comments_open()) : ?>
    <section id="respond" class="respond">
        <h2><?php wtg_comment_form_title(); ?></h2>
        <?php if (get_option('comment_registration') && !is_user_logged_in()) : ?>
            <p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'roots'), esc_attr(wp_login_url(get_permalink()))) ?></p>
        <?php else : ?>
            <form action="<?php echo esc_attr(get_option('siteurl')) ?>/wp-comments-post.php" method="post" id="commentform">
                <?php if (!is_user_logged_in()) : ?>
                    <div class="form-group">
                        <label for="author"><?php _e('Name', 'roots'); if ($req) { _e(' (required)', 'roots'); } ?></label>
                        <input type="text" class="form-control" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22" <?php if ($req) { echo 'aria-required="true"'; } ?>>
                    </div>
                    <div class="form-group">
                        <label for="email"><?php _e('Email (will not be published)', 'roots'); if ($req) { _e(' (required)', 'roots'); } ?></label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" <?php if ($req) { echo 'aria-required="true"'; } ?>>
                    </div>
                    <div class="form-group">
                        <label for="url"><?php _e('Website', 'roots'); ?></label>
                        <input type="url" class="form-control" name="url" id="url" <?php echo $comment_author_url ? "value='".esc_attr($comment_author_url)."'" : '' ?> size="22">
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label class="screenreader-only" for="comment">Comment on this page</label>
                    <textarea name="comment" id="comment" class="form-control" rows="5" aria-required="true"></textarea>
                </div>
                <div class="form-buttons"><input name="submit" class="button" type="submit" id="submit" value="<?php _e('Post your comment', 'roots'); ?>"></div>
                <?php comment_id_fields(); ?>
                <?php do_action('comment_form', $post->ID); ?>
            </form>
        <?php endif; ?>
    </section>
<?php endif; ?>
<?php
if (post_password_required()) {
    return;
}
?>
<?php if (have_comments()) : ?>
    <section id="comments" class="comments">
        <h3><?php printf(_n('Comments', '%1$s Responses to &ldquo;%2$s&rdquo;', get_comments_number(), 'roots'), esc_html(number_format_i18n(get_comments_number())), esc_html(get_the_title())) ?></h3>
        <ol class="media-list">
            <?php wp_list_comments(array('walker' => new Roots_Walker_Comment())); ?>
        </ol>
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>

            <ul class="pager">
                <?php if (get_previous_comments_link()) : ?>
                    <li class="previous"><?php previous_comments_link(__('&larr; Older comments', 'roots')); ?></li>
                <?php endif; ?>
                <?php if (get_next_comments_link()) : ?>
                    <li class="next"><?php next_comments_link(__('Newer comments &rarr;', 'roots')); ?></li>
                <?php endif; ?>
            </ul>

        <?php endif; ?>
        <?php if (!comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')) : ?>
            <div class="alert">
                <?php _e('Comments are closed.', 'roots'); ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
<?php if (!have_comments() && !comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')) : ?>
    <section id="comments" class="comments">
        <div class="alert">
            <?php _e('Comments are closed.', 'roots'); ?>
        </div>
    </section>
<?php endif; ?>
