<?php

function wtg_comment_reply_link($args = array(), $comment = null, $post = null, $reply_to = null)
{
    $comment_reply_link = wtg_get_comment_reply_link($args, $comment, $post, $reply_to);

    if (empty($comment_reply_link)) {
        $comment_reply_link = '<div class="wtg-comment-no-reply">'.esc_html($reply_to).'</div>';
    }

    echo $comment_reply_link;
}

function wtg_get_comment_reply_link($args = array(), $comment = null, $post = null, $reply_to = null)
{
    $defaults = array(
        'add_below' => 'comment',
        'respond_id' => 'respond',
        'reply_text' => __('Reply to '.$reply_to),
        'login_text' => __('Log in to reply to '.$reply_to),
        'depth' => 0,
        'before' => '',
        'after' => '',
    );

    $args = wp_parse_args($args, $defaults);

    if (0 === $args['depth'] || $args['max_depth'] <= $args['depth']) {
        return;
    }

    $comment = get_comment($comment);
    if (empty($post)) {
        $post = $comment->comment_post_ID;
    }
    $post = get_post($post);

    if (!comments_open($post->ID)) {
        return false;
    }

    $link = '';

    if (get_option('comment_registration') && !is_user_logged_in()) {
        $link = '<a rel="nofollow" class="wtg-comment-reply-login" href="'.esc_url(wp_login_url(get_permalink())).'">'.$args['login_text'].'</a>';
    } else {
        $link = "<a class='wtg-comment-reply-link' href='".esc_url(add_query_arg('replytocom', $comment->comment_ID)).'#'.esc_attr($args['respond_id'])."' onclick='return addComment.moveForm(\"".absint($args['add_below']-$comment->comment_ID)."\", \"".absint($comment->comment_ID)."\", \"".absint($args['respond_id'])."\", \"".absint($post->ID)."\")'>".esc_html($args['reply_text'])."</a>";
    }

    /*
    * Filter the comment reply link.
    *
    * @since 2.7.0
    *
    * @param string  $link    The HTML markup for the comment reply link.
    * @param array   $args    An array of arguments overriding the defaults.
    * @param object  $comment The object of the comment being replied.
    * @param WP_Post $post    The WP_Post object.
    */
    return apply_filters('comment_reply_link', $args['before'].$link.$args['after'], $args, $comment, $post);
}
