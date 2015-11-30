<div class="meta">
    <time class="published" datetime="<?php echo comment_date('c'); ?>">Published on <?php echo get_the_time('d F Y'); ?></time>

    <p class="author"><?php echo __('By', 'roots'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author"><?php echo get_the_author_meta('first_name').' '.get_the_author_meta('last_name'); ?></a></p>
</div>
