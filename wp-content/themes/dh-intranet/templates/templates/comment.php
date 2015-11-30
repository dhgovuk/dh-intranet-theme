<?php

if (!isset($GLOBALS['comment_count'])) {
    $GLOBALS['comment_count'] = 0;
} else {
    ++$GLOBALS['comment_count'];
}
?>

<div class="comment-block">
    <?php if ((int)$comment->comment_parent === 0 && $GLOBALS['comment_count'] !== 0) {
        echo '<hr>';
    }?>
    <div class="comment-avatar">
        <?php echo get_avatar($comment, $size = '64'); ?>
    </div>
    <div class="comment-body">

        <?php edit_comment_link(__('(Edit)', 'roots'), '', ''); ?>

        <?php if ((string)$comment->comment_approved === '0') : ?>
            <div class="alert">
                <?php _e('Your comment is awaiting moderation.', 'roots'); ?>
            </div>
        <?php endif; ?>

        <?php comment_text(); ?>
        <div class="comment-meta">

            <div class="comment-author">
                <?php wtg_comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'])), null, null, $comment->comment_author); ?>
            </div>

            <time datetime="<?php echo comment_date('c'); ?>"><?php echo comment_date('d F Y'); ?></time>
        </div>
    </div>
</div>
